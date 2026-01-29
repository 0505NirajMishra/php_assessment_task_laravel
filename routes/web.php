<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/fetch', [ProductController::class, 'fetch']);
Route::post('/products/store', [ProductController::class, 'store']);
Route::get('/products/edit/{id}', [ProductController::class, 'edit']);
Route::post('/products/update/{id}', [ProductController::class, 'update']);
Route::delete('/products/delete/{id}', [ProductController::class, 'destroy']);