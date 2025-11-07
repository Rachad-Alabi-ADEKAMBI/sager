<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Claim;
use Illuminate\Support\Facades\Auth;

class ClaimController extends Controller
{
    /**
     * Ajouter  la créance d'un client
     */

    public function index()
    {
        // Vérifie que l'utilisateur est connecté et est admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        // Retourne la vue avec les clients
        return view('pages/back/admin/claims');
    }

    public function add(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0',
            'comment' => 'nullable|string',
        ]);

        $claim = Claim::create([
            'client_id' => $request->client_id,
            'amount' => $request->amount,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Créance ajoutée avec succès.',
            'claim' => $claim,
        ]);
    }


    public function delete($id)
    {
        $claim = Claim::find($id);

        if (!$claim) {
            return response()->json([
                'success' => false,
                'message' => 'Créance non trouvée.'
            ], 404);
        }

        $claim->delete();

        return response()->json([
            'success' => true,
            'message' => 'Créance supprimée avec succès.'
        ]);
    }



    /**
     * Liste des claims en JSON
     */
    public function list()
    {
        $claims = Claim::with('client')->get()->map(function ($claim) {
            return [
                'id' => $claim->id,
                'client_id' => $claim->client_id,
                'client_name' => $claim->client->name ?? null,
                'client_phone' => $claim->client->phone ?? null,
                'debt_amount' => $claim->amount,
                'comment' => $claim->comment,
                'created_at' => $claim->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $claim->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($claims);
    }
}
