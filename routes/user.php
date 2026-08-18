<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Company\PackController;
use App\Http\Controllers\HR\HRController;
use App\Http\Controllers\Employee\EmployeeController;

/*
|--------------------------------------------------------------------------
| Routes Utilisateur
|--------------------------------------------------------------------------
|
| Ces routes gèrent les différents types d'utilisateurs de l'application
| (Entreprise, RH/Paie, Employé)
|
*/

// Routes pour les entreprises
Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
    Route::get('/dashboard', [CompanyController::class, 'dashboard'])->name('dashboard');
    Route::get('/plan/pricing', [CompanyController::class, 'pricing'])->name('plan.pricing');

    // Routes pour la gestion des packs/abonnements
    Route::get('/packs', [PackController::class, 'index'])->name('packs.index');
    Route::get('/packs/plans', [PackController::class, 'getPlans'])->name('packs.plans');
    Route::post('/packs/payment/{plan}', [PackController::class, 'showPayment'])->name('packs.payment');
    Route::post('/packs/subscribe', [PackController::class, 'subscribe'])->name('packs.subscribe');
    Route::post('/packs/renew', [PackController::class, 'renew'])->name('packs.renew');
    Route::post('/packs/upgrade', [PackController::class, 'upgrade'])->name('packs.upgrade');
    Route::get('/packs/history', [PackController::class, 'history'])->name('packs.history');

    // Routes pour les détails et actions sur les commandes
    Route::get('/packs/orders/{order}/details', [PackController::class, 'getOrderDetails'])->name('packs.orders.details');
    Route::post('/packs/orders/{order}/cancel', [PackController::class, 'cancelOrder'])->name('packs.orders.cancel');
    Route::get('/packs/orders/{order}/invoice', [PackController::class, 'downloadInvoice'])->name('packs.orders.invoice');
    Route::get('/packs/orders/{order}', [PackController::class, 'showOrderDetails'])->name('packs.orders.show');
});

// Routes pour les RH/Paie
Route::middleware(['auth', 'maintenance'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', [HRController::class, 'dashboard'])->name('dashboard');
});

// Routes pour les employés
Route::middleware(['auth', 'maintenance'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
});

