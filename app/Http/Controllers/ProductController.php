<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;


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

        Product::create($data);

        echo "<script>alert('Produit ajouté avec succès.'); window.location.href = '/stocks';</script>";

        return redirect()->route('stocks');

    }

    public function update(Request $request, $id)
{
    $request->validate([
        'price_detail' => 'required|numeric',
        'price_semi_bulk' => 'required|numeric',
        'price_bulk' => 'required|numeric',
    ]);

    $product = Product::findOrFail($id);
    $product->price_detail = $request->price_detail;
    $product->price_semi_bulk = $request->price_semi_bulk;
    $product->price_bulk = $request->price_bulk;
    $product->save();

    return redirect()->back()->with('success', 'Prix mis à jour.');
}

}
