<?php

use Illuminate\Support\Facades\Route;
use Modules\Declarations\Http\Controllers\DeclarationsController;
use Modules\Declarations\Http\Controllers\BulletinQueueController;

Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
     //Dashboard
    Route::get('declarations/dashboard', [DeclarationsController::class, 'dashboard'])->name('declarations.dashboard');

    //Delacrations  
    Route::get('declarations/index', [DeclarationsController::class, 'index'])->name('declarations.index');
    Route::get('declarations/create', [DeclarationsController::class, 'create'])->name('declarations.create');
    Route::post('declarations/store', [DeclarationsController::class, 'store'])->name('declarations.store');
    Route::get('declarations/show/{id}', [DeclarationsController::class, 'show'])->name('declarations.show');
    Route::get('declarations/edit/{id}', [DeclarationsController::class, 'edit'])->name('declarations.edit');
    Route::put('declarations/update/{id}', [DeclarationsController::class, 'update'])->name('declarations.update');
    Route::delete('declarations/destroy/{id}', [DeclarationsController::class, 'destroy'])->name('declarations.destroy');

    //Résumé des paies - Gestion des bulletins
    Route::get('declarations/resume', [DeclarationsController::class, 'resumeIndex'])->name('declarations.resume.index');
    Route::get('declarations/resume/create', [DeclarationsController::class, 'create'])->name('declarations.resume.create');
    
    // Routes Queue pour génération PDF en arrière-plan (must come before {id} routes)
    Route::post('declarations/resume/generate-queue', [BulletinQueueController::class, 'generateBulletinsPDF'])->name('declarations.resume.generate-queue');
    Route::get('declarations/resume/download', [BulletinQueueController::class, 'downloadBulletinFile'])->name('declarations.resume.download');
    Route::get('declarations/bulletins/download', [BulletinQueueController::class, 'downloadBulletinFile']); // Fallback pour éviter 404
    Route::get('declarations/resume/progress', [BulletinQueueController::class, 'getBulletinProgress'])->name('declarations.resume.progress');
    Route::get('declarations/get_bulletins', [DeclarationsController::class, 'getBulletins'])->name('declarations.get_bulletins');
    
    // Routes with {id} parameter (must come after specific routes)
    Route::get('declarations/resume/{id}', [DeclarationsController::class, 'resumeShow'])->name('declarations.resume.show');
    Route::get('declarations/resume/{id}/edit', [DeclarationsController::class, 'resumeEdit'])->name('declarations.resume.edit');
    Route::put('declarations/resume/{id}', [DeclarationsController::class, 'resumeUpdate'])->name('declarations.resume.update');
    Route::delete('declarations/resume/{id}', [DeclarationsController::class, 'destroy'])->name('declarations.resume.destroy');
    Route::post('declarations/resume-bulk-destroy', [DeclarationsController::class, 'bulkDestroy'])->name('declarations.resume.bulk-destroy');
    Route::get('declarations/resume/bulletins-all/{id}', [DeclarationsController::class, 'bulletinsAll'])->name('declarations.resume.bulletins-all');
    
    // Test route
    Route::get('declarations/test', function() {
        return response()->json(['message' => 'Declarations module routes are working']);
    })->name('declarations.test');

    //Livre de paie
    Route::get('declarations/livrepaie/mensuel', [DeclarationsController::class, 'livrepaieMensuel'])->name('declarations.livrepaie.mensuel');
    Route::get('declarations/livrepaie/annuel', [DeclarationsController::class, 'livrepaieAnnuel'])->name('declarations.livrepaie.annuel');
    Route::get('declarations/livrepaie/individuel', [DeclarationsController::class, 'livrepaieIndividuel'])->name('declarations.livrepaie.individuel');

    //Récap des déclarations
    Route::get('declarations/cotisation/index', [DeclarationsController::class, 'cotisation'])->name('declarations.cotisation.index');

    //Etat des déclarations
    Route::get('declarations/declaration/mensuelle', [DeclarationsController::class, 'declarationMensuelle'])->name('declarations.declaration.mensuelle');
    Route::get('declarations/declaration/annuelle', [DeclarationsController::class, 'declarationAnnuelle'])->name('declarations.declaration.annuelle');

    //Routes AJAX pour les livres de paie et cotisations
    Route::get('declarations/get_employees_periode', [DeclarationsController::class, 'getEmployeesPeriode'])->name('declarations.get_employees_periode');
    Route::get('declarations/get_periodes', [DeclarationsController::class, 'get_periodes'])->name('declarations.get_periodes');
    Route::get('declarations/get_paylists', [DeclarationsController::class, 'get_paylists'])->name('declarations.get_paylists');
    Route::get('declarations/get_disa', [DeclarationsController::class, 'get_disa'])->name('declarations.get_disa');
    Route::get('declarations/get_paylists_individuel', [DeclarationsController::class, 'get_paylists_individuel'])->name('declarations.get_paylists_individuel');
    Route::get('declarations/get_paylists_annuel', [DeclarationsController::class, 'get_paylists_annuel'])->name('declarations.get_paylists_annuel');
    Route::get('declarations/get_cotisations', [DeclarationsController::class, 'get_cotisations'])->name('declarations.get_cotisations');
    Route::get('declarations/get_decla_efi', [DeclarationsController::class, 'get_decla_efi'])->name('declarations.get_decla_efi');
    Route::get('declarations/get_decla_edi', [DeclarationsController::class, 'get_decla_edi'])->name('declarations.get_decla_edi');
    Route::get('declarations/get_decla_cnps', [DeclarationsController::class, 'get_decla_cnps'])->name('declarations.get_decla_cnps');
    Route::get('declarations/get_decla_cmu', [DeclarationsController::class, 'get_decla_cmu'])->name('declarations.get_decla_cmu');
});
