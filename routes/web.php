<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [ProductController::class, 'index']);
Route::resource('products', ProductController::class)
    ->only([
        'store',
        'update',
        'destroy'
    ]);
