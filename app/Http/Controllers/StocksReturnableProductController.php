<?php

namespace App\Http\Controllers;

use App\Models\StocksReturnableProduct;
use Illuminate\Http\Request;
use Carbon\Carbon;


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
            'date' => 'nullable|date', // date optionnelle
        ]);

        $now = Carbon::now();
        $data['created_at'] = $now;

        // si la date n'est pas envoyée, on met la date du jour
        if (empty($data['date'])) {
            $data['date'] = $now->toDateString();
        }

        return StocksReturnableProduct::create($data);
    }



    public function stocksReturnableProductsList()
    {
        return StocksReturnableProduct::orderBy('id', 'desc')->get();
    }
}
