<?php

use Illuminate\Support\Facades\Route;
use Modules\Pointeuses\Http\Controllers\PointeusesController;

// Routes pour les entreprises
Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
    //Pointeuses
    Route::get('pointeuses/index', [PointeusesController::class, 'index'])->name('pointeuses.index');
    Route::get('pointeuses/create', [PointeusesController::class, 'create'])->name('pointeuses.create');
    Route::post('pointeuses/store', [PointeusesController::class, 'store'])->name('pointeuses.store');
    Route::get('pointeuses/show/{id}', [PointeusesController::class, 'show'])->name('pointeuses.show');
    Route::get('pointeuses/edit/{id}', [PointeusesController::class, 'edit'])->name('pointeuses.edit');
    Route::put('pointeuses/update/{id}', [PointeusesController::class, 'update'])->name('pointeuses.update');
    Route::delete('pointeuses/destroy/{id}', [PointeusesController::class, 'destroy'])->name('pointeuses.destroy');
});
