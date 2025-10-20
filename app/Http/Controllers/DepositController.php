<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\StockDeposit;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    // Liste des dépôts
    public function index()
    {
        return response()->json(Deposit::all());
    }

    public function depositsList()
    {
        try {
            $deposits = Deposit::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $deposits,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des dépôts.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Créer un dépôt
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_name' => 'required|string',
            'initial_quantity' => 'nullable|numeric',
            'quantity' => 'required|numeric',
            'final_quantity' => 'nullable|numeric',
            'comment' => 'nullable|string',
        ]);

        $deposit = Deposit::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Dépôt créé avec succès.',
            'deposit' => $deposit,
        ]);
    }


    // Ajouter ou recharger un dépôt existant
    public function addDeposit(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:deposits,product_id',
            'quantity'   => 'required|numeric|min:0.01',
            'comment'    => 'nullable|string',
        ]);

        try {
            // Récupérer le dépôt existant
            $deposit = Deposit::where('product_id', $data['product_id'])->first();

            if (!$deposit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé dans les dépôts.',
                ], 404);
            }

            $initial_quantity = (float) $deposit->quantity;
            $added_quantity = (float) $data['quantity'];
            $final_quantity = $initial_quantity + $added_quantity;

            // Mettre à jour la quantité dans deposits
            $deposit->update([
                'quantity' => $final_quantity,
            ]);

            // Enregistrer l'historique dans stocks_deposits
            StockDeposit::create([
                'product_id'    => $deposit->product_id,
                'initial_stock' => $initial_quantity,
                'quantity'      => $added_quantity,
                'final_stock'   => $final_quantity,
                'comment'       => $data['comment'] ?? 'Recharge de stock',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock mis à jour et historique enregistré.',
                'data' => [
                    'product_id'      => $deposit->product_id,
                    'initial_quantity' => $initial_quantity,
                    'added_quantity'  => $added_quantity,
                    'final_quantity'  => $final_quantity,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l’ajout du stock.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Afficher un dépôt spécifique
    public function show($id)
    {
        $deposit = Deposit::findOrFail($id);
        return response()->json($deposit);
    }

    // Mettre à jour un dépôt
    public function update(Request $request, $id)
    {
        $deposit = Deposit::findOrFail($id);

        $data = $request->validate([
            'product_name' => 'sometimes|string',
            'initial_quantity' => 'sometimes|numeric',
            'quantity' => 'sometimes|numeric',
            'final_quantity' => 'sometimes|numeric',
            'comment' => 'nullable|string',
        ]);

        $deposit->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Dépôt mis à jour avec succès.',
            'deposit' => $deposit,
        ]);
    }

    // Supprimer un dépôt
    public function destroy($id)
    {
        $deposit = Deposit::findOrFail($id);
        $deposit->delete();

        return response()->json(['message' => 'Dépôt supprimé avec succès.']);
    }
}
