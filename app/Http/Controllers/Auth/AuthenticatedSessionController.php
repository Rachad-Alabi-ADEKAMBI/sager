<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Vérifie si le compte est banni
        if ($user->status === 'banned') {
            Auth::logout(); // Déconnecte l'utilisateur
            return redirect()->back()->with('error', "Votre compte a été banni, merci de contacter l'administrateur");
        }

        // Redirection selon le rôle
        if ($user->role === 'admin') {
            return redirect()->intended('/dashboardAdmin');
        }

        return redirect()->intended('/dashboard');
    }



    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
