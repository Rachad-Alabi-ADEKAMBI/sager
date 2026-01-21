<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use App\Models\StocksReturnableProduct;
use App\Models\ReturnableProductsList;

class StocksReturnableProductController extends Controller
{
    public function index()
    {
        $returns = StocksReturnableProduct::query()
            ->orderBy('date', 'desc')
            ->get([
                'id',
                'returnable_product_id',
                'product_id',
                'quantity_returned',
                'date',
                'comment',
                'created_at',
                'updated_at'
            ]);

        return response()->json($returns);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'returnable_product_id' => 'required|exists:returnable_products,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_returned' => 'required|numeric|min:0.01',
            'date' => 'nullable|date',
            'comment' => 'nullable|string',
        ]);

        $date = $data['date'] ?? now()->toDateString();
        $rows = [];

        DB::beginTransaction();

        try {
            foreach ($data['items'] as $item) {

                // Ligne produit dans returnable_products_list
                $returnableProduct = ReturnableProductsList::where(
                    'returnable_product_id',
                    $data['returnable_product_id']
                )
                    ->where('product_id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $qtyToReturn = round($item['quantity_returned'], 2);
                $qtyAvailable =
                    $returnableProduct->quantity_given
                    - $returnableProduct->quantity_returned;

                // Sécurité quantité
                if ($qtyToReturn > $qtyAvailable) {
                    abort(422, 'Quantité retournée supérieure à la quantité disponible');
                }

                // 1️⃣ Enregistrer le retour (historique)
                $rows[] = StocksReturnableProduct::create([
                    'returnable_product_id' => $data['returnable_product_id'],
                    'product_id' => $item['product_id'],
                    'quantity_returned' => $qtyToReturn,
                    'date' => $date,
                    'comment' => $data['comment'] ?? null,
                ]);

                // 2️⃣ Mettre à jour la quantité retournée
                $returnableProduct->increment('quantity_returned', $qtyToReturn);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Retours enregistrés avec succès',
                'data' => $rows,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
