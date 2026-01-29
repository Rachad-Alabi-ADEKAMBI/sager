<?php

namespace App\Http\Controllers;

use App\Models\ReturnableProductsList;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ReturnableProduct;

class ReturnableProductsListController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'returnable_product_id' => 'required|exists:returnable_products,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_given' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            $rows = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // vérifier que le produit est returnable
                if (!$product->isReturnable) continue;

                $rows[] = ReturnableProductsList::create([
                    'returnable_product_id' => $data['returnable_product_id'],
                    'product_id' => $item['product_id'],
                    'quantity_given' => round($item['quantity_given'], 2),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Produits avec emballages enregistrés',
                'data' => $rows
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Échec de l’enregistrement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $list = ReturnableProductsList::query()
            ->join(
                'products',
                'products.id',
                '=',
                'returnable_products_list.product_id'
            )
            ->orderBy('returnable_products_list.created_at', 'desc')
            ->get([
                'returnable_products_list.id',
                'returnable_products_list.returnable_product_id',
                'returnable_products_list.product_id',
                'products.name as product_name',
                'returnable_products_list.quantity_given',
                'returnable_products_list.quantity_returned',
                'returnable_products_list.created_at',
                'returnable_products_list.updated_at',
            ]);

        return response()->json($list);
    }
}
