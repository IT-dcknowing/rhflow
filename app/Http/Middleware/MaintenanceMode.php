<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si le mode maintenance est activé
        $maintenanceMode = Setting::get('maintenance_mode', false);
        $maintenanceMessage = Setting::get('maintenance_message', 'Le site est en maintenance.');

        // Si le mode maintenance est activé et que l'utilisateur n'est pas un super admin
        if ($maintenanceMode && (!Auth::check() || Auth::user()->type !== 'super_admin')) {
            // Autoriser l'accès aux routes d'authentification et à la page de maintenance elle-même
            if ($request->is('login') || $request->is('maintenance') || $request->routeIs('login.*') || $request->is('logout')) {
                return $next($request);
            }

            // Rediriger vers la page de maintenance
            return response()->view('maintenance', [
                'message' => $maintenanceMessage,
                'title' => 'Maintenance - RH Flow'
            ]);
        }

        return $next($request);
    }
}
