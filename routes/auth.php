<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Routes d'Authentification
|--------------------------------------------------------------------------
|
| Ces routes gèrent l'authentification pour tous les types d'utilisateurs
| du système RH Flow (Super Admin, Entreprise, HR, Paie, Employé)
|
*/

// Routes publiques (accessibles à tous, connectés ou non)
Route::get('/', [\Modules\LandingPage\Http\Controllers\LandingPageController::class, 'index'])->name('landingpage');
Route::get('/simulateur', [\Modules\LandingPage\Http\Controllers\LandingPageController::class, 'simulateur'])->name('simulateur');
Route::get('/contact', [\Modules\LandingPage\Http\Controllers\LandingPageController::class, 'contact'])->name('contact');

// Routes d'authentification (uniquement pour les visiteurs non-connectés)
Route::middleware('guest')->group(function () {
    // Routes de paiement
    Route::post('/payment/initiate', [\Modules\LandingPage\Http\Controllers\PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::get('/payment/callback', [\Modules\LandingPage\Http\Controllers\PaymentController::class, 'handleCallback'])->name('payment.callback');
    Route::post('/payment/webhook/wave', [\Modules\LandingPage\Http\Controllers\PaymentController::class, 'webhook'])->name('payment.webhook.wave');

    // Connexion
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    // Mot de passe oublié
    Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
    // Limité à 6 envois par minute : évite l'envoi massif d'emails et le test d'adresses en rafale
    Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->middleware('throttle:6,1')->name('password.email');

    // Réinitialisation de mot de passe (lien reçu par email)
    Route::get('/reset-password/{code}', [LoginController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password/{code}', [LoginController::class, 'resetPassword'])->middleware('throttle:6,1')->name('password.update');
});

// Routes nécessitant une authentification
Route::middleware('auth')->group(function () {
    // Déconnexion
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});