<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        \Nwidart\Modules\LaravelModulesServiceProvider::class,
    ])
     ->withMiddleware(function (Middleware $middleware): void {
        // Un utilisateur déjà connecté qui atteint /login est renvoyé vers SON dashboard,
        // et non vers la landing page (comportement par défaut de Laravel quand aucune
        // route nommée "dashboard" ou "home" n'existe).
        $middleware->redirectUsersTo(fn () => \App\Http\Controllers\Auth\LoginController::dashboardUrl());

        $middleware->alias([
            'super.admin'        => \App\Http\Middleware\SuperAdmin::class,
            'maintenance'        => \App\Http\Middleware\MaintenanceMode::class,
            'verify.hub.token'   => \App\Http\Middleware\VerifyHubToken::class,
            'abonnement'         => \App\Http\Middleware\VerifierAbonnement::class,
        ]);

        // Contrôle d'échéance d'abonnement sur toutes les pages web.
        // Le middleware ne bloque rien tant que config('subscription.blocage_actif')
        // est à false, et laisse toujours passer le chemin de renouvellement.
        $middleware->web(append: [
            \App\Http\Middleware\VerifierAbonnement::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Intercepter toutes les exceptions pour afficher la page premium, même en débug
        $exceptions->render(function (Throwable $e, Request $request) {
            // Si c'est une requête API/JSON, on garde le format JSON
            if ($request->expectsJson()) {
                $statusCode = 500;
                if (method_exists($e, 'getStatusCode')) {
                    $statusCode = $e->getStatusCode();
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    $statusCode = $e->getStatusCode();
                }

                return response()->json([
                    'message' => $e->getMessage(),
                    'status' => 'error',
                    'code' => $statusCode,
                ], $statusCode);
            }

            // Pour les requêtes Web classiques (HTML)
            // On force l'affichage de notre vue premium pour les erreurs fatales uniquement
            // On NE DOIT PAS intercepter les exceptions d'auth ou de validation qui gèrent leurs propres redirections
            if (
                !($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) &&
                !($e instanceof \Illuminate\Auth\AuthenticationException) &&
                !($e instanceof \Illuminate\Validation\ValidationException)
            ) {
                // Une exception HTTP (403, 419, 429...) garde son propre code et sa page.
                // Sans ça, un simple "accès non autorisé" s'affichait en "Erreur serveur 500".
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                    $statusCode = $e->getStatusCode();
                    $view = view()->exists("errors.{$statusCode}") ? "errors.{$statusCode}" : 'errors.500';

                    return response()->view($view, [
                        'exception' => $e
                    ], $statusCode);
                }

                // On prépare les données pour la vue
                return response()->view('errors.500', [
                    'exception' => $e
                ], 500);
            }
        });

        // Rediriger explicitement les erreurs 404 vers notre page premium
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Resource not found',
                    'status' => 'error',
                    'code' => 404
                ], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // Logger les erreurs critiques
        $exceptions->report(function (Throwable $e) {
            if ($e instanceof \Exception && $e->getCode() >= 500) {
                Log::critical('Critical error occurred', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        });
    })->create();
