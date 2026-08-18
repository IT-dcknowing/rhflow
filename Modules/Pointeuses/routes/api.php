<?php

use Illuminate\Support\Facades\Route;
use Modules\Pointeuses\Http\Controllers\PointeusesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pointeuses', PointeusesController::class)->names('pointeuses');
});
