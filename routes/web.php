<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('/profile')
->as('profile.')
->middleware(['auth'])
->group(function () {
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('update');
});

Route::prefix('/products')
->as('products.')
->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
});

Route::prefix('/cart')
->as('cart.')
->middleware(['auth', 'role:user'])
->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/checkout', [CartController::class, 'checkoutItems'])->name('checkout');
    Route::delete('/items/{cart_item}', [CartController::class, 'removeCartItem'])->name('destroy');
    Route::put('/products/{product}', [CartController::class, 'upsertProductToCart'])->name('upsert');
});

Route::prefix('/orders')
->as('orders.')
->middleware(['auth', 'role:user'])
->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
});

require __DIR__.'/auth.php';
