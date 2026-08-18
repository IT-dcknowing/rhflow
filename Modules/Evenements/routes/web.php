<?php

use Illuminate\Support\Facades\Route;
use Modules\Evenements\Http\Controllers\EvenementsController;
use Modules\Evenements\Http\Controllers\MeetingController; 
use Modules\Evenements\Http\Controllers\AnnonceController;
use Modules\Evenements\Http\Controllers\RapportController;
use Modules\Evenements\Http\Controllers\AwardController;
use Modules\Evenements\Http\Controllers\PromotionController;
use Modules\Evenements\Http\Controllers\DashboardController;
use Modules\Evenements\Http\Controllers\EventController;
use Modules\Evenements\Http\Controllers\TransferController;

// Routes pour les entreprises
Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
    // Routes du module Evenements
    Route::prefix('evenements')->name('evenements.')->group(function () {
        // Tableau de bord
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Événements (calendrier)
        Route::get('events/calendar', [EventController::class, 'calendar'])->name('events.calendar');
        Route::post('events/{event}/export', [EventController::class, 'export'])->name('events.export');
        Route::patch('events/{event}/cancel', [EventController::class, 'cancel'])->name('events.cancel');
        Route::resource('events', EventController::class)->names('events');
        
        // Réunions
        Route::resource('meetings', MeetingController::class)->names('meetings');
        Route::get('meetings/calendar', [MeetingController::class, 'calendar'])->name('meetings.calendar');
        Route::post('meetings/{meeting}/export', [MeetingController::class, 'export'])->name('meetings.export');

        // Annonces
        Route::resource('annonces', AnnonceController::class)->names('annonces');
        Route::post('annonces/{annonce}/export', [AnnonceController::class, 'export'])->name('annonces.export');

        // Rapports
        Route::resource('rapports', RapportController::class)->names('rapports');
        Route::post('rapports/{rapport}/export', [RapportController::class, 'export'])->name('rapports.export');

        // Récompenses
        Route::resource('awards', AwardController::class)->names('awards');
        Route::post('awards/quick-create', [AwardController::class, 'quickCreate'])->name('awards.quick-create');
        Route::post('awards/{award}/export', [AwardController::class, 'export'])->name('awards.export');

        // Promotions
        Route::resource('promotions', PromotionController::class)->names('promotions');
        Route::post('promotions/export', [PromotionController::class, 'export'])->name('promotions.export');

        // Transferts - Affectations 
        Route::resource('transfers', TransferController::class)->names('transfers');
        Route::post('transfers/{transfer}/approve', [TransferController::class, 'approve'])->name('transfers.approve');
        Route::post('transfers/{transfer}/reject', [TransferController::class, 'reject'])->name('transfers.reject');
        Route::post('transfers/{transfer}/export', [TransferController::class, 'export'])->name('transfers.export');

        // API pour le calendrier
        Route::prefix('api/evenements')->group(function () {
            Route::get('events', [EventController::class, 'getEvents'])->name('api.events.index');
            Route::post('events', [EventController::class, 'store'])->name('api.events.store');
            Route::put('events/{event}', [EventController::class, 'update'])->name('api.events.update');
            Route::delete('events/{event}', [EventController::class, 'destroy'])->name('api.events.destroy');
        });
    });
});
