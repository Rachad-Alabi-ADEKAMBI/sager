<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Claim;
use App\Models\ClaimsPayment;

class ClaimsPaymentController extends Controller
{
    /**
     * Ajouter un paiement pour une créance
     */
    public function addPayment(Request $request)
    {
        $request->validate([
            'claim_id' => 'required|exists:claims,id',
            'amount' => 'required|numeric|min:0',
            'comment' => 'nullable|string',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $claim = Claim::find($request->claim_id);

        $payment = ClaimsPayment::create([
            'claim_id' => $claim->id,
            'amount' => $request->amount,
            'comment' => $request->comment,
            'payment_method' => $request->payment_method,
        ]);

        return response()->json([
            'success' => true,
            'payment' => $payment,
        ]);
    }

    /**
     * Liste des paiements en JSON
     */
    public function list()
    {
        $payments = ClaimsPayment::with('claim.client')->get();

        return response()->json($payments);
    }
}
