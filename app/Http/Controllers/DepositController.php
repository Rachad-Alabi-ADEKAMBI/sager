<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;


class DepositController extends Controller
{
    //recuperer toutes les consignations
    public function depositsList()
    {
        $deposits = Deposit::orderBy('id', 'desc')->get();
        return response()->json($deposits);
    }

    /**
     * Met à jour le statut d'une consignation (Terminée ou Annulée)
     */
    public function updateDeposit(Request $request, $id)
    {
        // Validation du statut
        $validated = $request->validate([
            'status' => 'required|string|in:En cours,Terminée,Annulée',
        ]);

        // Recherche de la consignation
        $deposit = Deposit::findOrFail($id);

        // Sauvegarde de l'ancien statut
        $oldStatus = $deposit->status;

        // Mise à jour du statut
        $deposit->update([
            'status' => $validated['status'],
        ]);

        // Récupération de la vente associée pour info dans la notification
        $sale = Sale::find($deposit->sale_id);

        // Création de la notification
        Notification::create([
            'description' => 'Le statut de la consignation N°' . $deposit->id
                . ' pour la facture N°' . $deposit->sale_id
                . '/FR-N de ' . ($sale ? $sale->buyer_name : 'client inconnu')
                . ' a été modifié de "' . $oldStatus . '" à "' . $deposit->status . '" par '
                . (Auth::check() ? (Auth::user()->role === 'admin' ? 'admin' : Auth::user()->name) : 'un utilisateur inconnu') . '.',
        ]);

        return response()->json([
            'message' => "Statut de la consignation mis à jour avec succès.",
            'deposit' => $deposit,
        ], 200);
    }
}
