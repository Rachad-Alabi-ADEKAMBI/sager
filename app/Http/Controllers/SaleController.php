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

}
