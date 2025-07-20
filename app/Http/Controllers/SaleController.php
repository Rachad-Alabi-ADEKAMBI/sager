<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;

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
            'buyer_phone' => 'required|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // Calculer le total et vérifier les stocks
        $total = 0;
        foreach ($data['products'] as $item) {
            $product = Product::findOrFail($item['id']);
            if ($product->stock < $item['quantity']) {
                return back()->withErrors(['stock' => "Stock insuffisant pour {$product->name}"]);
            }
            // Pour le prix, on peut choisir price_detail par défaut
            $total += $product->price_detail * $item['quantity'];
        }

        // Créer la vente
        $sale = Sale::create([
            'seller_name' => $data['seller_name'],
            'buyer_name' => $data['buyer_name'],
            'buyer_phone' => $data['buyer_phone'],
            'total' => $total,
        ]);

        // Attacher les produits et déstocker
        foreach ($data['products'] as $item) {
            $product = Product::findOrFail($item['id']);

            // Diminuer le stock
            $product->decrement('stock', $item['quantity']);

            // Attacher avec quantité et prix
            $sale->products()->attach($product->id, [
                'quantity' => $item['quantity'],
                'price' => $product->price_detail,
            ]);
        }

        return redirect()->route('sales.index')->with('success', 'Vente enregistrée avec succès.');
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

}
