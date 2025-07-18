<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages/front/home');
});

require __DIR__.'/auth.php';

Route::get('/home', function () {
    return view('pages/front/home');
});

Route::get('/dashboardAdmin', function () {
    return view('pages/back/dashboardAdmin');
});
