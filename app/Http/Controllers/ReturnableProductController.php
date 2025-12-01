<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReturnableProduct;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

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
}
