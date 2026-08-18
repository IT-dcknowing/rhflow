<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Abonnement de l'utilisateur connecté.
 *
 * La date d'échéance (users.plan_expire_date) et le plan sont portés par le
 * compte propriétaire de l'entreprise. Les comptes RH, Paie et Employé n'en
 * ont pas : on remonte au propriétaire via leur company_id.
 */
class SubscriptionService
{
    /**
     * Compte portant l'abonnement pour l'utilisateur connecté.
     */
    public function proprietaire(): ?User
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();

        if ($user->type === 'company') {
            return $user;
        }

        if (empty($user->company_id)) {
            return null;
        }

        $entreprise = Company::find($user->company_id);

        return $entreprise ? User::find($entreprise->user_id) : null;
    }

    /**
     * Date d'échéance de l'abonnement, ou null si aucune n'est définie.
     */
    public function dateEcheance(): ?Carbon
    {
        $proprietaire = $this->proprietaire();

        if (!$proprietaire || empty($proprietaire->plan_expire_date)) {
            return null;
        }

        return Carbon::parse($proprietaire->plan_expire_date)->endOfDay();
    }

    /**
     * Jours restants avant l'échéance. Négatif si elle est dépassée,
     * null si aucune échéance n'est définie.
     */
    public function joursRestants(): ?int
    {
        $echeance = $this->dateEcheance();

        return $echeance === null
            ? null
            : (int) Carbon::now()->startOfDay()->diffInDays($echeance->copy()->startOfDay(), false);
    }

    /**
     * L'échéance est-elle dépassée ?
     * Un compte sans échéance n'est jamais considéré comme expiré.
     */
    public function estExpire(): bool
    {
        $echeance = $this->dateEcheance();

        return $echeance !== null && Carbon::now()->greaterThan($echeance);
    }

    /**
     * L'accès doit-il être bloqué ? Tient compte du délai de grâce et de
     * l'interrupteur config('subscription.blocage_actif').
     */
    public function doitBloquer(): bool
    {
        if (!config('subscription.blocage_actif', false)) {
            return false;
        }

        $echeance = $this->dateEcheance();

        if ($echeance === null) {
            return false;
        }

        $grace = (int) config('subscription.jours_grace', 0);

        return Carbon::now()->greaterThan($echeance->copy()->addDays($grace));
    }

    /**
     * Faut-il afficher le rappel d'échéance ?
     * Vrai à l'approche de l'échéance et tant qu'elle est dépassée sans blocage.
     */
    public function doitAlerter(): bool
    {
        $seuil = (int) config('subscription.jours_alerte', 0);

        if ($seuil <= 0) {
            return false;
        }

        $jours = $this->joursRestants();

        return $jours !== null && $jours <= $seuil;
    }

    /**
     * Le plan d'abonnement en cours, ou null.
     */
    public function plan()
    {
        $proprietaire = $this->proprietaire();

        if (!$proprietaire || !$proprietaire->plan) {
            return null;
        }

        return \App\Models\Plan::find($proprietaire->plan);
    }

    /**
     * Données prêtes pour l'affichage du rappel.
     */
    public function resume(): array
    {
        $jours = $this->joursRestants();
        $echeance = $this->dateEcheance();

        return [
            'echeance' => $echeance,
            'jours_restants' => $jours,
            'expire' => $this->estExpire(),
            'alerter' => $this->doitAlerter(),
            'plan' => optional($this->plan())->name,
            'est_proprietaire' => Auth::check() && Auth::user()->type === 'company',
        ];
    }
}
