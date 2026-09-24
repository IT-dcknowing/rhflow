<?php

use Illuminate\Support\Facades\Route;
use Modules\PaieSalaries\Http\Controllers\PaieSalariesController;

// Routes pour les entreprises
Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
    //Dashboard
    Route::get('paiesalaries/dashboard', [PaieSalariesController::class, 'dashboard'])->name('paiesalaries.dashboard');

    // Paie du mois : ouvre la période à traiter ou propose d'ouvrir le mois suivant
    Route::get('paiesalaries/paie-du-mois', [PaieSalariesController::class, 'paieDuMois'])->name('paiesalaries.paie-du-mois');

    // Routes pour les exercices
    Route::get('paiesalaries/exercices', [PaieSalariesController::class, 'indexExercice'])->name('paiesalaries.exercices.index');
    Route::get('paiesalaries/exercices/create', [PaieSalariesController::class, 'createExercice'])->name('paiesalaries.exercices.create');
    Route::post('paiesalaries/exercices', [PaieSalariesController::class, 'storeExercice'])->name('paiesalaries.exercices.store');
    Route::get('paiesalaries/exercices/{id}', [PaieSalariesController::class, 'showExercice'])->name('paiesalaries.exercices.show');
    Route::get('paiesalaries/exercices/{id}/edit', [PaieSalariesController::class, 'editExercice'])->name('paiesalaries.exercices.edit');
    Route::put('paiesalaries/exercices/{id}', [PaieSalariesController::class, 'updateExercice'])->name('paiesalaries.exercices.update');
    Route::delete('paiesalaries/exercices/{id}', [PaieSalariesController::class, 'destroyExercice'])->name('paiesalaries.exercices.destroy');

    // Routes pour les périodes 
    Route::get('paiesalaries/exercices/{exerciceId}/periodes/create', [PaieSalariesController::class, 'createPeriode'])->name('paiesalaries.periodes.create');
    Route::post('paiesalaries/exercices/{exerciceId}/periodes', [PaieSalariesController::class, 'storePeriode'])->name('paiesalaries.periodes.store');
    Route::get('paiesalaries/periodes/{id}', [PaieSalariesController::class, 'showPeriode'])->name('paiesalaries.periodes.show');
    Route::get('paiesalaries/periodes/{id}/edit', [PaieSalariesController::class, 'editPeriode'])->name('paiesalaries.periodes.edit');
    Route::put('paiesalaries/periodes/{id}', [PaieSalariesController::class, 'updatePeriode'])->name('paiesalaries.periodes.update');
    Route::delete('paiesalaries/periodes/{id}', [PaieSalariesController::class, 'destroyPeriode'])->name('paiesalaries.periodes.destroy');
    Route::post('paiesalaries/periodes/{periode}/duplicate-elements', [PaieSalariesController::class, 'duplicateElements'])->name('paiesalaries.periodes.duplicate-elements');
    Route::post('paiesalaries/periodes/{id}/generer-bulletins', [PaieSalariesController::class, 'genererBulletins'])->name('paiesalaries.periodes.generer-bulletins');
    Route::post('paiesalaries/periodes/{id}/valider-paiement', [PaieSalariesController::class, 'validerPaiement'])->name('paiesalaries.periodes.valider-paiement');
    Route::post('paiesalaries/periodes/loanpaiement/{id}', [PaieSalariesController::class, 'loanPaiement'])->name('paiesalaries.periodes.loanpaiement');
    Route::post('paiesalaries/periodes/loanpaiement/{id}/retirer', [PaieSalariesController::class, 'loanPaiementRetirer'])->name('paiesalaries.periodes.loanpaiement.retirer');
    Route::post('paiesalaries/periodes/{id}/recopier-primes', [PaieSalariesController::class, 'recopierPrimes'])->name('paiesalaries.periodes.recopier-primes');
    Route::post('paiesalaries/periodes/{id}/traitement-masse', [PaieSalariesController::class, 'traitementMasse'])->name('paiesalaries.periodes.traitement-masse');
    Route::post('paiesalaries/periodes/{id}/salarie/{employee}', [PaieSalariesController::class, 'enregistrerSalarie'])->name('paiesalaries.periodes.enregistrer-salarie');

    // Routes pour le sélecteur global d'exercice et de période
    Route::post('paiesalaries/set-active-exercice', [PaieSalariesController::class, 'setActiveExercice'])->name('paiesalaries.set-active-exercice');
    Route::post('paiesalaries/set-active-periode', [PaieSalariesController::class, 'setActivePeriode'])->name('paiesalaries.set-active-periode');
    Route::get('paiesalaries/exercices-dropdown', [PaieSalariesController::class, 'getExercicesForDropdown'])->name('paiesalaries.exercices-dropdown');
    Route::get('paiesalaries/periodes-dropdown/{exerciceId}', [PaieSalariesController::class, 'getPeriodesForDropdown'])->name('paiesalaries.periodes-dropdown');

    // Routes pour la gestion des éléments de paie
    Route::prefix('paiesalaries/allowance')->name('paiesalaries.allowance.')->group(function () {
        Route::get('/', [PaieSalariesController::class, 'allowance'])->name('index');
        Route::get('/create/{id}/{periode}', [PaieSalariesController::class, 'createAllowance'])->name('create');
        Route::get('/edit/{id}/{periode}', [PaieSalariesController::class, 'editAllowance'])->name('edit');
        Route::post('/store', [PaieSalariesController::class, 'storeAllowance'])->name('store');
        Route::get('/show/{id}/{periode}', [PaieSalariesController::class, 'showAllowance'])->name('show');
        Route::put('/{id}', [PaieSalariesController::class, 'updateAllowance'])->name('update');
        Route::delete('/{id}', [PaieSalariesController::class, 'destroyAllowance'])->name('destroy');
        Route::get('/option', [PaieSalariesController::class, 'allowanceOption'])->name('option');
        Route::post('/option', [PaieSalariesController::class, 'storeOption'])->name('storeOption');
        Route::get('/option/edit/{id}', [PaieSalariesController::class, 'editOption'])->name('editOption.edit');
        Route::put('/option/{id}', [PaieSalariesController::class, 'updateOption'])->name('updateOption');
        Route::delete('/option/{id}', [PaieSalariesController::class, 'destroyOption'])->name('destroyOption');
        Route::post('/saveDefaults', [PaieSalariesController::class, 'saveDefaults'])->name('saveDefaults');
    });

    //retenues
    Route::get('paiesalaries/retenues/', [PaieSalariesController::class, 'retenues'])->name('paiesalaries.retenues.index');
    Route::get('paiesalaries/retenues/apply/{id}/{periode_id}', [PaieSalariesController::class, 'applyRetenue'])->name('paiesalaries.retenues.apply');
    Route::get('paiesalaries/retenues/create/{id}', [PaieSalariesController::class, 'createRetenue'])->name('paiesalaries.retenues.create');
    Route::post('paiesalaries/retenues/store', [PaieSalariesController::class, 'storeRetenue'])->name('paiesalaries.retenues.store');
    Route::post('paiesalaries/retenues/storeApply', [PaieSalariesController::class, 'storeRetenueApply'])->name('paiesalaries.retenues.storeApply');
    Route::post('paiesalaries/retenues/apply-all', [PaieSalariesController::class, 'applyAllDefaultRetenues'])->name('paiesalaries.retenues.applyAll');
    Route::get('paiesalaries/retenues/edit/{id}', [PaieSalariesController::class, 'editRetenue'])->name('paiesalaries.retenues.edit');
    Route::put('paiesalaries/retenues/update/{id}', [PaieSalariesController::class, 'updateRetenue'])->name('paiesalaries.retenues.update');
    Route::delete('paiesalaries/retenues/{id}', [PaieSalariesController::class, 'destroyRetenue'])->name('paiesalaries.retenues.destroy');
    Route::post('paiesalaries/retenues/{id}/activate', [PaieSalariesController::class, 'activateRetenue'])->name('paiesalaries.retenues.activate');
    Route::post('paiesalaries/retenues/{id}/deactivate', [PaieSalariesController::class, 'deactivateRetenue'])->name('paiesalaries.retenues.deactivate');
    Route::get('paiesalaries/retenues/{id}/add-employee', [PaieSalariesController::class, 'addEmployeeRetenue'])->name('paiesalaries.retenues.add-employee');
    Route::post('paiesalaries/retenues/{id}/store-employees', [PaieSalariesController::class, 'storeEmployeeRetenue'])->name('paiesalaries.retenues.store-employees');
    Route::get('paiesalaries/retenues/add/{id}', [PaieSalariesController::class, 'destroyRetenueAdd'])->name('paiesalaries.retenues.destroy.add');
    Route::get('paiesalaries/retenues/add/{id}/activate', [PaieSalariesController::class, 'activateRetenueAdd'])->name('paiesalaries.retenues.activate.add');
    Route::get('paiesalaries/retenues/add/{id}/deactivate', [PaieSalariesController::class, 'deactivateRetenueAdd'])->name('paiesalaries.retenues.deactivate.add');
    Route::get('paiesalaries/retenues/add/show/{id}', [PaieSalariesController::class, 'showRetenueAdd'])->name('paiesalaries.retenues.add.show');

    //remboursements
    Route::get('paiesalaries/remboursements', [PaieSalariesController::class, 'remboursements'])->name('paiesalaries.remboursements');
    Route::get('paiesalaries/remboursements/create/{id}', [PaieSalariesController::class, 'createRemboursement'])->name('paiesalaries.remboursements.create');
    Route::post('paiesalaries/remboursements/store', [PaieSalariesController::class, 'storeRemboursement'])->name('paiesalaries.remboursements.store');
    Route::get('paiesalaries/remboursements/edit/{id}', [PaieSalariesController::class, 'editRemboursement'])->name('paiesalaries.remboursements.edit');
    Route::put('paiesalaries/remboursements/update/{id}', [PaieSalariesController::class, 'updateRemboursement'])->name('paiesalaries.remboursements.update');
    Route::delete('paiesalaries/remboursements/{id}', [PaieSalariesController::class, 'destroyRemboursement'])->name('paiesalaries.remboursements.destroy');
    Route::post('paiesalaries/remboursements/{id}/activate', [PaieSalariesController::class, 'activateRemboursement'])->name('paiesalaries.remboursements.activate');
    Route::post('paiesalaries/remboursements/{id}/deactivate', [PaieSalariesController::class, 'deactivateRemboursement'])->name('paiesalaries.remboursements.deactivate');
    Route::get('paiesalaries/remboursements/{id}/add-employee', [PaieSalariesController::class, 'addEmployeeRemboursement'])->name('paiesalaries.remboursements.add-employee');
    Route::post('paiesalaries/remboursements/{id}/store-employees', [PaieSalariesController::class, 'storeEmployeeRemboursement'])->name('paiesalaries.remboursements.store-employees');
    Route::get('paiesalaries/remboursements/add/{id}', [PaieSalariesController::class, 'destroyRemboursementAdd'])->name('paiesalaries.remboursements.destroy.add');
    Route::get('paiesalaries/remboursements/add/{id}/activate', [PaieSalariesController::class, 'activateRemboursementAdd'])->name('paiesalaries.remboursements.activate.add');
    Route::get('paiesalaries/remboursements/add/{id}/deactivate', [PaieSalariesController::class, 'deactivateRemboursementAdd'])->name('paiesalaries.remboursements.deactivate.add');
    Route::get('paiesalaries/remboursements/add/show/{id}', [PaieSalariesController::class, 'showRemboursementAdd'])->name('paiesalaries.remboursements.add.show');


    // Paie
    Route::post('paiesalaries/store', [PaieSalariesController::class, 'store'])->name('paiesalaries.store');
    Route::get('paiesalaries/show/{id}/{periode_id}', [PaieSalariesController::class, 'show'])->name('paiesalaries.show');
    Route::put('paiesalaries/{id}', [PaieSalariesController::class, 'update'])->name('paiesalaries.update');

    //calcule
    Route::get('paiesalaries/calcule', [PaieSalariesController::class, 'calcule'])->name('paiesalaries.calcule');

    // Aperçu bulletin
    Route::get('paiesalaries/preview-bulletin/{id}/{exerciceId}/{periodeId}', [PaieSalariesController::class, 'previewBulletin'])->name('paiesalaries.preview-bulletin');
    Route::get('paiesalaries/download-preview-bulletin/{id}/{exerciceId}/{periodeId}', [PaieSalariesController::class, 'downloadPreviewBulletin'])->name('paiesalaries.download-preview-bulletin');
});
