<?php
 
use Illuminate\Support\Facades\Route;
use Modules\Loans\Http\Controllers\LoansController;
use Modules\Loans\Http\Controllers\LoanPaymentController;

Route::middleware(['auth', 'verified'])->prefix('company')->name('company.')->group(function () {
    // Routes principales pour les prêts
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [LoansController::class, 'index'])->name('index');
        Route::get('create', [LoansController::class, 'create'])->name('create');
        Route::post('/', [LoansController::class, 'store'])->name('store');
        Route::get('/show/{loan}', [LoansController::class, 'show'])->name('show');
        Route::get('/{loan}/edit', [LoansController::class, 'edit'])->name('edit');
        Route::put('/{loan}', [LoansController::class, 'update'])->name('update');
        Route::delete('/{loan}', [LoansController::class, 'destroy'])->name('destroy');

        // Routes pour les rapports et prêts actifs
        Route::get('active-loans', [LoansController::class, 'activeLoans'])->name('active');
        Route::get('loans-report', [LoansController::class, 'report'])->name('report');
    });
    
    // Routes pour les paiements de prêts
    Route::prefix('loans/{loan}/payments')->name('loans.payments.')->group(function () {
        Route::get('/', [LoanPaymentController::class, 'index'])->name('index');
        Route::get('/create', [LoanPaymentController::class, 'create'])->name('create');
        Route::post('/', [LoanPaymentController::class, 'store'])->name('store');
        Route::get('/{payment}/edit', [LoanPaymentController::class, 'edit'])->name('edit');
        Route::put('/{payment}', [LoanPaymentController::class, 'update'])->name('update');
        Route::delete('/{payment}', [LoanPaymentController::class, 'destroy'])->name('destroy');
    });
});
