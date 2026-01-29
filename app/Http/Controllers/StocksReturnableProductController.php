<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\Product;
use App\Models\StocksReturnableProduct;
use App\Models\ReturnableProductsList;
use App\Models\ReturnableProduct;
use App\Models\Stock;

class StocksReturnableProductController extends Controller
{
    public function index()
    {
        $returns = StocksReturnableProduct::query()
            ->orderBy('date', 'desc')
            ->get([
                'id',
                'returnable_product_id',
                'product_id',
                'quantity_returned',
                'date',
                'comment',
                'created_at',
                'updated_at'
            ]);

        return response()->json($returns);
    }


    //enregistrer un retour 
    public function store(Request $request)
    {
        $data = $request->validate([
            'returnable_product_id' => 'required|exists:returnable_products,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_returned' => 'required|numeric|min:0.01',
            'date' => 'nullable|date',
            'comment' => 'nullable|string',
        ]);

        $date = $data['date'] ?? now()->toDateTimeString();
        $rows = [];

        DB::beginTransaction();

        try {
            // Récupérer la remise existante
            $returnable = ReturnableProduct::with('client')->findOrFail($data['returnable_product_id']);
            $clientName = $returnable->client->name ?? 'Client inconnu';

            foreach ($data['items'] as $item) {

                // Ligne produit dans returnable_products_list
                $returnableProduct = ReturnableProductsList::where(
                    'returnable_product_id',
                    $data['returnable_product_id']
                )
                    ->where('product_id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $qtyToReturn = round($item['quantity_returned'], 2);
                $qtyAvailable = $returnableProduct->quantity_given - $returnableProduct->quantity_returned;

                // Sécurité : on ne peut pas retourner plus que ce qui a été remis
                if ($qtyToReturn > $qtyAvailable) {
                    abort(422, 'Quantité retournée supérieure à la quantité disponible');
                }

                // 1️⃣ Enregistrer le retour dans la table StocksReturnableProduct
                $returnedRow = StocksReturnableProduct::create([
                    'returnable_product_id' => $data['returnable_product_id'],
                    'product_id' => $item['product_id'],
                    'quantity_returned' => $qtyToReturn,
                    'date' => $date,
                    'comment' => $data['comment'] ?? null,
                ]);

                // 2️⃣ Mettre à jour la quantité retournée dans la table returnable_products_list
                $returnableProduct->increment('quantity_returned', $qtyToReturn);

                // 3️⃣ Mettre à jour le stock produit : augmenter la quantité ramenée
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $initialStock = $product->quantity;
                $product->quantity += $qtyToReturn;
                $product->save();

                // 4️⃣ Enregistrer le mouvement de stock
                Stock::create([
                    'date' => $date,
                    'initial_stock' => $initialStock,
                    'label' => 'Retour produits – client ' . $clientName,
                    'quantity' => $qtyToReturn,
                    'final_stock' => $product->quantity,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                ]);

                $rows[] = $returnedRow;
            }

            // 5️⃣ Notification globale
            Notification::create([
                'description' => 'Retour produits – Client : ' . $clientName .
                    ' : ' . implode(', ', array_map(
                        fn($r) => $r->product->name . ' (+' . $r->quantity_returned . ')',
                        $rows
                    )),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Retours enregistrés avec succès',
                'data' => $rows,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l’enregistrement des retours',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //supprimer un retour
    /*
    public function deleteReturn($id)
    {
        DB::beginTransaction();

        try {
            // 1️⃣ Récupérer le retour
            $return = StocksReturnableProduct::with(['client', 'items.product'])
                ->findOrFail($id);

            $client = $return->client;

            $notifications = [];

            // 2️⃣ Pour chaque produit retourné → annuler l'impact stock
            foreach ($return->items as $item) {

                if ($item->quantity_returned > 0) {

                    $product = Product::lockForUpdate()->findOrFail($item->product_id);

                    // Stock avant correction
                    $initialStock = $product->quantity;

                    // On retire du stock ce qui avait été ajouté par le retour
                    $product->quantity -= $item->quantity_returned;
                    $product->save();

                    // Mouvement de stock inverse
                    Stock::create([
                        'date'          => now()->toDateString(),
                        'initial_stock' => $initialStock,
                        'label'         => 'Annulation retour produits – client ' . $client->name,
                        'quantity'      => $item->quantity_returned,
                        'final_stock'   => $product->quantity,
                        'product_id'    => $product->id,
                        'product_name'  => $product->name,
                    ]);

                    $notifications[] = $product->name . ' (-' . $item->quantity_returned . ')';
                }
            }

            // 3️⃣ Supprimer les lignes enfants puis l'opération
            ReturnableProductsList::where('returnable_product_id', $return->id)->delete();
            $return->delete();

            // 4️⃣ Notification métier
            Notification::create([
                'description' =>
                'Suppression retour produits – Client : ' . $client->name .
                    (!empty($notifications) ? ' : ' . implode(', ', $notifications) : ''),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Retour supprimé avec succès'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du retour',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
        */
}
