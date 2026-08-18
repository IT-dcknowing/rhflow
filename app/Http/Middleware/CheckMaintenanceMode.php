<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si le mode maintenance est activé
        $maintenanceMode = Setting::get('maintenance_mode', false);
        $maintenanceMessage = Setting::get('maintenance_message', 'Le site est en maintenance. Nous serons bientôt de retour.');

        // Si le mode maintenance est activé
        if ($maintenanceMode) {
            // Les super admins peuvent toujours accéder
            if (Auth::check() && Auth::user()->type === 'super_admin') {
                return $next($request);
            }

            // Liste des routes exemptées du mode maintenance
            $exemptedRoutes = [
                'login',
                'password.request',
                'password.email',
                'password.reset',
                'password.update',
                'health', // Route de health check pour les services externes
            ];

            // Vérifier si la route actuelle est exemptée
            $currentRoute = $request->route() ? $request->route()->getName() : null;
            if ($currentRoute && in_array($currentRoute, $exemptedRoutes)) {
                return $next($request);
            }

            // Vérifier si l'utilisateur essaie d'accéder à une route admin
            if ($request->is('admin/*') || $request->is('super-admin/*')) {
                if (Auth::check()) {
                    // Si l'utilisateur est connecté mais n'est pas super admin, le déconnecter et afficher la page de maintenance
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                return response()->view('maintenance', [
                    'message' => $maintenanceMessage,
                    'title' => 'Maintenance en cours'
                ], 503);
            }

            // Pour les autres routes, vérifier si l'utilisateur est connecté
            if (Auth::check()) {
                // Les utilisateurs connectés peuvent accéder aux pages normales
                return $next($request);
            } else {
                // Les utilisateurs non connectés sont redirigés vers la connexion avec un avertissement
                return redirect()->route('login')->with('warning', 'Le site est actuellement en maintenance. Veuillez réessayer plus tard.');
            }
        }

        return $next($request);
}
