<?php

use Illuminate\Support\Facades\Route;
use Modules\Impot\Http\Controllers\ImpotController;

// Routes pour les entreprises
Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
    //Dashboard
    Route::get('impots/dashboard', [ImpotController::class, 'dashboard'])->name('impots.dashboard');

    //Impots
    Route::get('impots/index', [ImpotController::class, 'index'])->name('impots.index');
    Route::get('impots/create', [ImpotController::class, 'create'])->name('impots.create');
    Route::post('impots/store', [ImpotController::class, 'store'])->name('impots.store');
    Route::get('impots/show/{id}', [ImpotController::class, 'show'])->name('impots.show');
    Route::get('impots/edit/{id}', [ImpotController::class, 'edit'])->name('impots.edit');
    Route::put('impots/update/{id}', [ImpotController::class, 'update'])->name('impots.update');
    Route::delete('impots/destroy/{id}', [ImpotController::class, 'destroy'])->name('impots.destroy');
});
