<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Vérifie que l'utilisateur est connecté et est admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        // Retourne la vue avec les clients
        return view('pages/back/admin/clients');
    }

    //clients list
    public function clientsList()
    {
        // Vérifie que l'utilisateur est connecté et est admin
        /*  if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
            */

        // Récupère tous les clients
        $clients = Client::all();

        // Retourne les clients en JSON
        return response()->json($clients);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $client = Client::create([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Client créé avec succès.',
            'client' => $client
        ], 200); // <-- Assure-toi que le code HTTP est bien 200
    }


    public function delete($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client non trouvé.'
            ], 404);
        }

        // Supprimer les claims associés au client
        \App\Models\Claim::where('client_id', $client->id)->delete();

        // Supprimer le client
        $client->delete();

        return response()->json([
            'success' => true,
            'message' => 'Client et ses créances supprimés avec succès.'
        ], 200);
    }





    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
