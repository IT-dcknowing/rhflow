<?php

use Illuminate\Support\Facades\Route;
use Modules\Chatbot\Http\Controllers\ChatbotController;

// Routes pour le chat et le rapport
Route::post('/chat', [ChatbotController::class, 'chat']);
Route::post('/report', [ChatbotController::class, 'generateReport']);
Route::get('/guide', [ChatbotController::class, 'getGuideMarkdown']);
