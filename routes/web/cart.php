<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('/cart')
->as('cart.')
->middleware(['auth', 'role:user'])
->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/checkout', [CartController::class, 'checkoutItems'])->name('checkout');
    Route::delete('/items/{cart_item}', [CartController::class, 'removeCartItem'])->name('destroy');
    Route::put('/products/{product}', [CartController::class, 'upsertProductToCart'])->name('upsert');
});
