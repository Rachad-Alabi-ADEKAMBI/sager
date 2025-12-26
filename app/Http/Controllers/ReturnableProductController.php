<?php

namespace App\Http\Controllers;

use App\Models\ReturnableProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnableProductController extends Controller
{
    public function index()
    {
        // Vérifie que l'utilisateur est connecté et est admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        // Retourne la vue avec les clients
        return view('pages/back/admin/returnableProducts');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'comment' => 'nullable|string',
        ]);

        $operation = ReturnableProduct::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Opération de remise créée avec succès',
            'data' => $operation
        ], 201);
    }
}
