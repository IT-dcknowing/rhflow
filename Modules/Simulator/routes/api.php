<?php

use Illuminate\Support\Facades\Route;
use Modules\Simulator\Http\Controllers\SimulatorController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('simulators', SimulatorController::class)->names('simulator');
});
