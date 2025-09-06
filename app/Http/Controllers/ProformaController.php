<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Proforma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProformaController extends Controller
{
    public function index()
    {
        $proformas = Proforma::with('products')->get();
        return view('proformas.index', compact('proformas'));
    }

    public function create()
    {
        $products = Product::all();
        return view('proformas.create', compact('products'));
    }

public function store(Request $request)
{
    // Validation des données
    $validated = $request->validate([
        'seller_name'           => 'required|string',
        'buyer_name'            => 'required|string',
        'buyer_phone'           => 'nullable|string',
        'products'              => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity'   => 'required|integer|min:1',
        'products.*.price'      => 'required|numeric|min:0',
    ]);

    // Calcul du total
    $total = 0;
    $products_details = [];

    foreach ($validated['products'] as $item) {
        $product = Product::find($item['product_id']); // juste pour info
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;

        $products_details[] = [
            'product_id' => $item['product_id'],
            'name'       => $product ? $product->name : null,
            'quantity'   => $item['quantity'],
            'price'      => $item['price'],
            'subtotal'   => $subtotal,
        ];
    }

    // Debug : afficher toutes les données reçues
    dd([
        'seller_name' => $validated['seller_name'],
        'buyer_name'  => $validated['buyer_name'],
        'buyer_phone' => $validated['buyer_phone'] ?? null,
        'total'       => $total,
        'products'    => $products_details,
    ]);

    // Ici on ne fait pas de DB::beginTransaction ni d'insert
}




    public function show($id)
    {
        $proforma = Proforma::with('products')->findOrFail($id);
        return view('proformas.show', compact('proforma'));
    }
}
