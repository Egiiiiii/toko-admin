<?php

use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index'])->name('home');
// Pastikan baris ini benar (menggunakan {product})
Route::get('/product/{product}', [ShopController::class, 'show'])->name('product.show');