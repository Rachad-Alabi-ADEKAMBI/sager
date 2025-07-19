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

        return redirect()->route('stocks');

    }
}
