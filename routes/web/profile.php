<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/**
 * Laravel breeze default profile routes.
 */
Route::prefix('/profile')
->as('profile.')
->middleware(['auth'])
->group(function () {
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('update');
});
