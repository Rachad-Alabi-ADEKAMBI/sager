<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // "confirmed" attend un champ password_confirmation
        ]);

        $user = Auth::user();

        // Vérifie que l'ancien mot de passe est correct
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Ancien mot de passe incorrect']);
        }

        // Met à jour le mot de passe
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('status', 'Mot de passe mis à jour avec succès.');
    }
}

