<?php

use Illuminate\Support\Facades\Route;
use Modules\Declarations\Http\Controllers\DeclarationsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('declarations', DeclarationsController::class)->names('declarations');
});
