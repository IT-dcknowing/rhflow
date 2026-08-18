<?php

use Illuminate\Support\Facades\Route;
use Modules\Ruptures\Http\Controllers\RupturesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ruptures', RupturesController::class)->names('ruptures');
});
