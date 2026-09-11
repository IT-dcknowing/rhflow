<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\SuperAdminController;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
|
| Ces routes sont réservées au Super Administrateur système
| Elles nécessitent une authentification de type 'super_admin'
|
*/

Route::middleware(['auth', 'super.admin'])->prefix('super-admin')->name('super-admin.')->group(function () {

    // Dashboard Super Admin
    Route::get('/', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // Gestion des entreprises
    Route::get('/enterprises', [SuperAdminController::class, 'enterprises'])->name('enterprises.index');
    Route::get('/enterprises/export/xlsx', [SuperAdminController::class, 'exportEnterprisesXlsx'])->name('enterprises.export.xlsx');
    Route::get('/enterprises/export/pdf', [SuperAdminController::class, 'exportEnterprisesPdf'])->name('enterprises.export.pdf');
    Route::get('/enterprises/lastlogin', [SuperAdminController::class, 'enterprisesLastLogin'])->name('enterprises.lastlogin');
    Route::get('/enterprises/create', [SuperAdminController::class, 'createEnterprise'])->name('enterprises.create');
    Route::post('/enterprises', [SuperAdminController::class, 'storeEnterprise'])->name('enterprises.store');
    Route::get('/enterprises/{enterprise}', [SuperAdminController::class, 'showEnterprise'])->name('enterprises.show');
    Route::get('/enterprises/{enterprise}/showactivity', [SuperAdminController::class, 'showActivity'])->name('enterprises.showactivity');
    Route::get('/enterprises/{enterprise}/edit', [SuperAdminController::class, 'editEnterprise'])->name('enterprises.edit');
    Route::put('/enterprises/{enterprise}', [SuperAdminController::class, 'updateEnterprise'])->name('enterprises.update');
    Route::delete('/enterprises/{enterprise}', [SuperAdminController::class, 'deleteEnterprise'])->name('enterprises.delete');
    Route::delete('/enterprises/{enterprise}/ajax', [SuperAdminController::class, 'deleteEnterpriseAjax'])->name('enterprises.delete.ajax');
    Route::get('/enterprises/{enterprise}/suspend', [SuperAdminController::class, 'suspendEnterprise'])->name('enterprises.suspend');
    Route::get('/enterprises/{enterprise}/activate', [SuperAdminController::class, 'activateEnterprise'])->name('enterprises.activate');
    Route::get('/enterprises/{enterprise}/users', [SuperAdminController::class, 'enterpriseUsers'])->name('enterprises.users');
    Route::get('/enterprises/{enterprise}/subscription', [SuperAdminController::class, 'subscriptionEntreprise'])->name('enterprises.subscription');
    Route::put('/enterprises/{enterprise}/subscription', [SuperAdminController::class, 'updateSubscription'])->name('enterprises.subscription.update');
    Route::post('/enterprises/{enterprise}/subscription/renew', [SuperAdminController::class, 'renewSubscription'])->name('enterprises.subscription.renew');
    
    // Gestion des utilisateurs système
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users.index');
    Route::get('/users/create/{entreprise}', [SuperAdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}', [SuperAdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [SuperAdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [SuperAdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [SuperAdminController::class, 'deleteUser'])->name('users.delete');
    Route::get('/users/{user}/suspend', [SuperAdminController::class, 'suspendUser'])->name('users.suspend');
    Route::get('/users/{user}/activate', [SuperAdminController::class, 'activateUser'])->name('users.activate');

    // Secteur d'activité
    Route::get('/sectors', [SuperAdminController::class, 'sectors'])->name('sectors.index');
    Route::get('/sectors/create', [SuperAdminController::class, 'createSector'])->name('sectors.create');
    Route::post('/sectors', [SuperAdminController::class, 'storeSector'])->name('sectors.store');
    Route::get('/sectors/{sector}', [SuperAdminController::class, 'showSector'])->name('sectors.show');
    Route::get('/sectors/{sector}/edit', [SuperAdminController::class, 'editSector'])->name('sectors.edit');
    Route::put('/sectors/{sector}', [SuperAdminController::class, 'updateSector'])->name('sectors.update');
    Route::patch('/sectors/{sector}/toggle', [SuperAdminController::class, 'toggleSector'])->name('sectors.toggle');
    Route::delete('/sectors/{sector}', [SuperAdminController::class, 'deleteSector'])->name('sectors.delete');
    Route::get('/sectors/export/xlsx', [SuperAdminController::class, 'exportSectors'])->name('sectors.export.xlsx');
    Route::get('/sectors/export/pdf', [SuperAdminController::class, 'exportSectorsPdf'])->name('sectors.export.pdf');
    
    // Gestion des packs/abonnements
    Route::get('/packs', [SuperAdminController::class, 'packs'])->name('packs.index');
    Route::get('/packs/create', [SuperAdminController::class, 'createPack'])->name('packs.create');
    Route::post('/packs', [SuperAdminController::class, 'storePack'])->name('packs.store');
    Route::get('/packs/{pack}', [SuperAdminController::class, 'showPack'])->name('packs.show');
    Route::get('/packs/{pack}/edit', [SuperAdminController::class, 'editPack'])->name('packs.edit');
    Route::put('/packs/{pack}', [SuperAdminController::class, 'updatePack'])->name('packs.update');
    Route::patch('/packs/{pack}/toggle', [SuperAdminController::class, 'togglePack'])->name('packs.toggle');
    Route::delete('/packs/{pack}', [SuperAdminController::class, 'deletePack'])->name('packs.delete');
    Route::get('/packs/{pack}/modules', [SuperAdminController::class, 'packModules'])->name('packs.modules');
    Route::post('/packs/{pack}/modules', [SuperAdminController::class, 'updatePackModules'])->name('packs.modules.update');

    // Rapports et analytics
    Route::get('/reports', [SuperAdminController::class, 'reports'])->name('reports.index');
    Route::get('/reports/export/xlsx', [SuperAdminController::class, 'exportReportsXlsx'])->name('reports.export.xlsx');
    Route::get('/reports/export/pdf', [SuperAdminController::class, 'exportReportsPdf'])->name('reports.export.pdf');

    // Activités récentes
    Route::get('/activities', [SuperAdminController::class, 'activities'])->name('activities.index');


    // Gestion des commandes
    Route::get('/commandes', [SuperAdminController::class, 'commandes'])->name('commandes.index');
    Route::get('/commandes/pending', [SuperAdminController::class, 'commandesPending'])->name('commandes.pending');
    Route::get('/commandes/{commande}', [SuperAdminController::class, 'showCommande'])->name('commandes.show');
    Route::post('/commandes/{commande}', [SuperAdminController::class, 'updateCommande'])->name('commandes.update');
    Route::post('/commandes/{commande}/cancel', [SuperAdminController::class, 'cancelCommande'])->name('commandes.cancel');
    Route::post('/commandes/{commande}/delete', [SuperAdminController::class, 'deleteCommande'])->name('commandes.delete');
    Route::get('/commandes/coupons/index', [SuperAdminController::class, 'commandesCoupons'])->name('commandes.coupons.index');
    Route::post('/commandes/coupons/store', [SuperAdminController::class, 'storeCoupon'])->name('commandes.coupons.store');
    Route::get('/commandes/coupons/{coupon}', [SuperAdminController::class, 'showCoupon'])->name('commandes.coupons.show');
    Route::get('/commandes/coupons/{coupon}/edit', [SuperAdminController::class, 'editCoupon'])->name('commandes.coupons.edit');
    Route::post('/commandes/coupons/{coupon}', [SuperAdminController::class, 'updateCoupon'])->name('commandes.coupons.update');
    Route::post('/commandes/coupons/{coupon}/toggle', [SuperAdminController::class, 'toggleCoupon'])->name('commandes.coupons.toggle');
    Route::delete('/commandes/coupons/{coupon}', [SuperAdminController::class, 'deleteCoupon'])->name('commandes.coupons.delete');
   

    // Paramètres système
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/test-email', [SuperAdminController::class, 'testEmailConfiguration'])->name('settings.test-email');
    Route::post('/settings/reset', [SuperAdminController::class, 'resetSettings'])->name('settings.reset');

    // Support et tickets
    Route::get('/support', [SuperAdminController::class, 'support'])->name('support.index');

    // Profile : le controleur traite l'affichage (GET) et l'enregistrement (POST)
    // sur la meme URL, le formulaire poste sur super-admin.profile.index.
    Route::match(['get', 'post'], '/profile', [SuperAdminController::class, 'profile'])->name('profile.index');

    // Système de recherche global
    Route::get('/search', [SuperAdminController::class, 'search'])->name('search.ajax');
});
