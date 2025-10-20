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
        // Valeur par défaut à 0 si non envoyée
        $request->merge([
            'is_depositable' => $request->input('is_depositable', 0),
        ]);

        $data = $request->validate([
            'name' => 'required|string',
            'purchase_price' => 'required|numeric',
            'price_detail' => 'nullable|numeric',
            'price_semi_bulk' => 'nullable|numeric',
            'price_bulk' => 'nullable|numeric',
            'quantity' => 'required|numeric',
            'is_depositable' => 'required|integer',
            'deposit_price' => 'nullable|numeric',
            'filling_price' => 'nullable|numeric',
        ]);

        try {
            // Gestion des champs selon le type
            if ($data['is_depositable']) {
                $data['price_detail'] = null;
                $data['price_semi_bulk'] = null;
                $data['price_bulk'] = null;
            } else {
                $data['deposit_price'] = null;
                $data['filling_price'] = null;
            }

            // Création du produit
            $product = Product::create($data);

            // Création du mouvement de stock
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

            // 🔹 Si le produit est consignable → création du dépôt et de l’historique
            if ($data['is_depositable']) {
                $deposit = \App\Models\Deposit::create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => 0,
                ]);

                \App\Models\StockDeposit::create([
                    'product_id' => $product->id,
                    'initial_stock' => 0,
                    'quantity' => 0,
                    'final_stock' => 0,
                    'comment' => 'Création du produit ' . $product->name,
                ]);
            }

            // Création d’une notification
            Notification::create([
                'description' => 'Produit ' . $data['name'] . ' ajouté avec succès. Quantité : ' . $data['quantity'] . '.',
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
            'price_detail' => 'required|numeric|min:0',
            'price_semi_bulk' => 'required|numeric|min:0',
            'price_bulk' => 'required|numeric|min:0',
            'deposit_price' => 'nullable|numeric|min:0',
        ]);

        // Trouver le produit
        $product = Product::findOrFail($id);

        // Récupération des anciens prix
        $oldDetail = $product->price_detail;
        $oldSemiBulk = $product->price_semi_bulk;
        $oldBulk = $product->price_bulk;
        $oldDeposit = $product->deposit_price;

        // Mise à jour des prix
        $product->price_detail = $validated['price_detail'];
        $product->price_semi_bulk = $validated['price_semi_bulk'];
        $product->price_bulk = $validated['price_bulk'];

        // Mise à jour du prix de consignation si le produit est consignable
        if ($product->is_depositable && isset($validated['deposit_price'])) {
            $product->deposit_price = $validated['deposit_price'];
        }

        // Construction du message de notification
        $messages = [];

        if ($oldDetail != $validated['price_detail']) {
            $messages[] = 'détail: ' . $oldDetail . ' → ' . $validated['price_detail'];
        }
        if ($oldSemiBulk != $validated['price_semi_bulk']) {
            $messages[] = 'semi-gros: ' . $oldSemiBulk . ' → ' . $validated['price_semi_bulk'];
        }
        if ($oldBulk != $validated['price_bulk']) {
            $messages[] = 'gros: ' . $oldBulk . ' → ' . $validated['price_bulk'];
        }
        if ($product->is_depositable && $oldDeposit != $product->deposit_price) {
            $messages[] = 'consignation: ' . $oldDeposit . ' → ' . $product->deposit_price;
        }

        $product->save();

        // Créer une notification seulement si au moins un prix a changé
        if (!empty($messages)) {
            $label = 'Mise à jour des prix pour le produit "' . $product->name . '": ' . implode('; ', $messages);
            Notification::create([
                'description' => $label,
            ]);
        }

        return response()->json([
            'message' => 'Prix mis à jour avec succès',
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
        ]);

        $product = Product::findOrFail($id);

        $initial_quantity = $product->quantity;
        $added_quantity = $request->input('quantity');

        $product->quantity += $added_quantity;
        $product->save();

        $label = "Mise à jour du stock de {$product->name}. Quantité ajoutée : {$added_quantity}.";

        Stock::create([
            'date' => now()->toDateString(),
            'initial_stock' => $initial_quantity,
            'label' => $label,
            'quantity' => $added_quantity,
            'final_stock' => $product->quantity,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sale_id' => null,
            'seller_name' => null,
        ]);

        Notification::create([
            'description' => $label,
        ]);

        return response()->json(['message' => 'Stock ajouté avec succès.']);
    }


    public function getAccountingData($id)
    {
        $product = Product::findOrFail($id);

        // Récupérer les ventes 'done' pour ce produit
        $doneSaleIds = \App\Models\Sale::where('status', 'done')->pluck('id');

        $sales = \App\Models\SaleProduct::where('product_id', $id)
            ->whereIn('sale_id', $doneSaleIds)
            ->get();

        $totalQuantity = $sales->sum('quantity');

        $totalRevenue = $sales->sum(function ($sale) {
            return $sale->quantity * $sale->price;
        });

        $totalCost = $product->purchase_price * $totalQuantity;

        $profit = $totalRevenue - $totalCost;

        $salesDetails = $sales->map(function ($sale) use ($product) {
            return [
                'id' => $sale->id,
                'quantity' => $sale->quantity,
                'price' => $sale->price,
                'created_at' => $sale->created_at->toDateTimeString(),
                'total_sale' => $sale->quantity * $sale->price,
                'total_cost' => $product->purchase_price * $sale->quantity,
                'profit' => $sale->quantity * $sale->price - $product->purchase_price * $sale->quantity,
            ];
        });

        return response()->json([
            'total_quantity_sold' => $totalQuantity,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'profit' => $profit,
            'sales_details' => $salesDetails,
        ]);
    }



    public function revertAddStock(Request $request)
    {
        // 1. Validation de l'ID du produit
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        DB::beginTransaction();

        try {
            // 2. Récupérer le produit concerné
            $product = Product::findOrFail($validated['product_id']);

            // 3. Trouver la dernière opération d'ajout de stock pour ce produit
            $lastStockEntry = Stock::where('product_id', $product->id)
                ->where('label', 'like', 'Mise à jour du stock de %')
                ->where('quantity', '>', 0) // Pour s'assurer que c'était bien un ajout
                ->latest()
                ->first();

            // 4. Vérifier si une opération à annuler a été trouvée
            if (!$lastStockEntry) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Opération non trouvée',
                    'message' => 'Aucune opération d\'ajout de stock récente n\'a été trouvée pour ce produit.',
                ], 404);
            }

            // 5. Vérifier si le stock actuel est suffisant
            if ($product->quantity < $lastStockEntry->quantity) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Stock insuffisant',
                    'message' => "Impossible d'annuler l'opération. Le stock actuel est inférieur à la quantité à retirer."
                ], 409);
            }

            // 6. Mettre à jour le stock du produit
            $initialQuantity = $product->quantity;
            $product->quantity -= $lastStockEntry->quantity;
            $product->save();

            // 7. Enregistrer une nouvelle opération dans la table `stocks`
            $label = "Annulation de l'opération de mise à jour du stock.";
            Stock::create([
                'date' => Carbon::now()->toDateString(),
                'initial_stock' => $initialQuantity,
                'label' => $label,
                'quantity' => -$lastStockEntry->quantity, // Quantité négative pour indiquer le retrait
                'final_stock' => $product->quantity,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sale_id' => null,
                'seller_name' => null,
            ]);

            // 8. Enregistrer une notification
            Notification::create([
                'description' => "Opération annulée pour le produit '{$product->name}'. Quantité retirée : {$lastStockEntry->quantity}.",
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Opération d\'ajout de stock annulée avec succès.',
                'product' => $product,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Échec de l\'annulation de l\'opération.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            Notification::create([
                'description' => 'Produit ' . $product->name . ' supprimé.',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Produit supprimé avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de suppression: ' . $e->getMessage()
            ], 500);
        }
    }
}
