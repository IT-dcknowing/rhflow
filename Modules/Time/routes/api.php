<?php

use Illuminate\Support\Facades\Route;
use Modules\Time\Http\Controllers\TimeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('times', TimeController::class)->names('time');
});
