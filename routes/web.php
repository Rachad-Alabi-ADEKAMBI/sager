<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


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


Route::get('/dashboard', function () {
    return view('pages/back/seller/dashboard');
})->name('dashboard');


Route::get('/stocks', function () {
    $products = Product::all();
    return view('pages/back/admin/stocks', compact('products'));
})->name('stocks');

Route::post('/products', [ProductController::class, 'store'])->name('products.store');




Route::get('/sellers', function () {
    $users = \App\Models\User::all(); // Fetch all users for the sellers page
    return view('pages/back/admin/sellers', compact('users'));
})->name('sellers');

Route::get('/sales', function () {
    return view('pages/back/admin/sales');
})->name('sales');

Route::get('/sale', function () {
    return view('pages/back/seller/sale');
})->name('sale');

Route::post('/users', [RegisteredUserController::class, 'store'])->name('users.store');



Route::post('/logout', function () {
    Auth::logout();
    return redirect('/home');
})->name('logout');


Route::get('/login', function () {
    return view('pages/front/login');
})->middleware('guest')->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');
