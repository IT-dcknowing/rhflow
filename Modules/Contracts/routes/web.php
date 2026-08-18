<?php

use Illuminate\Support\Facades\Route;
use Modules\Contracts\Http\Controllers\ContractsController;

// Routes pour les entreprises
Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
    //Dashboard
    Route::get('contracts/dashboard', [ContractsController::class, 'dashboard'])->name('contracts.dashboard');
    
    Route::prefix('contracts')->name('contracts.')->group(function () {
        //Contrats
        Route::get('index', [ContractsController::class, 'index'])->name('index');
        Route::get('employee/{id}', [ContractsController::class, 'ContractEmployee'])->name('employee');
        Route::get('create', [ContractsController::class, 'create'])->name('create');
        Route::post('store', [ContractsController::class, 'store'])->name('store');
        Route::get('show/{id}', [ContractsController::class, 'show'])->name('show');
        Route::get('edit/{id}', [ContractsController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [ContractsController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [ContractsController::class, 'destroy'])->name('destroy');
        
        // Pièces jointes
        Route::post('attachment/add/{id}', [ContractsController::class, 'addAttachment'])->name('attachment.add');
        Route::get('attachment/download/{id}', [ContractsController::class, 'downloadAttachment'])->name('attachment.download');
        Route::delete('attachment/delete/{id}', [ContractsController::class, 'deleteAttachment'])->name('attachment.delete');
        
        // Avenants
        Route::post('avenant/add/{id}', [ContractsController::class, 'addAvenant'])->name('avenant.add');
        Route::get('avenant/edit/{id}', [ContractsController::class, 'editAvenant'])->name('avenant.edit');
        Route::delete('avenant/destroy/{id}', [ContractsController::class, 'destroyAvenant'])->name('avenant.destroy');
        Route::get('avenant/download/{id}', [ContractsController::class, 'downloadAvenant'])->name('avenant.download');
        
        // Signatures
        Route::get('signature/{id}', [ContractsController::class, 'signature'])->name('signature');
        Route::post('signature/save/{id}', [ContractsController::class, 'saveSignature'])->name('signature.save');
        
        // Types de contrat
        Route::get('types', [ContractsController::class, 'contractTypes'])->name('types');
        Route::post('types/store', [ContractsController::class, 'storeContractType'])->name('types.store');
        Route::put('types/update/{id}', [ContractsController::class, 'updateContractType'])->name('types.update');
        Route::delete('types/destroy/{id}', [ContractsController::class, 'destroyContractType'])->name('types.destroy');
    });
});
