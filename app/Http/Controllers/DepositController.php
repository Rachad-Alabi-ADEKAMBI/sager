<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    // Liste des dépôts
    public function index()
    {
        return response()->json(Deposit::all());
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
