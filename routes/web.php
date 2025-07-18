<?php

use Illuminate\Support\Facades\Route;

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
    return view('pages/back/admin/sellers');
})->name('sellers');

Route::get('/sales', function () {
    return view('pages/back/admin/sales');
})->name('sales');
