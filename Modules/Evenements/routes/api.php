<?php

use Illuminate\Support\Facades\Route;
use Modules\Evenements\Http\Controllers\EvenementsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('evenements', EvenementsController::class)->names('evenements');
});
