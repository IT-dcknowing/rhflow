<?php

use Illuminate\Support\Facades\Route;
use Modules\Time\Http\Controllers\TimeController;
use Modules\Time\Http\Controllers\OvertimeController;
use Modules\Time\Http\Controllers\AbsenceController;

// Routes pour les entreprises
Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
    // Gestion des temps de travail (à mettre à jour si nécessaire)
    Route::prefix('times/absences')->name('times.absences.')->group(function () {
        Route::get('index', [AbsenceController::class, 'index'])->name('index');
         Route::get('create', [AbsenceController::class, 'create'])->name('create');
        Route::post('store', [AbsenceController::class, 'store'])->name('store');
        Route::get('show/{id}', [AbsenceController::class, 'show'])->name('show');
        Route::get('edit/{id}', [AbsenceController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [AbsenceController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [AbsenceController::class, 'destroy'])->name('destroy');
        Route::delete('remove-document/{id}', [AbsenceController::class, 'removeDocument'])->name('remove-document');
        Route::put('update-status/{id}', [AbsenceController::class, 'updateStatus'])->name('update-status');
    });
 
    // Gestion des heures supplémentaires 
    Route::prefix('times/overtime')->name('times.overtime.')->group(function () {
        Route::get('/', [OvertimeController::class, 'index'])->name('index');
        Route::post('store', [OvertimeController::class, 'store'])->name('store');
        Route::get('show/{id}', [OvertimeController::class, 'show'])->name('show');
        Route::get('edit/{id}', [OvertimeController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [OvertimeController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [OvertimeController::class, 'destroy'])->name('destroy');
                
        // Marquer comme payé
        Route::post('/{id}/mark-as-paid', [OvertimeController::class, 'markAsPaid'])->name('mark-as-paid');
        Route::post('/{id}/mark-as-unpaid', [OvertimeController::class, 'markAsUnpaid'])->name('mark-as-unpaid');

        // Exportation des données
        Route::get('/export', [OvertimeController::class, 'export'])->name('export');
    });

    // Gestion présence Code Qr
    Route::prefix('times/qrcode-pointage')->name('times.qrcode-pointage.')->group(function () {
        Route::get('index', [TimeController::class, 'index'])->name('index');
        Route::post('scan', [TimeController::class, 'scanQrCode'])->name('scan');
        Route::post('check-in', [TimeController::class, 'checkIn'])->name('check-in');
        Route::post('check-out', [TimeController::class, 'checkOut'])->name('check-out');
        Route::get('export', [TimeController::class, 'export'])->name('export');
        
        // Nouvelles routes pour le Portail Web Collectif
        Route::get('portal', [TimeController::class, 'portal'])->name('portal');
        Route::post('portal/store', [TimeController::class, 'storePortalPointage'])->name('portal.store');
        
        // Rapport et statistiques
        Route::get('reports', [TimeController::class, 'reports'])->name('reports');
    });
});

