<?php


namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'initial_stock' => 'required|integer',
            'label' => 'required|string',
            'quantity' => 'required|integer',
            'final_stock' => 'required|integer',
            'product_id' => 'required|integer',
            'product_name' => 'required|string',
            'sale_id' => 'nullable|integer',
            'seller_name' => 'nullable|string',
        ]);

        Stock::create($validated);

        return response()->json(['message' => 'Stock enregistré avec succès.']);
    }
}
