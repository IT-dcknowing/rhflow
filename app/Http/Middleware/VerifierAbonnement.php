<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloque l'accès quand l'abonnement de l'entreprise est échu.
 *
 * Le chemin de renouvellement, la déconnexion et les pages publiques
 * restent toujours accessibles (config subscription.routes_autorisees),
 * sans quoi l'utilisateur bloqué ne pourrait plus régulariser.
 */
class VerifierAbonnement
{
    public function __construct(private SubscriptionService $abonnement)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        // Le Super Admin garde l'accès : c'est lui qui prolonge les abonnements
        if (Auth::user()->type === 'super_admin') {
            return $next($request);
        }

        if ($this->routeAutorisee($request)) {
            return $next($request);
        }

        if (!$this->abonnement->doitBloquer()) {
            return $next($request);
        }

        $donnees = [
            'echeance' => $this->abonnement->dateEcheance(),
            'plan' => optional($this->abonnement->plan())->name,
            'estProprietaire' => Auth::user()->type === 'company',
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Abonnement échu. Renouvelez votre abonnement pour continuer.',
                'status' => 'subscription_expired',
                'echeance' => optional($donnees['echeance'])->toDateString(),
            ], 402);
        }

        // 402 Payment Required : le code décrit exactement la situation et
        // évite de confondre ce blocage avec un refus de droits (403).
        return response()->view('errors.abonnement-expire', $donnees, 402);
    }

    /**
     * La route demandée fait-elle partie des exceptions ?
     */
    private function routeAutorisee(Request $request): bool
    {
        $nom = $request->route() ? $request->route()->getName() : null;

        if ($nom === null) {
            return false;
        }

        foreach ((array) config('subscription.routes_autorisees', []) as $prefixe) {
            if ($nom === $prefixe || Str::startsWith($nom, $prefixe)) {
                return true;
            }
        }

        return false;
    }
}
