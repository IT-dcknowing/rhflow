<?php

use Illuminate\Support\Facades\Route;
use Modules\LandingPage\Http\Controllers\LandingPageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\AllowanceOptionController;

// Inclure les routes d'authentification (sans middleware maintenance pour permettre la connexion)
require __DIR__.'/auth.php';

// Routes de test pour les pages d'erreur (uniquement en mode debug)
if (config('app.debug')) {
    require __DIR__.'/test-errors.php';
}

// Page de maintenance (accessible à tous)
Route::get('/maintenance', function () {
    $maintenanceMessage = \App\Models\Setting::get('maintenance_message', 'Le site est en maintenance.');
    return view('maintenance', [
        'message' => $maintenanceMessage,
        'title' => 'Maintenance - RH Flow'
    ]);
})->name('maintenance');

// Routes des notifications (accessibles à tous les utilisateurs authentifiés)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');

    // Actions AJAX
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::patch('/notifications/{notification}/unread', [NotificationController::class, 'markAsUnread'])->name('notifications.mark-unread');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/ajax/unread', [NotificationController::class, 'getUnread'])->name('notifications.ajax.unread');
    Route::get('/notifications/stats', [NotificationController::class, 'stats'])->name('notifications.stats');

    // Guide utilisateur complet
    Route::get('/guide-utilisateur', function () {
        return view('guide.index');
    })->name('guide-utilisateur');
});

// Inclure les routes des utilisateurs
require __DIR__.'/user.php';

// Routes pour les allowances (web interface)
Route::middleware(['auth'])->group(function () {
    // Options d'allocation
    Route::resource('allowance-options', AllowanceOptionController::class)->except(['show']);

    // Allocations
    Route::resource('allowances', AllowanceController::class)->except(['show']);
    Route::get('allowances/employee/{employeeId}', [AllowanceController::class, 'getByEmployee'])->name('allowances.by-employee');
    Route::get('allowances/{allowance}/print', [AllowanceController::class, 'print'])->name('allowances.print');
});

// Inclure les routes des modules activés
Route::middleware(['auth', 'maintenance'])->group(function () {

});

// Inclure les routes Super Admin (avec leur propre middleware)
require __DIR__.'/super-admin.php';
