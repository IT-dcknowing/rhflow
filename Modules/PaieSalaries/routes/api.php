<?php

use Illuminate\Support\Facades\Route;
use Modules\PaieSalaries\Http\Controllers\PaieSalariesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('paiesalaries', PaieSalariesController::class)->names('paiesalaries');
});
