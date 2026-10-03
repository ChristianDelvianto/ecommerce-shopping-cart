<?php

use App\Http\Controllers\Cart\CheckoutController;
use App\Http\Controllers\Cart\DestroyCartItemController;
use App\Http\Controllers\Cart\IndexController;
use App\Http\Controllers\Cart\UpsertProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('/cart')
->as('cart.')
->middleware(['auth', 'role:user'])
->group(function () {
    Route::get('/', IndexController::class)->name('index');
    Route::post('/checkout', CheckoutController::class)->name('checkout');

    Route::prefix('items')
    ->as('items.')
    ->group(function () {
        Route::delete('/{cartItem}', DestroyCartItemController::class)->name('destroy');
        Route::put('/{product}', UpsertProductController::class)->name('upsert');
    });
});
