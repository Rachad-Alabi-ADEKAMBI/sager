<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
 public function updatePassword(Request $request)
{
    $validator = \Validator::make($request->all(), [
        'old_password' => 'required',
        'new_password' => 'required|min:8|confirmed', // "confirmed" attend new_password_confirmation
    ]);

    if ($validator->fails()) {
        // Retour immédiat si validation échoue
        return response()->json([
            'status' => 'error',
            'messages' => $validator->errors()->all()
        ]);
    }

    $user = Auth::user();

    if (!Hash::check($request->old_password, $user->password)) {
        // Retour immédiat si ancien mot de passe incorrect
        return response()->json([
            'status' => 'error',
            'messages' => ['Ancien mot de passe incorrect']
        ]);
    }

    // Mise à jour du mot de passe seulement si tout est correct
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json([
        'status' => 'success',
        'messages' => ['Mot de passe mis à jour avec succès.']
    ]);
}


}

