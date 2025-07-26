<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Notification;

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
        $data = $request->validate([
            'name' => 'required|string',
            'purchase_price' => 'required|numeric',
            'price_detail' => 'required|numeric',
            'price_semi_bulk' => 'required|numeric',
            'price_bulk' => 'required|numeric',
            'quantity' => 'required|integer',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('products', 'public');
        }

      try {
    Product::create($data);

    Notification::create([
        'description' => 'Produit ' . $data['name'] . ' ajouté avec succès. Quantité : ' . $data['quantity'] . '.',
    ]);

    return response()->json(['message' => 'Produit ajouté avec succès.'], 201); // Code 201 Created
} catch (\Exception $e) {
    return response()->json(['error' => 'Erreur lors de la création du produit.'], 500); // Code 500 Internal Server Error
}

    }

    public function update(Request $request, $id)
    {
        // Validation des données reçues
        $validated = $request->validate([
            'price_detail' => 'required|numeric|min:0',
            'price_semi_bulk' => 'required|numeric|min:0',
            'price_bulk' => 'required|numeric|min:0',
        ]);

        // Trouver le produit
        $product = Product::findOrFail($id);

        // Mise à jour des prix
        $product->price_detail = $validated['price_detail'];
        $product->price_semi_bulk = $validated['price_semi_bulk'];
        $product->price_bulk = $validated['price_bulk'];

        $product->save();

        return response()->json([
            'message' => 'Prix mis à jour avec succès',
            'product' => $product
        ], 200);
    }

}
