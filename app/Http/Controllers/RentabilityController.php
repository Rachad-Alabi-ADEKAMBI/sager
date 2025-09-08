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


}
