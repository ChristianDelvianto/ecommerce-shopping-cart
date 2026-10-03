<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('/orders')
->as('orders.')
->middleware(['auth', 'role:user'])
->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
});
