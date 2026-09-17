<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SettingsController;
use Modules\Settings\Http\Controllers\LoanTypeController;  

// Routes pour les entreprises
Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
    // Routes du module Settings (Paramètres d'entreprise)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/config', [SettingsController::class, 'index'])->name('config');
        Route::get('/config/end/', [SettingsController::class, 'configEnd'])->name('config.end');
        Route::get('/company-settings', [SettingsController::class, 'companySettings'])->name('settings');
        Route::post('/company-settings', [SettingsController::class, 'updateCompanySettings'])->name('settings.update');
        Route::post('/company-logo', [SettingsController::class, 'updateLogo'])->name('logo.update');
        Route::post('/company-signature', [SettingsController::class, 'updateSignature'])->name('signature.update');
        Route::post('/company-stamp', [SettingsController::class, 'updateStamp'])->name('stamp.update');
        Route::get('/theme', [SettingsController::class, 'themeColors'])->name('theme');
        Route::post('/theme', [SettingsController::class, 'updateThemeColors'])->name('theme.update');
        Route::get('/documents', [SettingsController::class, 'companyDocuments'])->name('documents');
        Route::post('/documents', [SettingsController::class, 'updateCompanyDocuments'])->name('documents.update');
   
        // Routes pour les types de congés
        Route::get('/leave-types', [SettingsController::class, 'leaveTypes'])->name('leave-types.index');
        Route::post('/leave-types', [SettingsController::class, 'storeLeaveType'])->name('leave-types.store');
        Route::get('/leave-types/{leaveType}/edit', [SettingsController::class, 'editLeaveType'])->name('leave-types.edit');
        Route::put('/leave-types/{leaveType}', [SettingsController::class, 'updateLeaveType'])->name('leave-types.update');
        Route::put('/leave-types/{leaveType}/toggle', [SettingsController::class, 'toggleLeaveType'])->name('leave-types.toggle');
        Route::delete('/leave-types/{leaveType}', [SettingsController::class, 'destroyLeaveType'])->name('leave-types.destroy');

        // Routes pour les types de prêts
        Route::get('/loan-types', [LoanTypeController::class, 'index'])->name('loan-types.index');
        Route::get('/loan-types/create', [LoanTypeController::class, 'create'])->name('loan-types.create');
        Route::post('/loan-types', [LoanTypeController::class, 'store'])->name('loan-types.store');
        Route::get('/loan-types/{loanType}', [LoanTypeController::class, 'show'])->name('loan-types.show');
        Route::get('/loan-types/{loanType}/edit', [LoanTypeController::class, 'edit'])->name('loan-types.edit');
        Route::put('/loan-types/{loanType}', [LoanTypeController::class, 'update'])->name('loan-types.update');
        Route::put('/loan-types/{loanType}/toggle-status', [LoanTypeController::class, 'toggleStatus'])->name('loan-types.toggle-status');
        Route::delete('/loan-types/{loanType}', [LoanTypeController::class, 'destroy'])->name('loan-types.destroy');

        // Routes pour les emplacements de travail (pointeuses)
        Route::get('/work-locations', [SettingsController::class, 'workLocations'])->name('work-locations.index');
        Route::post('/work-locations', [SettingsController::class, 'storeWorkLocation'])->name('work-locations.store');
        Route::put('/work-locations/{workLocation}', [SettingsController::class, 'updateWorkLocation'])->name('work-locations.update');
        Route::put('/work-locations/{workLocation}/toggle', [SettingsController::class, 'toggleWorkLocation'])->name('work-locations.toggle');
        Route::delete('/work-locations/{workLocation}', [SettingsController::class, 'destroyWorkLocation'])->name('work-locations.destroy');

        // Routes pour les documents de société
        Route::get('/company-documents', [SettingsController::class, 'companyDocuments'])->name('company-documents.index');
        Route::post('/company-documents/logos', [SettingsController::class, 'updateCompanyDocuments'])->name('company-documents.update-logos');
        Route::post('/company-documents/legal', [SettingsController::class, 'storeCompanyDocument'])->name('company-documents.store');
        Route::put('/company-documents/legal/{companyDocument}', [SettingsController::class, 'updateCompanyDocument'])->name('company-documents.update-legal');
        Route::delete('/company-documents/legal/{companyDocument}', [SettingsController::class, 'destroyCompanyDocument'])->name('company-documents.destroy');
        Route::post('/company-documents/legal/{companyDocument}/toggle-verification', [SettingsController::class, 'toggleDocumentVerification'])->name('company-documents.toggle-verification');
        Route::get('/company-documents/legal/{companyDocument}/download', [SettingsController::class, 'downloadCompanyDocument'])->name('company-documents.download');

        // API pour les suggestions de localisation
        Route::get('/api/location-suggestions', [SettingsController::class, 'getLocationSuggestions'])->name('location.suggestions');

        // Routes pour la gestion des Sites/Succursales (Branches)
        Route::get('/branches', [SettingsController::class, 'branches'])->name('branches.index');
        Route::post('/branches', [SettingsController::class, 'storeBranch'])->name('branches.store');
        Route::get('/branches/{branch}/edit', [SettingsController::class, 'editBranch'])->name('branches.edit');
        Route::put('/branches/{branch}', [SettingsController::class, 'updateBranch'])->name('branches.update');
        Route::put('/branches/{branch}/toggle', [SettingsController::class, 'toggleBranch'])->name('branches.toggle');
        Route::delete('/branches/{branch}', [SettingsController::class, 'destroyBranch'])->name('branches.destroy');
        // Création d'un manager depuis la modale d'un site (réponse JSON)
        Route::post('/branches/managers', [SettingsController::class, 'storeBranchManager'])->name('branches.managers.store');

        // Routes pour la gestion des Services (Departments)
        Route::get('/departments', [SettingsController::class, 'departments'])->name('departments.index');
        Route::post('/departments', [SettingsController::class, 'storeDepartment'])->name('departments.store');
        Route::get('/departments/{department}/edit', [SettingsController::class, 'editDepartment'])->name('departments.edit');
        Route::put('/departments/{department}', [SettingsController::class, 'updateDepartment'])->name('departments.update');
        Route::put('/departments/{department}/toggle', [SettingsController::class, 'toggleDepartment'])->name('departments.toggle');
        Route::delete('/departments/{department}', [SettingsController::class, 'destroyDepartment'])->name('departments.destroy');

        // Routes pour la gestion des Postes (Designations)
        Route::get('/designations', [SettingsController::class, 'designations'])->name('designations.index');
        Route::post('/designations', [SettingsController::class, 'storeDesignation'])->name('designations.store');
        Route::get('/designations/{designation}/edit', [SettingsController::class, 'editDesignation'])->name('designations.edit');
        Route::put('/designations/{designation}', [SettingsController::class, 'updateDesignation'])->name('designations.update');
        Route::put('/designations/{designation}/toggle', [SettingsController::class, 'toggleDesignation'])->name('designations.toggle');
        Route::delete('/designations/{designation}', [SettingsController::class, 'destroyDesignation'])->name('designations.destroy');
 
        // Routes pour le système de présence
        Route::get('/attendance-system', [SettingsController::class, 'attendanceSystem'])->name('attendance-system.index');
        Route::post('/attendance-system', [SettingsController::class, 'updateAttendanceSystem'])->name('attendance-system.update');
        Route::post('/attendance-system/qr-code-generate', [SettingsController::class, 'generateQRCode'])->name('attendance-system.qr-code-generate');
        Route::post('/attendance-system/qr-code-download', [SettingsController::class, 'downloadQRCode'])->name('attendance-system.qr-code-download');
        
        // Routes pour la gestion des utilisateurs     
        Route::get('/users', [SettingsController::class, 'users'])->name('users.index');
        Route::get('/users/create', [SettingsController::class, 'createUser'])->name('users.create');
        Route::post('/users', [SettingsController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [SettingsController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [SettingsController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [SettingsController::class, 'destroyUser'])->name('users.destroy');
        Route::put('/users/{user}/toggle-status', [SettingsController::class, 'toggleUserStatus'])->name('users.toggle-status');
        Route::get('/users/{user}/details', [SettingsController::class, 'userDetails'])->name('users.details');
        Route::post('/users/{user}/reset-password', [SettingsController::class, 'resetUserPassword'])->name('users.reset-password');
        Route::post('/users/{user}/send-welcome-email', [SettingsController::class, 'sendWelcomeEmail'])->name('users.send-welcome-email');
        Route::get('/users/export', [SettingsController::class, 'exportUsers'])->name('users.export');
        Route::post('/users/import', [SettingsController::class, 'importUsers'])->name('users.import');
    });
});
 