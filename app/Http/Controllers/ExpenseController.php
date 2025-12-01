<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;
use App\Models\Notification;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        return view('pages/back/admin/expenses');
    }

    //liste des depenses
    public function expensesList()
    {
        $expenses = Expense::orderBy('date', 'desc')->get();

        // On transforme chaque dépense pour s'assurer que tous les champs existent et ont un type correct
        $expensesData = $expenses->map(function ($expense) {
            return [
                'id'          => $expense->id,
                'type'        => $expense->type ?? '',
                'amount'      => (float) $expense->amount,
                'description' => $expense->description ?? '',
                'date'        => $expense->date ?? null,
                'created_at'  => $expense->created_at?->toDateTimeString(),
                'updated_at'  => $expense->updated_at?->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $expensesData
        ]);
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
        $data = $request->validate([
            'type'        => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'date'        => 'required|date',
        ]);

        $expense = Expense::create($data);

        // Notification
        Notification::create([
            'description' => 'Dépense ajoutée : ' . $data['type'] . '. Montant : ' . $data['amount'] . ' CFA.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dépense enregistrée.',
            'data'    => $expense
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['success' => false, 'message' => 'Dépense introuvable.'], 404);
        }

        $data = $request->validate([
            'type'        => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'date'        => 'required|date',
        ]);

        $expense->update($data);

        // Notification
        Notification::create([
            'description' => 'Dépense modifiée : ' . $data['type'] . '. Nouveau montant : ' . $data['amount'] . ' CFA.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dépense mise à jour.',
            'data'    => $expense
        ]);
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


    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['success' => false, 'message' => 'Dépense introuvable.'], 404);
        }

        $expense->delete();

        // Notification
        Notification::create([
            'description' => 'Dépense supprimée : ' . $expense->type . '. Montant : ' . $expense->amount . ' CFA.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dépense supprimée.'
        ]);
    }
}
