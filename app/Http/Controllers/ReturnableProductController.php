<?php

namespace App\Http\Controllers;

use App\Models\ReturnableProduct;
use App\Models\ReturnableProductsList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use Illuminate\Support\Facades\DB;


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
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_given' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ Création de la remise
            $operation = ReturnableProduct::create([
                'client_id' => $data['client_id'],
                'date' => $data['date'],
                'comment' => $data['comment'] ?? null,
            ]);

            // 2️⃣ Enregistrement des produits remis
            foreach ($data['items'] as $item) {
                ReturnableProductsList::create([
                    'returnable_product_id' => $operation->id,
                    'product_id' => $item['product_id'],
                    'quantity_given' => round($item['quantity_given'], 2),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Remise enregistrée avec ses produits',
                'data' => $operation->load('products')
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la remise',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function getReturnableProductsTransactions()
    {
        $transactions = ReturnableProduct::with('client:id,name') // récupère seulement id et name du client
            ->orderBy('date', 'desc')
            ->get([
                'id',
                'client_id',
                'date',
                'reference',
                'comment',
                'created_at',
                'updated_at'
            ]);

        // Ajouter un champ client_name directement dans les résultats
        $transactions->transform(function ($item) {
            $item->client_name = $item->client->name ?? null;
            unset($item->client); // optionnel : enlève l'objet client
            return $item;
        });

        return response()->json($transactions);
    }
}
