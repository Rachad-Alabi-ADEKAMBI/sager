<?php

namespace App\Http\Controllers;

use App\Models\StocksReturnableProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class StocksReturnableProductController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'returnable_product_id' => 'required|exists:returnable_products,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_returned' => 'required|numeric|min:0.01',
            'date' => 'nullable|date',
            'comment' => 'nullable|string',
        ]);

        $rows = [];
        $date = $data['date'] ?? now()->toDateString();

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            // Vérifier que le produit est returnable
            if (!$product->isReturnable) continue;

            $rows[] = StocksReturnableProduct::create([
                'returnable_product_id' => $data['returnable_product_id'],
                'product_id' => $item['product_id'],
                'quantity_returned' => round($item['quantity_returned'], 2),
                'date' => $date,
                'comment' => $data['comment'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Retours enregistrés avec succès',
            'data' => $rows,
        ], 201);
    }
}
