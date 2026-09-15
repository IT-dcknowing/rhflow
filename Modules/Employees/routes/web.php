<?php

use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Route;
use Modules\Employees\Http\Controllers\EmployeesController;
use Modules\Employees\Http\Controllers\DemandeController;
use Modules\Employees\Http\Controllers\GrilleController;

// Routes pour les entreprises
Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {

    Route::prefix('employees')->name('employees.')->group(function () {
        // Dashboard des employés
        Route::get('dashboard', [EmployeesController::class, 'dashboard'])->name('dashboard');

        // Employés mensuels
        Route::get('/', [EmployeesController::class, 'index'])->name('index');
        Route::get('create', [EmployeesController::class, 'create'])->name('create');
        // Suggestions du champ adresse (formulaires de création et de modification)
        Route::get('adresses/suggestions', [EmployeesController::class, 'addressSuggestions'])->name('address-suggestions');
        Route::post('import', [EmployeesController::class, 'import'])->name('import');
        Route::get('import-template', [EmployeesController::class, 'downloadTemplate'])->name('import.template');

        Route::post('/', [EmployeesController::class, 'store'])->name('store');

        Route::get('{id}', [EmployeesController::class, 'show'])->name('show');
        Route::get('{id}/edit', [EmployeesController::class, 'edit'])->name('edit');
        Route::put('{id}', [EmployeesController::class, 'update'])->name('update');
        Route::delete('{id}', [EmployeesController::class, 'destroy'])->name('destroy');
        Route::post('{id}/toggle', [EmployeesController::class, 'toggle'])->name('toggle');

        // Statistiques et rapports
        Route::get('dossiers/index', [EmployeesController::class, 'dossiers'])->name('dossiers.index');
        Route::get('dossiers/profile/{id}', [EmployeesController::class, 'profile'])->name('dossiers.profile');

        // Routes AJAX pour les sélecteurs dynamiques
        Route::post('getdepartment', [EmployeesController::class, 'getDepartment'])->name('getdepartment');
        Route::post('employee/json', [EmployeesController::class, 'getDesignations'])->name('employee.json');
        Route::get('get-categories-by-type/{typeId}', [EmployeesController::class, 'getCategoriesByType'])->name('get-categories-by-type');
        
        // Route pour les détails de catégorie
        Route::get('get-category-details/{typeId}/{categoryId}', [EmployeesController::class, 'getCategoryDetails'])->name('get-category-details');

        // Routes des demandes 
        Route::get('demandes/index', [DemandeController::class, 'index'])->name('demandes.index');
        Route::get('demandes/show/{id}', [DemandeController::class, 'show'])->name('demandes.show');
        Route::delete('demandes/destroy/{id}', [DemandeController::class, 'destroy'])->name('demandes.destroy');
        
        // Routes de validation des demandes
        Route::post('demandes/validate/{id}', [DemandeController::class, 'validateDemande'])->name('demandes.validate');
        Route::post('demandes/reject/{id}', [DemandeController::class, 'rejectDemande'])->name('demandes.reject');

        // Routes pour les téléchargements de documents
        Route::get('{id}/attestation-travail', [EmployeesController::class, 'downloadWorkCertificate'])->name('attestation-travail');
        Route::get('{id}/certificat-travail', [EmployeesController::class, 'downloadWorkCertification'])->name('certificat-travail');

        // Routes pour la gestion des données associées
        Route::post('family/store', [EmployeesController::class, 'storeFamily'])->name('family.store');
        Route::put('family/{id}', [EmployeesController::class, 'updateFamily'])->name('family.update');
        Route::delete('family/{id}', [EmployeesController::class, 'destroyFamily'])->name('family.destroy');
        Route::post('documents/store', [EmployeesController::class, 'storeDocument'])->name('documents.store');
        Route::delete('documents/{id}', [EmployeesController::class, 'destroyDocument'])->name('documents.destroy');

        // Route grille salariale
        Route::get('grille/index', [GrilleController::class, 'index'])->name('grille.index');
        Route::get('grille/create/{id}', [GrilleController::class, 'create'])->name('grille.create');
        Route::post('grille/store', [GrilleController::class, 'store'])->name('grille.store');
        Route::get('grille/show/{id}', [GrilleController::class, 'show'])->name('grille.show');
        Route::get('grille/edit/{id}', [GrilleController::class, 'edit'])->name('grille.edit');
        Route::put('grille/update/{id}', [GrilleController::class, 'update'])->name('grille.update');
        Route::delete('grille/destroy/{id}', [GrilleController::class, 'destroy'])->name('grille.destroy');

        // Routes Bulletins de paie
        Route::get('bulletins/show/{id}', [EmployeesController::class, 'showBulletin'])->name('bulletins.show');
    });
});
