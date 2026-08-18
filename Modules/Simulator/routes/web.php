<?php

use Illuminate\Support\Facades\Route;
use Modules\Simulator\Http\Controllers\SimulatorController;

Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
    //Dashboard
    Route::get('simulator/dashboard', [SimulatorController::class, 'dashboard'])->name('simulator.dashboard');
    
    // Simulateur
    Route::get('simulator/index', [SimulatorController::class, 'index'])->name('simulator.index');
    Route::get('simulator/create', [SimulatorController::class, 'create'])->name('simulator.create');
    Route::post('simulator/store', [SimulatorController::class, 'store'])->name('simulator.store');
    Route::get('simulator/show/{id}', [SimulatorController::class, 'show'])->name('simulator.show');
    Route::get('simulator/edit/{id}', [SimulatorController::class, 'edit'])->name('simulator.edit');
    Route::put('simulator/update/{id}', [SimulatorController::class, 'update'])->name('simulator.update');
    Route::delete('simulator/destroy/{id}', [SimulatorController::class, 'destroy'])->name('simulator.destroy');
});
