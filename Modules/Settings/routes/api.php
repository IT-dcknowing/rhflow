<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SettingsController;

Route::middleware(['web', 'auth'])->prefix('settings')->name('settings.')->group(function () {
    // API pour les suggestions de localisation
    Route::get('location-suggestions', [SettingsController::class, 'getLocationSuggestions'])->name('location.suggestions');
});
