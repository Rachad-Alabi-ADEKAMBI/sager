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
    /**
     * Retourne tous les retours enregistrés.
     * Le front-end filtre par returnable_product_id = ID de la ligne (returnable_products_list.id)
     */
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

    /**
     * Enregistre les retours de produits d'un client.
     *
     * Règle importante :
     *   - items[].returnable_product_id = ID de la LIGNE dans returnable_products_list
     *   - C'est cet ID que le front-end utilise dans getRealReturnedQtyForItem(itemId)
     *   - On stocke donc cet ID (et non l'ID de la transaction parente) dans stocks_returnable_products
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'returnable_product_id'               => 'nullable|exists:returnable_products,id',
            'transaction_id'                      => 'nullable|exists:returnable_products,id',
            'items'                               => 'required|array|min:1',
            'items.*.returnable_product_id'       => 'required|exists:returnable_products_list,id',
            'items.*.product_id'                  => 'required|exists:products,id',
            'items.*.quantity_returned'           => 'required|numeric|min:0.01',
            'date'                                => 'nullable|date',
            'comment'                             => 'nullable|string',
        ]);

        $transactionId = $data['returnable_product_id'] ?? $data['transaction_id'] ?? null;

        if (!$transactionId) {
            return response()->json([
                'success' => false,
                'message' => 'La transaction de produits consignés est requise.',
            ], 422);
        }

        $date              = $data['date'] ?? now()->toDateTimeString();
        $notificationLines = [];

        DB::beginTransaction();

        try {
            // Récupérer la transaction parente pour avoir le nom du client
            $transaction = ReturnableProduct::with('client')->findOrFail($transactionId);
            $clientName  = $transaction->client->name ?? 'Client inconnu';

            foreach ($data['items'] as $item) {

                // Ligne produit dans returnable_products_list identifiée par son propre ID
                $listRow = ReturnableProductsList::lockForUpdate()->findOrFail($item['returnable_product_id']);

                $qtyToReturn  = round($item['quantity_returned'], 2);
                // Quantité encore en attente de retour
                $qtyAvailable = round($listRow->quantity_given - $listRow->quantity_returned, 2);

                if ($qtyToReturn > $qtyAvailable) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Quantité retournée (' . $qtyToReturn . ') supérieure à la quantité disponible (' . $qtyAvailable . ')',
                    ], 422);
                }

                // 1. Enregistrer le retour avec l'ID de la LIGNE produit (pas l'ID de la transaction)
                //    → le front-end filtre sur ce champ dans getRealReturnedQtyForItem()
                StocksReturnableProduct::create([
                    'returnable_product_id' => $listRow->id,
                    'product_id'            => $item['product_id'],
                    'quantity_returned'     => $qtyToReturn,
                    'date'                  => $date,
                    'comment'               => $data['comment'] ?? null,
                ]);

                // 2. Mettre à jour le cumul retourné sur la ligne produit
                $listRow->increment('quantity_returned', $qtyToReturn);

                // 3. Remettre en stock (retour client = entrée stock)
                $product           = Product::lockForUpdate()->findOrFail($item['product_id']);
                $initialStock      = $product->quantity;
                $product->quantity += $qtyToReturn;
                $product->save();

                // 4. Mouvement de stock traçable
                Stock::create([
                    'date'          => $date,
                    'initial_stock' => $initialStock,
                    'label'         => 'Retour produits – client ' . $clientName,
                    'quantity'      => $qtyToReturn,
                    'final_stock'   => $product->quantity,
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                ]);

                $notificationLines[] = $product->name . ' (+' . $qtyToReturn . ')';
            }

            // 5. Notification
            Notification::create([
                'description' => 'Retour produits – Client : ' . $clientName .
                    ' : ' . implode(', ', $notificationLines),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Retours enregistrés avec succès',
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de l'enregistrement des retours",
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $return = StocksReturnableProduct::lockForUpdate()->findOrFail($id);

            $listRow = ReturnableProductsList::lockForUpdate()->findOrFail($return->returnable_product_id);
            $product = Product::lockForUpdate()->findOrFail($return->product_id);
            $transaction = ReturnableProduct::with('client')->find($listRow->returnable_product_id);

            $qtyToRemove = round((float) $return->quantity_returned, 2);
            $currentReturnedQty = round((float) $listRow->quantity_returned, 2);

            if ($qtyToRemove > $currentReturnedQty) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer ce retour car le cumul retourne est incoherent.',
                ], 422);
            }

            if ($qtyToRemove > (float) $product->quantity) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant pour annuler ce retour.',
                ], 422);
            }

            $listRow->decrement('quantity_returned', $qtyToRemove);

            $initialStock = $product->quantity;
            $product->quantity = round($product->quantity - $qtyToRemove, 2);
            $product->save();

            $clientName = $transaction?->client?->name ?? 'Client inconnu';

            Stock::create([
                'date'          => now()->toDateString(),
                'initial_stock' => $initialStock,
                'label'         => 'Suppression retour produits - client ' . $clientName,
                'quantity'      => $qtyToRemove,
                'final_stock'   => $product->quantity,
                'product_id'    => $product->id,
                'product_name'  => $product->name,
            ]);

            Notification::create([
                'description' => 'Suppression retour produits - Client : ' . $clientName .
                    ' : ' . $product->name . ' (-' . $qtyToRemove . ')',
            ]);

            $return->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Retour supprime avec succes',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du retour',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
