<?php

use Illuminate\Support\Facades\Route;
use Modules\Leaves\Http\Controllers\LeavesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('leaves', LeavesController::class)->names('leaves');
});
