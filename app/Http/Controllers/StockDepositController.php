<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockDeposit;

class StockDepositController extends Controller
{
    // Récupérer tous les stocks_deposits
    public function index()
    {
        try {
            $stocksDeposits = StockDeposit::with('product')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stocksDeposits
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des stocks de dépôts.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Créer un enregistrement manuel
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'initial_stock' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'final_stock' => 'required|numeric|min:0',
            'comment' => 'nullable|string',
        ]);

        try {
            $stockDeposit = StockDeposit::create($data);

            return response()->json([
                'success' => true,
                'data' => $stockDeposit,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du stock de dépôt.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function history($product_id)
    {
        // Vérifie que le produit existe
        $depositsHistory = StockDeposit::where('product_id', $product_id)
            ->orderBy('created_at', 'desc')
            ->get(['initial_stock', 'quantity', 'final_stock', 'comment', 'created_at']);

        if ($depositsHistory->isEmpty()) {
            return response()->json([
                'message' => 'Aucun historique de dépôt trouvé pour ce produit.',
                'history' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Historique des dépôts récupéré avec succès.',
            'history' => $depositsHistory
        ], 200);
    }



    // Supprimer un stock_deposit
    public function destroy($id)
    {
        try {
            $stockDeposit = StockDeposit::findOrFail($id);
            $stockDeposit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Stock de dépôt supprimé avec succès.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du stock de dépôt.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
