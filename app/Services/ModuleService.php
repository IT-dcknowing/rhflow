<?php

namespace App\Services;

use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class ModuleService
{
    /**
     * Vérifier si un module est actif pour l'utilisateur connecté (méthode principale)
     */
    public function isModuleActive(string $moduleAlias): bool
    {
        $plan = $this->planAbonnement();

        if (!$plan) {
            return false;
        }

        $module = Module::where('alias', $moduleAlias)->first();

        if (!$module) {
            return false;
        }

        return $module->isActiveForPlan($plan);
    }

    /**
     * Obtenir tous les modules actifs pour l'utilisateur connecté
     */
    public function getActiveModules(): array
    {
        $plan = $this->planAbonnement();

        if (!$plan) {
            return [];
        }

        return $plan->activeModules()->pluck('alias')->toArray();
    }

    /**
     * Plan d'abonnement applicable à l'utilisateur connecté.
     *
     * Le plan est porté par le compte propriétaire de l'entreprise (type
     * 'company'). Les comptes RH et Paie n'en ont pas : on remonte au
     * propriétaire via leur company_id, sans quoi aucun module ne leur
     * apparaît comme actif et leur menu reste vide.
     */
    private function planAbonnement(): ?\App\Models\Plan
    {
        if (!Auth::check()) {
            return null;
        }

        // Les employés n'ouvrent aucune section de gestion : inutile de
        // leur résoudre un plan.
        if (!in_array(Auth::user()->type, ['company', 'hr', 'paie', 'payroll'])) {
            return null;
        }

        return app(SubscriptionService::class)->plan();
    }

    /**
     * Vérifier si un module est actif (compatibilité avec l'ancien système)
     */
    public function isModuleEnabled(string $moduleAlias): bool
    {
        // Fallback vers le système global si pas de plan spécifique
        $globalStatus = $this->getGlobalModuleStatus();
        if (isset($globalStatus[$moduleAlias])) {
            return $globalStatus[$moduleAlias];
        }

        return $this->isModuleActive($moduleAlias);
    }

    /**
     * Obtenir le statut global des modules (pour compatibilité)
     */
    private function getGlobalModuleStatus(): array
    {
        $path = base_path('modules_statuses.json');
        if (file_exists($path)) {
            $content = json_decode(file_get_contents($path), true);
            return $content ?: [];
        }

        return [];
    }
}
