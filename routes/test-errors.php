<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route pour tester l'erreur 500
Route::get('/test-500', function () {
    throw new \Exception('Ceci est une erreur de test pour la page 500');
});

// Route pour tester l'erreur 419 (session expirée)
Route::get('/test-419', function () {
    // Simuler une erreur CSRF en invalidant le token
    abort(419, 'Page expired');
});

// Route pour tester l'erreur 404
Route::get('/test-404', function () {
    abort(404, 'Page not found');
});
