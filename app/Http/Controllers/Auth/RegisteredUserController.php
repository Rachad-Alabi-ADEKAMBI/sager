<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class RegisteredUserController extends Controller
{
    public function store(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(
                '<script>alert("Erreur : ' . addslashes($error) . '"); history.back();</script>',
                200
            )->header('Content-Type', 'text/html');
        }

        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        event(new Registered($user));
      //  Auth::login($user);

        return response(
            '<script>alert("Inscription réussie"); window.location.href="/sellers";</script>',
            200
        )->header('Content-Type', 'text/html');
    }
}
    