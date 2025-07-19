<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/', function () {
    return view('pages/front/home');
});

require __DIR__.'/auth.php';

Route::get('/home', function () {
    return view('pages/front/home');
})->name('home');

Route::get('/dashboardAdmin', function () {
    return view('pages/back/admin/dashboardAdmin');
})->name('dashboardAdmin');


Route::get('/stocks', function () {
    return view('pages/back/admin/stocks');
})->name('stocks');

Route::get('/sellers', function () {
    $users = \App\Models\User::all(); // Fetch all users for the sellers page
    return view('pages/back/admin/sellers', compact('users'));
})->name('sellers');

Route::get('/sales', function () {
    return view('pages/back/admin/sales');
})->name('sales');

Route::post('/users', [RegisteredUserController::class, 'store'])->name('users.store');
