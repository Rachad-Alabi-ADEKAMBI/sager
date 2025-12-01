<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\StockDeposit;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Product;

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

    //ajouter du stock de consignation
    //ajouter du stock de consignation
    public function addDeposit(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:deposits,product_id',
            'quantity'   => 'required|numeric|min:0.01',
            'comment'    => 'nullable|string',
        ]);

        try {
            $deposit = Deposit::where('product_id', $data['product_id'])->first();

            if (!$deposit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé dans les dépôts.',
                ], 404);
            }

            // Récupérer le nom du produit
            $product = Product::find($deposit->product_id);

            $initial_quantity = (float) $deposit->quantity;
            $added_quantity   = (float) $data['quantity'];
            $final_quantity   = $initial_quantity + $added_quantity;

            $deposit->update([
                'quantity' => $final_quantity,
            ]);

            StockDeposit::create([
                'product_id'    => $deposit->product_id,
                'initial_stock' => $initial_quantity,
                'quantity'      => $added_quantity,
                'final_stock'   => $final_quantity,
                'comment'       => $data['comment'] ?? 'Recharge de stock',
            ]);

            // Notification consignation avec nom du produit
            Notification::create([
                'description' =>
                'Ajout de stock en consignation pour "' . $product->name . '" (+ ' . $added_quantity . ').',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock mis à jour et historique enregistré.',
                'data' => [
                    'product_id'      => $deposit->product_id,
                    'initial_quantity' => $initial_quantity,
                    'added_quantity'   => $added_quantity,
                    'final_quantity'   => $final_quantity,
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


    //retirer du stock de consignation
    //retirer du stock de consignation
    public function removeDeposit(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:deposits,product_id',
            'quantity'   => 'required|numeric|min:0.01',
            'comment'    => 'nullable|string',
        ]);

        try {
            $deposit = Deposit::where('product_id', $data['product_id'])->first();

            if (!$deposit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé dans les dépôts.',
                ], 404);
            }

            // Récupérer le nom du produit
            $product = Product::find($deposit->product_id);

            $initial_quantity = (float) $deposit->quantity;
            $removed_quantity = (float) $data['quantity'];

            if ($removed_quantity > $initial_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quantité insuffisante dans le stock.',
                ], 400);
            }

            $final_quantity = $initial_quantity - $removed_quantity;

            $deposit->update([
                'quantity' => $final_quantity,
            ]);

            StockDeposit::create([
                'product_id'    => $deposit->product_id,
                'initial_stock' => $initial_quantity,
                'quantity'      => -$removed_quantity,
                'final_stock'   => $final_quantity,
                'comment'       => $data['comment'] ?? 'Retrait de stock',
            ]);

            // Notification consignation avec nom du produit
            Notification::create([
                'description' =>
                'Retrait de stock en consignation pour "' . $product->name . '" (- ' . $removed_quantity . ').',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock retiré et historique enregistré.',
                'data' => [
                    'product_id'      => $deposit->product_id,
                    'initial_quantity' => $initial_quantity,
                    'removed_quantity' => $removed_quantity,
                    'final_quantity'   => $final_quantity,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du retrait du stock.',
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
