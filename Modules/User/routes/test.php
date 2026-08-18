<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\TestController;

// Route de test pour vérifier le module User
Route::get('/test-module-user', [TestController::class, 'test'])->name('test.module.user');
