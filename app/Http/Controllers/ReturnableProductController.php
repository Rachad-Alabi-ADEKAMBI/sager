<?php

namespace App\Http\Controllers;

use App\Models\ReturnableProduct;
use App\Models\ReturnableProductsList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StocksReturnableProduct;


class ReturnableProductController extends Controller
{
    public function index()
    {
        // Vérifie que l'utilisateur est connecté et est admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        // Retourne la vue avec les clients
        return view('pages/back/admin/returnableProducts');
    }


    //enregistrer une remise
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'comment' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_given' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            // Client
            $client = Client::findOrFail($data['client_id']);

            // 1️⃣ Création de la remise
            $operation = ReturnableProduct::create([
                'client_id' => $data['client_id'],
                'date'      => $data['date'],
                'comment'   => $data['comment'] ?? null,
                'status'    => 'Fait', // ✅ ajout du statut
            ]);

            $notifications = [];

            // 2️⃣ Produits remis au client
            foreach ($data['items'] as $item) {

                $product  = Product::lockForUpdate()->findOrFail($item['product_id']);
                $quantity = round($item['quantity_given'], 2);

                // Table pivot métier
                ReturnableProductsList::create([
                    'returnable_product_id' => $operation->id,
                    'product_id'           => $product->id,
                    'quantity_given'       => $quantity, // remis au client
                    'quantity_returned'    => 0,         // rien encore retourné
                ]);

                // Mise à jour stock
                $initialStock = $product->quantity;
                $product->quantity -= $quantity;
                $product->save();

                // Mouvement de stock
                Stock::create([
                    'date'          => now()->toDateString(),
                    'initial_stock' => $initialStock,
                    'label'         => 'Remise produits retournables – client ' . $client->name,
                    'quantity'      => $quantity,
                    'final_stock'   => $product->quantity,
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                ]);

                $notifications[] = $product->name . ' (+' . $quantity . ')';
            }

            // 3️⃣ Notification
            Notification::create([
                'description' =>
                'Remise produits retournables – Client : ' . $client->name . ' : ' .
                    implode(', ', $notifications),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Remise enregistrée avec ses produits',
                'data' => $operation->load('products')
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la remise',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Annuler une remise
    public function cancel($id)
    {
        DB::beginTransaction();

        try {
            $operation = ReturnableProduct::with(['client'])
                ->lockForUpdate()
                ->findOrFail($id);

            if ($operation->status === 'Annulé') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette remise est déjà annulée'
                ], 400);
            }

            $client = $operation->client;
            $notifications = [];

            // 🔥 On récupère les lignes métier directement
            $items = ReturnableProductsList::where('returnable_product_id', $operation->id)
                ->lockForUpdate()
                ->get();

            foreach ($items as $item) {
                $given = (float) $item->quantity_given;

                $returnRows = StocksReturnableProduct::where('returnable_product_id', $item->id)
                    ->lockForUpdate()
                    ->get();

                $returned = round((float) $returnRows->sum('quantity_returned'), 2);

                $correction = round($given - $returned, 2);

                if ($correction > 0) {

                    $product = Product::lockForUpdate()->findOrFail($item->product_id);

                    $initialStock = $product->quantity;
                    $product->quantity += $correction;
                    $product->save();

                    Stock::create([
                        'date'          => now()->toDateString(),
                        'initial_stock' => $initialStock,
                        'label'         => 'Annulation remise produits retournables – client ' . $client->name,
                        'quantity'      => $correction,
                        'final_stock'   => $product->quantity,
                        'product_id'    => $product->id,
                        'product_name'  => $product->name,
                    ]);

                    $notifications[] = $product->name . ' (+' . $correction . ')';
                }

                if ($item->quantity_returned != 0) {
                    $item->quantity_returned = 0;
                    $item->save();
                }

                if ($returnRows->isNotEmpty()) {
                    StocksReturnableProduct::whereIn('id', $returnRows->pluck('id'))->delete();
                }
            }

            // ✅ Mise à jour du statut
            $operation->status = 'Annulé';
            $operation->save();

            // Notification
            Notification::create([
                'description' =>
                'Annulation remise produits retournables – Client : ' . $client->name .
                    (!empty($notifications) ? ' : ' . implode(', ', $notifications) : ''),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Remise annulée avec succès'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l’annulation',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    public function getReturnableProductsTransactions()
    {
        $transactions = ReturnableProduct::with('client:id,name') // récupère seulement id et name du client
            ->orderBy('date', 'desc')
            ->get([
                'id',
                'client_id',
                'date',
                'reference',
                'status',
                'comment',
                'created_at',
                'updated_at'
            ]);

        // Ajouter un champ client_name directement dans les résultats
        $transactions->transform(function ($item) {
            $item->client_name = $item->client->name ?? null;
            unset($item->client); // optionnel : enlève l'objet client
            return $item;
        });

        return response()->json(
            $transactions,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
