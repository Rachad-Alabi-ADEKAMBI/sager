<?php

namespace App\Http\Controllers;

use App\Models\StocksReturnableProduct;
use Illuminate\Http\Request;

class StocksReturnableProductController extends Controller
{
    public function index()
    {
        return StocksReturnableProduct::orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id' => 'required|integer',
            'product_id' => 'required|integer',
            'buyer_name' => 'required|string|max:255',
            'quantity_purchased' => 'required|integer|min:0',
            'quantity_returned' => 'required|integer|min:0',
        ]);

        return StocksReturnableProduct::create($data);
    }

    public function stocksReturnableProductsList()
    {
        return StocksReturnableProduct::orderBy('id', 'desc')->get();
    }
}
