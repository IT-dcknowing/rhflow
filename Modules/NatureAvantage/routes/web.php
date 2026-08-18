<?php

use Illuminate\Support\Facades\Route;
use Modules\NatureAvantage\Http\Controllers\NatureAvantageController;

Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
    Route::resource('avantages', NatureAvantageController::class);
    Route::prefix('avantages')->name('avantages.')->group(function () {
        Route::put('/{avantage}/toggle-status', [NatureAvantageController::class, 'toggleStatus'])->name('toggle-status');
    });
});
