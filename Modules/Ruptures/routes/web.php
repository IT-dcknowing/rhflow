<?php

use Illuminate\Support\Facades\Route;
use Modules\Ruptures\Http\Controllers\RupturesController;

// Routes pour les entreprises
Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
    
    // Routes principales pour les ruptures
    Route::get('ruptures/index', [RupturesController::class, 'index'])->name('ruptures.index');
    Route::get('ruptures/employee/{employee_id}', [RupturesController::class, 'ruptureEmployee'])->name('ruptures.employee');
    Route::get('ruptures/create', [RupturesController::class, 'create'])->name('ruptures.create');
    Route::post('ruptures/store', [RupturesController::class, 'store'])->name('ruptures.store');
    Route::get('ruptures/show/{id}', [RupturesController::class, 'show'])->name('ruptures.show');
    Route::get('ruptures/edit/{id}', [RupturesController::class, 'edit'])->name('ruptures.edit');
    Route::put('ruptures/update/{id}', [RupturesController::class, 'update'])->name('ruptures.update');
    Route::delete('ruptures/destroy/{id}', [RupturesController::class, 'destroy'])->name('ruptures.destroy');
    Route::get('ruptures/get-contract-type/{id}', [RupturesController::class, 'getContractType'])->name('ruptures.get-contract-type');
    Route::get('ruptures/get-contract-type-end/{id}', [RupturesController::class, 'getContractTypeend'])->name('ruptures.get-contract-type-end');
    // API de calcul automatique des droits à la rupture (préavis + licenciement)
    Route::get('ruptures/calcul-droits/{employee_id}', [RupturesController::class, 'calculerDroitsRupture'])->name('ruptures.calcul-droits');

    
    // Routes pour les pièces jointes
    Route::get('ruptures/decompte/download/pdf/{id}', [RupturesController::class, 'downloadDecomptePdf'])->name('ruptures.decompte.download.pdf');
    Route::get('ruptures/decompte/download/doc/{id}', [RupturesController::class, 'downloadDecompteDoc'])->name('ruptures.decompte.download.doc');
    Route::get('ruptures/releve/download/pdf/{id}', [RupturesController::class, 'downloadRelevePdf'])->name('ruptures.releve.download.pdf');
    Route::get('ruptures/releve/download/doc/{id}', [RupturesController::class, 'downloadReleveDoc'])->name('ruptures.releve.download.doc');
    Route::get('ruptures/solde/download/pdf/{id}', [RupturesController::class, 'downloadSoldePdf'])->name('ruptures.solde.download.pdf');
    Route::get('ruptures/solde/download/doc/{id}', [RupturesController::class, 'downloadSoldeDoc'])->name('ruptures.solde.download.doc');
    
    // Routes pour le changement de statut
    Route::post('ruptures/{id}/change-status', [RupturesController::class, 'changeStatus'])->name('ruptures.change-status');
    
    // Routes pour les types de rupture
    Route::get('ruptures/types', [RupturesController::class, 'types'])->name('ruptures.types');
    Route::post('ruptures/types/store', [RupturesController::class, 'storeType'])->name('ruptures.types.store');
    Route::put('ruptures/types/update/{id}', [RupturesController::class, 'updateType'])->name('ruptures.types.update');
    Route::delete('ruptures/types/destroy/{id}', [RupturesController::class, 'destroyType'])->name('ruptures.types.destroy');
});
