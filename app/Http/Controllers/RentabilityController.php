<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class RentabilityController extends Controller
{

    public function getRentability(): \Illuminate\Http\JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $sales = Sale::with('products')
            ->where('status', 'done')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($sales->isEmpty()) {
            return response()->json([]);
        }

        $startDate = Carbon::parse($sales->first()->created_at)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Initialisation des jours avec profit à 0
        $days = [];
        for ($current = $startDate->copy(); $current->lte($endDate); $current->addDay()) {
            $days[$current->format('Y-m-d')] = 0;
        }

        foreach ($sales as $sale) {
            $day = $sale->created_at->format('Y-m-d');

            foreach ($sale->products as $product) {
                if (!$product) continue;

                $quantity = (float) $product->pivot->quantity;

                if ((int) $product->is_depositable === 1) {
                    // Récupérer price_type depuis la table sale_products, défaut "refill"
                    $saleProduct = \App\Models\SaleProduct::where('sale_id', $sale->id)
                        ->where('product_id', $product->id)
                        ->first();

                    $priceType = $saleProduct->price_type ?? 'refill';

                    $profitUnit = match ($priceType) {
                        'refill' => (float) ($product->benefit_refill ?? 0),
                        'deposit' => (float) ($product->benefit_deposit ?? 0),
                        'both' => (float) ($product->benefit_deposit_refill ?? 0),
                        default => 0
                    };

                    $profit = $profitUnit * $quantity;
                } else {
                    $profit = ((float) $product->pivot->price - (float) $product->purchase_price) * $quantity;
                }

                $days[$day] += $profit;
            }
        }

        $rentability = collect($days)
            ->map(fn($total, $day) => ['day' => $day, 'total_profit' => (float) $total])
            ->sortByDesc('day')
            ->values();

        return response()->json($rentability);
    }


    public function getDailyRentability(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $date = $request->query('date');
        if (!$date) {
            return response()->json(['error' => 'Date parameter is required'], 400);
        }

        try {
            $targetDate = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format. Use dd/mm/yyyy'], 400);
        }

        // Récupérer toutes les ventes "done" du jour avec leurs produits
        $sales = Sale::with('products')
            ->whereDate('created_at', $targetDate)
            ->where('status', 'done')
            ->get();

        $details = $sales->map(function ($sale) {
            return [
                'sale_id' => $sale->id,
                'client_name' => $sale->buyer_name,
                'client_phone' => $sale->buyer_phone,
                'products' => $sale->products->map(function ($product) use ($sale) {
                    $quantity = (float) $product->pivot->quantity;
                    $salePrice = (float) $product->pivot->price;
                    $purchasePrice = (float) $product->purchase_price;

                    $profit = 0;

                    if ((int) $product->is_depositable === 1) {
                        // Récupérer price_type depuis sale_products, défaut "refill"
                        $saleProduct = \App\Models\SaleProduct::where('sale_id', $sale->id)
                            ->where('product_id', $product->id)
                            ->first();

                        $priceType = $saleProduct->price_type ?? 'refill';

                        $profitUnit = match ($priceType) {
                            'refill' => (float) ($product->benefit_refill),
                            'deposit' => (float) ($product->benefit_deposit),
                            'both' => (float) ($product->benefit_deposit_refill),
                            default => 0
                        };

                        $profit = $profitUnit * $quantity;
                    } else {
                        $profit = ($salePrice - $purchasePrice) * $quantity;
                    }

                    return [
                        'product_name' => $product->name,
                        'quantity' => $quantity,
                        'price' => $salePrice,
                        'purchase_price' => $purchasePrice,
                        'profit' => $profit
                    ];
                }),
                'total_sale' => $sale->total,
            ];
        });

        return response()->json($details);
    }
}
