<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SaleController;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('pages/front/home');
});

require __DIR__.'/auth.php';

Route::get('/home', function () {
    return view('pages/front/home');
})->name('home');


/*admin routes*/

Route::get('/dashboardAdmin', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login'); // Redirige vers login
    }

      $products = Product::all();

    return view('pages/back/admin/dashboardAdmin', compact('products'));
})->name('dashboardAdmin');


Route::get('/stocks', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }

    $products = Product::all();
    return view('pages/back/admin/stocks', compact('products'));
})->name('stocks');


Route::post('/products', [ProductController::class, 'store'])
    ->middleware('auth')
    ->name('products.store');


Route::get('/sellers', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }
    $users = User::all();
    return view('pages/back/admin/sellers', compact('users'));
})->name('sellers');

Route::get('/sales', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }
    return view('pages/back/admin/sales');
})->name('sales');

Route::get('/settingsAdmin', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }
    return view('pages/back/admin/settingsAdmin');
})->name('settingsAdmin');


Route::post('/users', [RegisteredUserController::class, 'store'])->name('users.store');


/*end admin routes*/



/*routes for seller*/
    Route::get('/sale', function () {
    if (!Auth::check() || Auth::user()->role !== 'seller') {
        return redirect()->route('login');
    }
      $products = Product::all();
    return view('pages/back/seller/sale', compact('products'));
})->name('sale');

Route::post('/sale', function (Request $request) {
    // Pour debug : afficher les données reçues en JSON
    return response()->json($request->all());
})->name('sale');

 Route::get('/dashboard', function () {
    if (!Auth::check() || Auth::user()->role !== 'seller') {
        return redirect()->route('login');
    }
    return view('pages/back/seller/dashboard');
})->name('dashboard');

   Route::get('/settings', function () {
    if (!Auth::check() || Auth::user()->role !== 'seller') {
        return redirect()->route('login');
    }
    return view('pages/back/seller/settings');
})->name('settings');

/* end seller routes*/


/* api routes */
     Route::get('/productsList', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $products = Product::all();
    return response()->json($products);
})->name('productsList');


  Route::get('/product/{id}', function ($id) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $product = Product::find($id);
    if ($product) {
        return response()->json($product->toArray());
    } else {
        return response()->json(['error' => 'Product not found'], 404);
    }
})->name('product');

/* end api routes*/








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

    Route::get('/reset_password', function () {
    return view('pages/front/reset_password');
})->middleware('guest')->name('reset_password');


Route::post('/vente', function (\Illuminate\Http\Request $request) {
    dd($request->all());
})->name('vente.store');



Route::post('/sales/store', [SaleController::class, 'store'])->name('sales.store');


/*
Route::get('/sale', [App\Http\Controllers\SaleController::class, 'create'])->name('sale');
Route::post('/sale', [App\Http\Controllers\SaleController::class, 'store'])->name('sales.store');
*/

