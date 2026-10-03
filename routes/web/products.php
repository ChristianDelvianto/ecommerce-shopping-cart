<?php

use App\Http\Controllers\Product\IndexController;
use App\Http\Controllers\Product\ShowController;
use Illuminate\Support\Facades\Route;

Route::prefix('/products')
->as('products.')
->group(function () {
    Route::get('/', IndexController::class)->name('index');
    Route::get('/{product}', ShowController::class)->name('show');
});
