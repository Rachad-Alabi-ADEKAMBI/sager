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

    public function store(Request $request)
    {
        $data = $request->validate([
            'seller_name' => 'required|string',
            'buyer_name' => 'required|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $total = 0;

        foreach ($data['products'] as $item) {
            $product = Product::findOrFail($item['id']);
            if ($product->stock < $item['quantity']) {
                // Si c'est une requête API/Axios → JSON
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => "Échec de l'enregistrement de la vente.",
                        'message' => "Stock insuffisant pour le produit: {$product->name}",
                    ], 422);
                }

                // Sinon → comportement classique (formulaire HTML)
                return back()->withErrors(['stock' => "Stock insuffisant pour {$product->name}"]);
            }

            $total += $product->price_detail * $item['quantity'];
        }

        $sale = Sale::create([
            'seller_name' => $data['seller_name'],
            'buyer_name' => $data['buyer_name'],
            'total' => $total,
        ]);

        foreach ($data['products'] as $item) {
            $product = Product::findOrFail($item['id']);
            $product->decrement('stock', $item['quantity']);
            $sale->products()->attach($product->id, [
                'quantity' => $item['quantity'],
                'price' => $product->price_detail,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Vente enregistrée avec succès.',
                'sale_id' => $sale->id,
            ], 201);
        }

        return redirect()->route('sale.index')->with('success', 'Vente enregistrée avec succès.');
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

    //cancel a sale
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

            foreach ($saleProducts as $item) {
                $product = Product::findOrFail($item->product_id);
                if ($product->quantity < $item->quantity) {
                    return response()->json([
                        'error' => 'Stock insuffisant',
                        'message' => "Impossible d'annuler la facture. Stock actuel insuffisant pour le produit '{$product->name}'."
                    ], 409);
                }
            }

            foreach ($saleProducts as $item) {
                $product = Product::findOrFail($item->product_id);
                $initial_quantity = $product->quantity;
                $product->increment('quantity', $item->quantity);

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

            $sale->status = 'cancelled';
            $sale->save();

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
