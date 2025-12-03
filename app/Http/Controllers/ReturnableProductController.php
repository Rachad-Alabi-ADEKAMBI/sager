<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReturnableProduct;
use App\Models\StocksReturnableProduct;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Client; // modèle contenant les clients



class ReturnableProductController extends Controller
{
    /*
    public function returnPackaging(Request $request)
    {
        $data = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $returnable = ReturnableProduct::where('sale_id', $data['sale_id'])
            ->where('product_id', $data['product_id'])
            ->firstOrFail();

        $returnable->quantity_returned += $data['quantity'];
        $returnable->save();

        // Notification
        Notification::create([
            'description' => 'Retour d’emballage pour le produit ' . $returnable->product->name .
                ' de la facture #' . $returnable->sale_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Retour d’emballage enregistré.',
            'data' => $returnable
        ]);
    }
        */

    public function index()
    {
        // Vérifie que l'utilisateur est connecté et est admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        // Retourne la vue avec les clients
        return view('pages/back/admin/returnableProducts');
    }

    public function returnableProductsList()
    {
        $products = DB::table('returnable_products')
            ->leftJoin('products', 'returnable_products.product_id', '=', 'products.id')
            ->leftJoin('sales', 'returnable_products.sale_id', '=', 'sales.id')
            ->select(
                'returnable_products.id',
                'returnable_products.sale_id',
                'returnable_products.product_id',
                'products.name as product_name',
                'sales.buyer_name as client_name',
                'returnable_products.quantity_purchased',
                'returnable_products.quantity_returned',
                'returnable_products.created_at',
                'returnable_products.updated_at'
            )
            ->orderBy('returnable_products.id', 'desc')
            ->get();

        return response()->json($products);
    }

    public function handleReturn(Request $request, $id)
    {
        $data = $request->validate([
            'quantity_returned' => 'required|integer|min:1',
            'date' => 'nullable|date', // date optionnelle
        ]);

        // Récupération du produit retournable
        $item = ReturnableProduct::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Élément introuvable'
            ], 404);
        }

        // Quantité max possible
        $maxReturn = $item->quantity_purchased - $item->quantity_returned;

        if ($data['quantity_returned'] > $maxReturn) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de retourner plus de $maxReturn unités"
            ], 400);
        }

        // Mise à jour du retour
        $item->quantity_returned += $data['quantity_returned'];
        $item->save();

        // Récupérer le client correspondant au buyer_name
        $buyerName = $item->sale->buyer_name ?? null;
        $buyerId = null;
        if ($buyerName) {
            $buyer = Client::where('name', $buyerName)->first();
            if ($buyer) {
                $buyerId = $buyer->id;
            }
        }

        // Insertion dans l’historique (StocksReturnableProduct)
        $now = Carbon::now();
        StocksReturnableProduct::create([
            'sale_id'             => $item->sale_id,
            'product_id'          => $item->product_id,
            'buyer_id'            => $buyerId,
            'quantity_purchased'  => $item->quantity_purchased,
            'quantity_returned'   => $item->quantity_returned,
            'date'                => $data['date'] ?? $now->toDateString(),
            'created_at'          => $now,
        ]);

        // Création de la notification
        Notification::create([
            'description' => 'Retour d’emballage pour le produit ' . ($item->product->name ?? 'Produit inconnu') .
                ' de la facture #' . $item->sale_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Retour enregistré avec succès',
            'data'    => $item
        ]);
    }


    public function destroy($id)
    {
        $item = ReturnableProduct::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Élément introuvable'
            ], 404);
        }

        try {
            // Créer la notification avant suppression
            Notification::create([
                'description' => 'Suppression du produit retournable: ' . ($item->product->name ?? 'Produit inconnu') .
                    ' de la facture #' . $item->sale_id
            ]);

            // Supprimer l'enregistrement
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Enregistrement supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
