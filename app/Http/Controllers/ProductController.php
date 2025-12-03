<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Notification;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SaleController;
use App\Models\Stock;
use App\Models\StockDeposit;
use App\Models\Deposit;
use App\Models\Sale;
use App\Models\SaleProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class ProductController extends BaseController
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }


    public function store(Request $request)
    {
        // Valeurs par défaut
        $request->merge([
            'is_depositable' => $request->input('is_depositable', 0),
            'isReturnable'   => $request->input('isReturnable', 0),
        ]);

        $data = $request->validate([
            'name' => 'required|string',

            'purchase_price' => 'required|numeric',
            'price_detail' => 'nullable|numeric',
            'price_semi_bulk' => 'nullable|numeric',
            'price_bulk' => 'nullable|numeric',

            'quantity' => 'required|numeric',

            'is_depositable' => 'required|integer',
            'isReturnable'   => 'required|integer',

            'deposit_price' => 'nullable|numeric',
            'filling_price' => 'nullable|numeric',

            // Nouveaux champs
            'benefit_deposit' => 'nullable|numeric',
            'benefit_refill' => 'nullable|numeric',
            'benefit_deposit_refill' => 'nullable|numeric',
        ]);

        try {

            // Gestion automatique selon consignable ou non
            if ($data['is_depositable']) {
                // Produit consignable → prix classiques inutiles
                $data['price_detail'] = null;
                $data['price_semi_bulk'] = null;
                $data['price_bulk'] = null;
            } else {
                // Produit normal → prix consignation inutiles
                $data['deposit_price'] = null;
                $data['filling_price'] = null;
                $data['benefit_deposit'] = null;
                $data['benefit_refill'] = null;
                $data['benefit_deposit_refill'] = null;
            }

            // Création du produit
            $product = Product::create($data);

            // Mouvement de stock initial
            Stock::create([
                'date' => now()->toDateString(),
                'initial_stock' => 0,
                'label' => 'Ajout du produit ' . $product->name,
                'quantity' => $data['quantity'],
                'final_stock' => $data['quantity'],
                'product_id' => $product->id,
                'product_name' => $product->name,
                'is_depositable' => $data['is_depositable'],
                'deposit_price' => $data['deposit_price'],
                'filling_price' => $data['filling_price'],
                'sale_id' => null,
                'seller_name' => null,
            ]);

            // Dépôt si consignable
            if ($data['is_depositable']) {

                Deposit::create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => 0,
                ]);

                StockDeposit::create([
                    'product_id' => $product->id,
                    'initial_stock' => 0,
                    'quantity' => 0,
                    'final_stock' => 0,
                    'comment' => 'Création du produit ' . $product->name,
                ]);
            }

            // Notification
            Notification::create([
                'description' =>
                'Produit ' . $data['name'] . ' ajouté avec succès. Quantité : ' . $data['quantity'] . '.',
            ]);

            return response()->json(['message' => 'Produit ajouté avec succès.'], 201);
        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Erreur lors de la création du produit.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePrices(Request $request, $id)
    {
        // Validation des données reçues
        $validated = $request->validate([
            'price_detail' => 'nullable|numeric|min:0',
            'price_semi_bulk' => 'nullable|numeric|min:0',
            'price_bulk' => 'nullable|numeric|min:0',
            'deposit_price' => 'nullable|numeric|min:0',
            'filling_price' => 'nullable|numeric|min:0',
            'benefit_deposit' => 'nullable|numeric|min:0',
            'benefit_refill' => 'nullable|numeric|min:0',
            'benefit_deposit_refill' => 'nullable|numeric|min:0',
        ]);

        // Trouver le produit
        $product = Product::findOrFail($id);

        // Récupération des anciens prix et bénéfices
        $old = [
            'price_detail' => $product->price_detail,
            'price_semi_bulk' => $product->price_semi_bulk,
            'price_bulk' => $product->price_bulk,
            'deposit_price' => $product->deposit_price,
            'filling_price' => $product->filling_price,
            'benefit_deposit' => $product->benefit_deposit,
            'benefit_refill' => $product->benefit_refill,
            'benefit_deposit_refill' => $product->benefit_deposit_refill,
        ];

        // Mise à jour des prix classiques
        foreach (['price_detail', 'price_semi_bulk', 'price_bulk', 'deposit_price', 'filling_price', 'benefit_deposit', 'benefit_refill', 'benefit_deposit_refill'] as $field) {
            if (isset($validated[$field])) {
                $product->$field = $validated[$field];
            }
        }

        $product->save();

        // Construire le message de notification
        $messages = [];
        foreach ($old as $key => $oldValue) {
            if ($oldValue != $product->$key) {
                $messages[] = "$key: $oldValue → {$product->$key}";
            }
        }

        if (!empty($messages)) {
            $label = 'Mise à jour des prix/bénéfices pour le produit "' . $product->name . '": ' . implode('; ', $messages);
            Notification::create([
                'description' => $label,
            ]);
        }

        return response()->json([
            'message' => 'Prix et bénéfices mis à jour avec succès',
            'product' => $product
        ], 200);
    }


    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => [
                'required',
                'numeric',
                'regex:/^\d+(\.\d{1,2})?$/',
                'min:0.01',
            ],
            'note' => ['nullable', 'string'],
        ]);

        $product = Product::findOrFail($id);

        $initial_quantity = $product->quantity;
        $added_quantity = $request->input('quantity');
        $product->quantity += $added_quantity;
        $product->save();

        $defaultLabel = "Mise à jour du stock de {$product->name}. Quantité ajoutée : {$added_quantity}.";
        $notificationDescription = $request->input('note') ?: $defaultLabel;

        Stock::create([
            'date' => now()->toDateString(),
            'initial_stock' => $initial_quantity,
            'label' =>  $notificationDescription,
            'quantity' => $added_quantity,
            'final_stock' => $product->quantity,
            'product_id' => $product->id,
            'product_name' => $product->name
        ]);

        Notification::create([
            'description' => $notificationDescription,
        ]);

        return response()->json(['message' => 'Stock ajouté avec succès.']);
    }


    public function removeStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => [
                'required',
                'numeric',
                'regex:/^\d+(\.\d{1,2})?$/',
                'min:0.01',
            ],
            'note' => ['nullable', 'string'],
        ]);

        $product = Product::findOrFail($id);
        $removed_quantity = $request->input('quantity');

        if ($removed_quantity > $product->quantity) {
            return response()->json([
                'message' => 'Stock insuffisant pour retirer cette quantité.'
            ], 400);
        }

        $initial_quantity = $product->quantity;
        $product->quantity -= $removed_quantity;
        $product->save();

        // Label par défaut
        $defaultLabel = "Retrait de stock pour {$product->name}. Quantité retirée : {$removed_quantity}.";

        // Utiliser la note si fournie
        $notificationDescription = $request->input('note') ?: $defaultLabel;

        Stock::create([
            'date' => now()->toDateString(),
            'initial_stock' => $initial_quantity,
            'label' =>  $notificationDescription,
            'quantity' => -$removed_quantity,
            'final_stock' => $product->quantity,
            'product_id' => $product->id,
            'product_name' => $product->name
        ]);

        Notification::create([
            'description' => $notificationDescription,
        ]);

        return response()->json(['message' => 'Stock retiré avec succès.']);
    }


    public function getAccountingData($id)
    {
        $product = Product::findOrFail($id);

        // Récupérer les ventes 'done' pour ce produit
        $doneSaleIds = \App\Models\Sale::where('status', 'done')->pluck('id');

        $sales = \App\Models\SaleProduct::where('product_id', $id)
            ->whereIn('sale_id', $doneSaleIds)
            ->get();

        $totalQuantity = 0;
        $totalRevenue = 0;
        $totalCost = 0;
        $totalProfit = 0;

        $salesDetails = $sales->map(function ($sale) use ($product, &$totalQuantity, &$totalRevenue, &$totalCost, &$totalProfit) {

            $quantity = (float) $sale->quantity;
            $salePrice = (float) $sale->price;
            $purchasePrice = (float) $product->purchase_price;

            // Calcul du profit selon consignable ou non
            if ((int) $product->is_depositable === 1) {
                $priceType = $sale->price_type ?? 'refill';

                $profitUnit = match ($priceType) {
                    'refill' => (float) ($product->benefit_refill ?? 4000),
                    'deposit' => (float) ($product->benefit_deposit ?? 5000),
                    'both' => (float) ($product->benefit_deposit_refill ?? 6000),
                    default => (float) ($product->benefit_refill ?? 4000),
                };

                $profit = $profitUnit * $quantity;
            } else {
                $profit = ($salePrice - $purchasePrice) * $quantity;
            }

            $revenue = $salePrice * $quantity;
            $cost = $purchasePrice * $quantity;

            // Ajouter aux totaux
            $totalQuantity += $quantity;
            $totalRevenue += $revenue;
            $totalCost += $cost;
            $totalProfit += $profit;

            return [
                'id' => $sale->id,
                'quantity' => number_format($quantity, 2, '.', ''),
                'price' => number_format($salePrice, 2, '.', ''),
                'created_at' => $sale->created_at->toDateTimeString(),
                'total_sale' => $revenue,
                'total_cost' => $cost,
                'profit' => $profit,
            ];
        });

        return response()->json([
            'total_quantity_sold' => $totalQuantity,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'profit' => $totalProfit,
            'sales_details' => $salesDetails,
        ]);
    }


    public function destroy($id, Request $request)
    {
        try {
            $product = Product::findOrFail($id);

            // Soft delete : remplit automatiquement la colonne deleted_at
            $product->delete();

            Notification::create([
                'description' => 'Produit "' . $product->name . '" marqué comme supprimé.',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Produit supprimé avec succès (soft delete).',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de suppression : ' . $e->getMessage(),
            ], 500);
        }
    }
}
