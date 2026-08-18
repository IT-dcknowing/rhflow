<?php

use Illuminate\Support\Facades\Route;
use Modules\Impot\Http\Controllers\ImpotController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('impots', ImpotController::class)->names('impot');
});
