<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReturnableProduct;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnableProductController extends Controller
{
    public function returnPackaging(Request $request)
    {
        $data = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $returnable = ReturnableProduct::where('sale_id', $data['sale_id'])
            ->where('product_id', $data['product_id'])
            ->firstOrFail();

        $returnable->quantity_returned += $data['quantity'];
        $returnable->save();

        // Notification
        Notification::create([
            'description' => 'Retour d’emballage pour le produit ' . $returnable->product->name .
                ' de la facture #' . $returnable->sale_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Retour d’emballage enregistré.',
            'data' => $returnable
        ]);
    }

    public function index()
    {
        // Vérifie que l'utilisateur est connecté et est admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        // Retourne la vue avec les clients
        return view('pages/back/admin/returnableProducts');
    }

    public function returnableProductsList()
    {
        $products = DB::table('returnable_products')
            ->leftJoin('products', 'returnable_products.product_id', '=', 'products.id')
            ->leftJoin('sales', 'returnable_products.sale_id', '=', 'sales.id')
            ->select(
                'returnable_products.id',
                'products.name as product_name',
                'sales.buyer_name as client_name',
                'returnable_products.quantity_purchased',
                'returnable_products.quantity_returned',
                'returnable_products.created_at',
                'returnable_products.updated_at'
            )
            ->orderBy('returnable_products.id', 'desc')
            ->get();

        return response()->json($products);
    }
}
