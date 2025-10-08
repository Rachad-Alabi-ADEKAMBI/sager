<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SaleProduct;
use App\Models\Stock;
use Illuminate\Support\Carbon;
use App\Models\Notification;
use App\Models\Deposit;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProductController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('products')->get();
        return view('sales.index', compact('sales'));
    }

    //  public function store(Request $request) {}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_name' => 'required|string',
            'buyer_name' => 'required|string',
            'buyer_phone' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity'   => 'required|numeric|min:0.01',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $total = collect($validated['products'])->sum(fn($item) => $item['quantity'] * $item['price']);

            $sale = Sale::create([
                'seller_name' => $validated['seller_name'],
                'buyer_name' => $validated['buyer_name'],
                'buyer_phone' => $validated['buyer_phone'],
                'total' => $total,
                'status' => 'done',
            ]);

            $details = [];

            foreach ($validated['products'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $initial_quantity = $product->quantity;

                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Stock insuffisant pour le produit : {$product->name}");
                }

                // Ligne de vente
                SaleProduct::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Mise à jour du stock
                $product->decrement('quantity', $item['quantity']);
                $final_quantity = $initial_quantity - $item['quantity'];

                // Historique du stock
                Stock::create([
                    'date' => now()->toDateString(),
                    'initial_stock' => $initial_quantity,
                    'label' => 'Vente produit ' . $product->name,
                    'quantity' => $item['quantity'],
                    'final_stock' => $final_quantity,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sale_id' => $sale->id,
                    'seller_name' => $sale->seller_name,
                ]);

                // Si produit consignable → création dans deposits
                if ($product->is_depositable) {
                    $depositTotal = $product->deposit_price * $item['quantity'];

                    Deposit::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'deposit_price_at_sale' => $product->deposit_price,
                        'quantity' => $item['quantity'],
                        'total' => $depositTotal,
                        'status' => 'En cours',
                    ]);
                }

                $details[] = [
                    'product' => $product->name,
                    'quantity_sold' => $item['quantity'],
                    'price' => $item['price']
                ];
            }

            // Notification
            Notification::create([
                'description' => 'Facture N°' . $sale->id . '/' . now()->format('m') . '/' . now()->format('y')
                    . '/FR-N pour ' . $sale->buyer_name . ' par ' . $sale->seller_name
                    . '. Total : ' . $total . ' FCFA.',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Vente enregistrée avec succès.',
                'sale_id' => $sale->id,
                'total' => $total,
                'products' => $details,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Échec de l\'enregistrement de la vente.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $sale = Sale::with('products')->findOrFail($id);
        return view('sales.show', compact('sale'));
    }

    public function createSale()
    {
        $products = Product::all();
        return view('sales.create', compact('products'));
    }

    public function create()
    {
        $products = Product::all();
        return view('pages.back.seller.sale', compact('products'));
    }

    public function cancel($id)
    {
        DB::beginTransaction();

        try {
            $sale = Sale::findOrFail($id);

            // Vérifie que la vente est bien "done"
            if ($sale->status !== 'done') {
                return response()->json([
                    'error' => 'Annulation impossible',
                    'message' => 'Seules les ventes avec le statut "done" peuvent être annulées.'
                ], 403);
            }

            $saleProducts = SaleProduct::where('sale_id', $sale->id)->get();

            // Vérification du stock avant annulation
            foreach ($saleProducts as $item) {
                $product = Product::findOrFail($item->product_id);
                if ($product->quantity < 0) {
                    return response()->json([
                        'error' => 'Stock insuffisant',
                        'message' => "Impossible d'annuler la facture. Stock actuel insuffisant pour le produit '{$product->name}'."
                    ], 409);
                }
            }

            // Restauration du stock
            foreach ($saleProducts as $item) {
                $product = Product::findOrFail($item->product_id);
                $initial_quantity = $product->quantity;

                // Restaure la quantité vendue
                $product->increment('quantity', $item->quantity);

                // Enregistrement dans l’historique du stock
                Stock::create([
                    'date' => now()->toDateString(),
                    'initial_stock' => $initial_quantity,
                    'label' => 'Annulation de la facture N°' . $id . ' pour '
                        . $sale->buyer_name . ' par '
                        . (Auth::user()->role === 'admin' ? 'admin' : $sale->seller_name) . '.',
                    'quantity' => $item->quantity,
                    'final_stock' => $product->quantity,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sale_id' => $sale->id,
                    'seller_name' => $sale->seller_name,
                ]);
            }

            // ✅ Annulation des produits consignés
            $deposits = Deposit::where('sale_id', $sale->id)->get();
            if ($deposits->isNotEmpty()) {
                foreach ($deposits as $deposit) {
                    $deposit->update(['status' => 'Annulée']);
                }
            }

            // Mise à jour du statut de la vente
            $sale->status = 'cancelled';
            $sale->save();

            // Notification
            Notification::create([
                'description' => 'Annulation de la facture N°' . $id . ' pour '
                    . $sale->buyer_name . ' par '
                    . (Auth::user()->role === 'admin' ? 'admin' : $sale->seller_name) . '.',
            ]);

            DB::commit();

            return response()->json(['message' => 'Facture annulée avec succès.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Échec de l\'annulation de la facture.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
