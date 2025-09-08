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

    // Récupérer uniquement les ventes "done"
    $sales = Sale::with('products')
                 ->where('status', 'done')
                 ->orderBy('created_at', 'asc')
                 ->get();

    if ($sales->isEmpty()) {
        return response()->json([]);
    }

    // Déterminer la période complète depuis la première vente jusqu'à aujourd'hui
    $startDate = Carbon::parse($sales->first()->created_at)->startOfDay();
    $endDate = Carbon::now()->endOfDay(); // aujourd'hui inclus

    // Générer tous les jours de la période
    $days = [];
    $current = $startDate->copy();
    while ($current->lte($endDate)) {
        $days[$current->format('Y-m-d')] = 0;
        $current->addDay();
    }

    // Calculer les profits
    foreach ($sales as $sale) {
        $day = $sale->created_at->format('Y-m-d');
        foreach ($sale->products as $product) {
            $purchasePrice = $product->purchase_price;
            $salePrice = $product->pivot->price;
            $quantity = $product->pivot->quantity;

            $profit = ($salePrice - $purchasePrice) * $quantity;
            $days[$day] += $profit;
        }
    }

    // Transformer en collection triée par jour décroissant
    $rentability = collect($days)
        ->map(fn($total, $day) => ['day' => $day, 'total_profit' => $total])
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

    // Convertir la date dd/mm/yyyy en Y-m-d
    try {
        $targetDate = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid date format. Use dd/mm/yyyy'], 400);
    }

    // Récupérer les ventes "done" du jour
    $sales = Sale::with('products')
                 ->whereDate('created_at', $targetDate)
                 ->where('status', 'done')
                 ->get();

    // Construire la réponse
    $details = $sales->map(function($sale) {
        return [
            'sale_id' => $sale->id,
            'client_name' => $sale->buyer_name,
            'client_phone' => $sale->buyer_phone,
            'products' => $sale->products->map(function($product) {
                return [
                    'product_name' => $product->name,
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->pivot->price,
                    'purchase_price' => $product->purchase_price,
                    'profit' => ($product->pivot->price - $product->purchase_price) * $product->pivot->quantity
                ];
            }),
            'total_sale' => $sale->total,
        ];
    });

    return response()->json($details);
}


}
