<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SaleProduct;
use App\Models\Stock;
use App\Models\StockDeposit;
use Illuminate\Support\Carbon;
use App\Models\Notification;
use App\Models\Deposit;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProductController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use App\Models\Client;
use App\Models\Claim;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('products')->get();
        return view('sales.index', compact('sales'));
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_name' => 'required|string',
            'buyer_name' => 'required|string',
            'buyer_phone' => 'nullable|string',
            'payment_method' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.price_type' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $total = collect($validated['products'])->sum(fn($item) => $item['quantity'] * $item['price']);

            $sale = Sale::create([
                'seller_name' => $validated['seller_name'],
                'buyer_name' => $validated['buyer_name'],
                'buyer_phone' => $validated['buyer_phone'],
                'payment_method' => $validated['payment_method'],
                'total' => $total,
                'status' => 'done',
            ]);

            if (strtolower($validated['payment_method']) === 'credit') {
                $client = Client::where('name', $validated['buyer_name'])->first();
                if ($client) {
                    Claim::create([
                        'client_id' => $client->id,
                        'amount' => $total,
                        'comment' => 'Vente N°' . $sale->id . ' à crédit',
                    ]);
                }
            }

            $details = [];

            foreach ($validated['products'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $initial_quantity = $product->quantity;

                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Stock insuffisant pour le produit : {$product->name}");
                }

                $unitPrice = $item['price'];
                $priceType = $item['price_type'] ?? null; // Utilise directement le price_type envoyé par le front

                SaleProduct::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $unitPrice,
                    'price_type' => $priceType,
                ]);

                $product->decrement('quantity', $item['quantity']);
                $final_quantity = $initial_quantity - $item['quantity'];

                Stock::create([
                    'date' => now()->toDateString(),
                    'initial_stock' => $initial_quantity,
                    'label' => 'Vente produit ' . $product->name,
                    'quantity' => $item['quantity'],
                    'final_stock' => $final_quantity,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sale_id' => $sale->id,
                    'seller_name' => $sale->seller_name,
                ]);

                if ($product->is_depositable && $priceType === 'refill') {
                    $comment = 'Facture ' . $sale->id . ' à ' . $sale->buyer_name;

                    $deposit = Deposit::firstOrCreate(
                        ['product_id' => $product->id],
                        ['product_name' => $product->name, 'quantity' => 0]
                    );
                    $deposit->increment('quantity', $item['quantity']);

                    StockDeposit::create([
                        'product_id' => $product->id,
                        'initial_stock' => $deposit->quantity - $item['quantity'],
                        'quantity' => $item['quantity'],
                        'final_stock' => $deposit->quantity,
                        'comment' => $comment,
                    ]);
                }

                $details[] = [
                    'product' => $product->name,
                    'quantity_sold' => $item['quantity'],
                    'price' => $unitPrice,
                    'price_type' => $priceType,
                ];
            }

            Notification::create([
                'description' => 'Facture N°' . $sale->id . '/' . now()->format('m') . '/' . now()->format('y')
                    . '/FR-N pour ' . $sale->buyer_name . ' par ' . $sale->seller_name
                    . '. Total : ' . $total . ' FCFA.',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Vente enregistrée avec succès.',
                'sale_id' => $sale->id,
                'total' => $total,
                'products' => $details,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Échec de l\'enregistrement de la vente.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }




    public function show($id)
    {
        $sale = Sale::with('products')->findOrFail($id);
        return view('sales.show', compact('sale'));
    }

    public function createSale()
    {
        $products = Product::all();
        return view('sales.create', compact('products'));
    }

    public function create()
    {
        $products = Product::all();
        return view('pages.back.seller.sale', compact('products'));
    }


    public function cancel($id)
    {
        DB::beginTransaction();

        try {
            $sale = Sale::findOrFail($id);

            if ($sale->status === 'cancelled') {
                return response()->json([
                    'error' => 'La vente est déjà annulée.'
                ], 400);
            }

            $saleProducts = SaleProduct::where('sale_id', $id)->get();

            foreach ($saleProducts as $item) {
                $product = Product::findOrFail($item->product_id);

                // Restaurer le stock principal
                $product->increment('quantity', $item->quantity);

                Stock::create([
                    'date' => now()->toDateString(),
                    'initial_stock' => $product->quantity - $item->quantity,
                    'label' => 'Annulation facture ' . $sale->id . ' produit ' . $product->name,
                    'quantity' => $item->quantity,
                    'final_stock' => $product->quantity,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sale_id' => $sale->id,
                    'seller_name' => $sale->seller_name,
                ]);

                // Produits consignables
                if ($product->is_depositable && $item->price_type) {
                    $comment = 'Annulation facture ' . $sale->id . ' à ' . $sale->buyer_name;

                    switch ($item->price_type) {
                        case 'refill':
                            $deposit = Deposit::firstOrCreate(
                                ['product_id' => $product->id],
                                ['product_name' => $product->name, 'quantity' => 0]
                            );

                            $deposit->decrement('quantity', $item->quantity);

                            StockDeposit::create([
                                'product_id' => $product->id,
                                'initial_stock' => $deposit->quantity + $item->quantity,
                                'quantity' => $item->quantity,
                                'final_stock' => $deposit->quantity,
                                'comment' => $comment,
                            ]);
                            break;

                        case 'deposit':
                        case 'both':
                            // Le stock a déjà été géré
                            break;
                    }
                }
            }

            $sale->update(['status' => 'cancelled']);

            // Notification systématique
            Notification::create([
                'description' => 'Annulation de la facture N°' . $sale->id . '/' . now()->format('m') . '/' . now()->format('y')
                    . ' pour ' . $sale->buyer_name . ' par ' . $sale->seller_name . '.',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Vente annulée avec succès.',
                'sale_id' => $sale->id,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Échec de l\'annulation de la vente.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
