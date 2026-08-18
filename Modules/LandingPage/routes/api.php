<?php

use Illuminate\Support\Facades\Route;
use Modules\LandingPage\Http\Controllers\LandingPageController;
use Modules\LandingPage\Http\Controllers\PaymentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('landingpages', LandingPageController::class)->names('landingpage');
});

// Webhooks
Route::match(['get', 'post'], 'geniuspay/webhook', 'Modules\LandingPage\Http\Controllers\PaymentController@webhook')->name('geniuspay.webhook');
