<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Claim;

class ClaimController extends Controller
{
    /**
     * Ajouter  la créance d'un client
     */
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


    /**
     * Liste des claims en JSON
     */
    public function list()
    {
        $claims = Claim::with('client')->get();

        return response()->json($claims);
    }
}
