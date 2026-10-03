<?php

use App\Http\Controllers\Order\IndexController;
use Illuminate\Support\Facades\Route;

Route::prefix('/orders')
->as('orders.')
->middleware(['auth', 'role:user'])
->group(function () {
    Route::get('/', IndexController::class)->name('index');
});
