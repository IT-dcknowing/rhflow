<?php

namespace App\Services;

use Modules\Employees\Models\Employee;
use App\Models\PaiePeriode;
use Modules\Leaves\Models\Leave;
use Modules\Loans\Models\Loan;
use Modules\NatureAvantage\Models\Avantage;
use App\Models\Allowance;
use Auth;

class SalaryService
{
    // -------------------------------------------------------
    // SMIG et constantes légales (Décret n°2022-986)
    // -------------------------------------------------------
    const SMIG_MENSUEL  = 75000;
    const SMIG_HORAIRE  = 432.7;    // 75 000 / 173,33
    const CNPS_PLAFOND  = 3375000;  // 45 × 75 000 (Art. CNPS 2023)

    public function netSalary(Employee $employee, $periode_id)
    {
        // 1️⃣ Vérifications de base
        if (!$periode_id)
            return 0;

        $periode = PaiePeriode::find($periode_id);
        if (!$periode)
            return 0;

        if (!$employee)
            return 0;

        // 2️⃣ Pré-chargement sécurisé
        $brut_total = $employee->get_brut_salary($periode_id);
        // Base imposable IRS tirée directement de la méthode get_salary_imposable() (inclut sursalaire stable)
        $brut = $employee->get_salary_imposable($periode_id);
        $brut_cnps = $employee->get_salary_social($periode_id);

        if ($brut_total <= 0)
            return 0;

        $autre_retenue = $employee->get_Autre_retenue($periode_id);
        $loan = $employee->get_loan_retenue($periode_id);

        // 3️⃣ CMU + CNPS
        $cmu = $employee->cmu;

        $coticmu = ($cmu < 7)
            ? round($cmu * 500)
            : 3000 + round(($cmu - 6) * 1000);

        // CNPS salariale plafonné à 45 × SMIG = 3 375 000 FCFA
        $brut_cnps_plafonne = min($brut_cnps, self::CNPS_PLAFOND);
        $cnps = round($brut_cnps_plafonne * 6.3 / 100);

        // 4️⃣ Impôts (fonction dédiée) — base = salary, toujours normalisée à 30 jours
        $nbre_jours = $employee->get_jours_work($periode_id);
        $impots = $this->calculImpots($brut, $nbre_jours, $employee->parts, $cnps, $coticmu);

        // 5️⃣ Avantages en nature
        $avantagesReal = Avantage::where('employee_id', $employee->id)
            ->where('is_active', 1)
            ->where('periode_id', $periode_id)
            ->sum('amount_reel');

        // 6️⃣ Cantine + primes
        $allowances = Allowance::where('employee_id', $employee->id)
            ->where('periode_id', $periode_id)
            ->get();

        $cantine = $allowances->where('allowance_option', 31)->sum('amount');
        $prime = $allowances->whereIn('allowance_option', [27, 28])->sum('amount');

        // 7️⃣ Calcul net = Brut - CNPS - CMU - Impôts - Prêts - Autres retenues
        $net = $brut_total
            - $cnps
            - $coticmu
            - ($impots + $cantine + $avantagesReal)
            - ($autre_retenue + $loan);

        // 8️⃣ Cas Stagiaire / Prestataire
        if (in_array($employee->statut_emp, ['Stagiaire', 'Apprenti'])) {
            return ($employee->tax_payer_id == 30)
                ? $prime
                : ($prime / 30) * $employee->tax_payer_id;
        }

        if ($employee->statut_emp == 'Prestataire') {
            return $employee->salary;
        }

        return max(0, $net);
    }

    private function calculImpots($brut, $nbre_jours, $parts, $cnps, $coticmu)
    {
        if ($brut <= 0)
            return 0;

        // 1. Calcul de l'IRS Brut selon les tranches mensuelles (Barème 2024)
        $j = intval($nbre_jours);
        $normalized_days = ($j == 28 || $j == 29 || $j == 31 || $j == 0) ? 30 : $j;

        // Base imposable = Brut Imposable (Réforme 2024: suppression de l'abattement de 20%)
        $base_imposable_reelle = $brut;
        if ($base_imposable_reelle <= 0)
            return 0;

        $base_m = ($normalized_days >= 30) ? $base_imposable_reelle : ($base_imposable_reelle * 30 / $normalized_days);

        if ($base_m <= 75000) {
            $irs_m = 0;
        } else if ($base_m <= 240000) {
            $irs_m = ($base_m - 75000) * 0.16;
        } else if ($base_m <= 800000) {
            $irs_m = (165000 * 0.16) + (($base_m - 240000) * 0.21);
        } else if ($base_m <= 2400000) {
            $irs_m = (165000 * 0.16) + (560000 * 0.21) + (($base_m - 800000) * 0.24);
        } else if ($base_m <= 8000000) {
            $irs_m = (165000 * 0.16) + (560000 * 0.21) + (1600000 * 0.24) + (($base_m - 2400000) * 0.28);
        } else {
            $irs_m = (165000 * 0.16) + (560000 * 0.21) + (1600000 * 0.24) + (5600000 * 0.28) + (($base_m - 8000000) * 0.32);
        }

        $resultimpricf = ($normalized_days >= 30) ? $irs_m : ($irs_m * $normalized_days / 30);

        // 2. Application des réductions fixes pour charges de famille (Barème 2024)
        $p = (float) str_replace(',', '.', (string) $parts);
        $fixedTable = [
            "1" => 0, "1.5" => 5500, "2" => 11000, "2.5" => 16500,
            "3" => 22000, "3.5" => 27500, "4" => 33000, "4.5" => 38500, "5" => 44000,
        ];
        $valeurFixe = 0;
        foreach ($fixedTable as $partsKey => $amount) {
            if (abs($p - (float) $partsKey) < 0.01) {
                $valeurFixe = $amount;
                break;
            }
        }
        if ($p > 5) {
            $valeurFixe = 44000;
        }

        // Proratisation temporelle de la réduction si mois incomplet (norme système)
        if ($normalized_days < 30 && $normalized_days > 0) {
            $reduction = round(($valeurFixe / 30) * $normalized_days);
        } else {
            $reduction = $valeurFixe;
        }

        $retenue_irs = round($resultimpricf - $reduction);

        // 3. Retourne le total des retenues obligatoires (IRS + CNPS + CMU)
        return max(0, $retenue_irs) + $cnps + $coticmu;
    }

    // -------------------------------------------------------
    // PRIME D'ANCIENNETÉ AUTOMATIQUE — Art. 55 CCI juillet 1977
    // -------------------------------------------------------
    /**
     * Calcule la prime d'ancienneté selon l'Art. 55 de la CCI de juillet 1977.
     *
     * Règle :
     *   - < 25 mois de présence → 0 FCFA
     *   - 25 à 36 mois         → 2% du salaire de base
     *   - > 36 mois            → 2% + 1% par année supplémentaire (plafonné à 25%)
     *
     * @param  Employee    $employee     L'employé concerné
     * @param  PaiePeriode $periode      La période de paie de référence
     * @return float                     Montant de la prime d'ancienneté (FCFA)
     */
    public function calculPrimeAnciennete(Employee $employee, PaiePeriode $periode): float
    {
        try {
            // Date d'embauche (company_doj prioritaire, sinon start_date)
            $date_embauche_raw = $employee->company_doj ?? $employee->start_date;
            if (!$date_embauche_raw) return 0.0;

            $date_embauche = new \DateTime(
                $date_embauche_raw instanceof \Carbon\Carbon
                    ? $date_embauche_raw->format('Y-m-d')
                    : $date_embauche_raw
            );
            $date_ref = new \DateTime(
                $periode->date_fin instanceof \Carbon\Carbon
                    ? $periode->date_fin->format('Y-m-d')
                    : $periode->date_fin
            );

            $diff = $date_embauche->diff($date_ref);
            $total_mois = ($diff->y * 12) + $diff->m;

            // Art. 55 CCI : pas de prime avant 25 mois révolus
            if ($total_mois < 25) return 0.0;

            // Calcul du taux :
            // Phase 1 (25e–36e mois) → 2%
            // Phase 2 (> 36 mois)    → 2% + 1%/an supplémentaire, max 25%
            $annees_completes = (int) floor($total_mois / 12);
            if ($annees_completes <= 2) {
                $taux = 2;
            } else {
                $taux = min(2 + ($annees_completes - 2), 25);
            }

            // Base de calcul = salaire de base mensuel de l'employé
            $base_calcul = (float) ($employee->salary ?? 0);
            if ($base_calcul <= 0) return 0.0;

            return (float) round($base_calcul * $taux / 100);

        } catch (\Throwable $e) {
            \Log::warning("Erreur calcul prime ancienneté employé #{$employee->id}: " . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Calcule tous les détails des retenues par défaut (rubriques 301 à 412)
     */
    public function getDefaultDeductionsDetails(Employee $employee, $periode_id)
    {
        $details = [];
        $company = $employee->company;
        if (!$company)
            $company = \App\Models\Company::find(Auth::user()->company_id);

        // Base imposable et base sociale
        $sbi = $employee->get_salary_imposable($periode_id);
        $sbs = $employee->get_salary_social($periode_id);

        // --- 1. SALARIALES ---

        // CMU
        $cmu_count = (int) $employee->cmu;
        $coticmu_sal = ($cmu_count < 7) ? ($cmu_count * 500) : (3000 + ($cmu_count - 6) * 1000);
        $coticmu_pat = ($cmu_count < 7) ? ($cmu_count * 500) : 3000;

        // CNPS Salariale (6.3%) — plafonnée à 45 × SMIG = 3 375 000 FCFA (Art. CNPS 2023)
        $sbs_plafonne = min($sbs, self::CNPS_PLAFOND);
        $cnps_sal = round($sbs_plafonne * 6.3 / 100);

        // IRS
        $j = intval($employee->tax_payer_id);
        $nbre_jours = ($j == 28 || $j == 29 || $j == 31 || $j == 0) ? 30 : $j;
        $parts = $employee->parts;

        $irs_brut = 0;
        $base_imposable_reelle = $sbi;
        $base_mensuelle = ($nbre_jours >= 30 || $nbre_jours == 0) ? $base_imposable_reelle : ($base_imposable_reelle * 30 / $nbre_jours);

        if ($base_mensuelle <= 75000) {
            $irs_brut_mensuel = 0;
        } else if ($base_mensuelle <= 240000) {
            $irs_brut_mensuel = ($base_mensuelle - 75000) * 0.16;
        } else if ($base_mensuelle <= 800000) {
            $irs_brut_mensuel = (165000 * 0.16) + (($base_mensuelle - 240000) * 0.21);
        } else if ($base_mensuelle <= 2400000) {
            $irs_brut_mensuel = (165000 * 0.16) + (560000 * 0.21) + (($base_mensuelle - 800000) * 0.24);
        } else if ($base_mensuelle <= 8000000) {
            $irs_brut_mensuel = (165000 * 0.16) + (560000 * 0.21) + (1600000 * 0.24) + (($base_mensuelle - 2400000) * 0.28);
        } else {
            $irs_brut_mensuel = (165000 * 0.16) + (560000 * 0.21) + (1600000 * 0.24) + (5600000 * 0.28) + (($base_mensuelle - 8000000) * 0.32);
        }

        $irs_brut = ($nbre_jours >= 30) ? $irs_brut_mensuel : ($irs_brut_mensuel * $nbre_jours / 30);

        // RICF — Barème 2024
        $p = (float) str_replace(',', '.', (string) $parts);
        $fixedTable = [
            "1" => 0, "1.5" => 5500, "2" => 11000, "2.5" => 16500,
            "3" => 22000, "3.5" => 27500, "4" => 33000, "4.5" => 38500, "5" => 44000,
        ];
        $valeurFixe = 0;
        foreach ($fixedTable as $partsKey => $amount) {
            if (abs($p - (float) $partsKey) < 0.01) {
                $valeurFixe = $amount;
                break;
            }
        }
        if ($p > 5) {
            $valeurFixe = 44000;
        }
        $tauxRed = 0;

        if ($nbre_jours < 30 && $nbre_jours > 0) {
            $reduction = round(($valeurFixe / 30) * $nbre_jours);
        } else {
            $reduction = $valeurFixe;
        }
        $irs_net = max(0, round($irs_brut - $reduction));

        // --- Double limite assurance maladie individuelle / retraite complémentaire (Art. 116 al.9 CGI) ---
        // Limite = min(10% × SBI, 320 000 FCFA)
        $limite_assurance = min(round($sbi * 0.10), 320000);

        $allowances_periode = \App\Models\Allowance::where('employee_id', $employee->id)
            ->where('periode_id', $periode_id)
            ->get();

        $prime_assurance_maladie = $allowances_periode
            ->filter(fn($a) => stripos($a->title ?? '', 'assurance maladie') !== false
                             || stripos($a->title ?? '', 'assurance individuelle') !== false)
            ->sum('amount');

        $prime_assurance_retraite = $allowances_periode
            ->filter(fn($a) => stripos($a->title ?? '', 'retraite complémentaire') !== false
                             || stripos($a->title ?? '', 'assurance retraite') !== false)
            ->sum('amount');

        // La part dépassant la limite double est imposable — peut être loggée ou affichée
        $imposable_assurance = max(0, ($prime_assurance_maladie + $prime_assurance_retraite) - $limite_assurance);

        // --- 2. PATRONALES ---
        // CNPS patronale (7.7%) — même plafond 3 375 000 FCFA
        $cnps_pat = round($sbs_plafonne * 7.7 / 100);
        $cne = round($sbi * 1.2 / 100);
        $cne_expat = ($employee->charge_expat != 'local') ? round($sbi * 9.2 / 100) : 0;
        $apprentissage = round($sbi * 0.4 / 100);
        // FPC : taux légal 1,2% MAIS payé à 0,6%/mois + régularisation annuelle (Art. 134 al.4 CGI)
        $fpc = round($sbi * 0.6 / 100);

        // Accident de travail & Prestation familiale (Plafonné à 75 000 FCFA)
        $base_at_pf = min($sbs, 75000);
        $act_taux_str = str_replace(',', '.', $company->accident_taux ?? '0.03');
        $act_taux = (float) $act_taux_str;
        $accident = round($base_at_pf * $act_taux);
        $pf = round($base_at_pf * 5.75 / 100);

        // --- Assemblage des Rubriques ---
        $rubriques = [
            ['code' => '401', 'libelle' => 'Impôts bruts avant RICF',           'salariale' => 1, 'patronale' => 0, 'base' => $sbi,      'taux' => '',      'amount' => round($irs_brut), 'ordre' => 1,  'type_id' => 1],
            ['code' => '402', 'libelle' => 'Réduction pour Charges de Famille',  'salariale' => 1, 'patronale' => 0, 'base' => $sbi,      'taux' => '',      'amount' => $reduction,       'ordre' => 2,  'type_id' => 1],
            ['code' => '403', 'libelle' => 'Impôts Nets',                        'salariale' => 1, 'patronale' => 0, 'base' => $sbi,      'taux' => '',      'amount' => $irs_net,         'ordre' => 3,  'type_id' => 1],
            ['code' => '301', 'libelle' => 'Cotisation Retraite CNPS',           'salariale' => 1, 'patronale' => 0, 'base' => $sbs_plafonne, 'taux' => '6.3%',  'amount' => $cnps_sal,   'ordre' => 4,  'type_id' => 1],
            ['code' => '302', 'libelle' => 'Couverture Maladie Universelle',     'salariale' => 1, 'patronale' => 0, 'base' => 1000,      'taux' => '',      'amount' => $coticmu_sal,     'ordre' => 5,  'type_id' => 1],
            ['code' => '409', 'libelle' => 'Contribution Nationale',             'salariale' => 0, 'patronale' => 1, 'base' => $sbi,      'taux' => '1.2%',  'amount' => $cne,             'ordre' => 6,  'type_id' => 1],
            ['code' => '410', 'libelle' => 'Contribution Employeur (Expatrié)',  'salariale' => 0, 'patronale' => 1, 'base' => $sbi,      'taux' => '9.2%',  'amount' => $cne_expat,       'ordre' => 7,  'type_id' => 1],
            ['code' => '411', 'libelle' => "Taxe d'Apprentissage",               'salariale' => 0, 'patronale' => 1, 'base' => $sbi,      'taux' => '0.4%',  'amount' => $apprentissage,   'ordre' => 8,  'type_id' => 1],
            ['code' => '412', 'libelle' => 'Taxe F.P.C (0,6%/mois)',             'salariale' => 0, 'patronale' => 1, 'base' => $sbi,      'taux' => '0.6%',  'amount' => $fpc,             'ordre' => 9,  'type_id' => 1],
            ['code' => '308', 'libelle' => 'Cotisation retraite employeur',      'salariale' => 0, 'patronale' => 1, 'base' => $sbs_plafonne, 'taux' => '7.7%',  'amount' => $cnps_pat,   'ordre' => 11, 'type_id' => 1],
            ['code' => '305', 'libelle' => 'Accident de travail',                'salariale' => 0, 'patronale' => 1, 'base' => $base_at_pf,'taux' => ($act_taux * 100) . '%', 'amount' => $accident, 'ordre' => 12, 'type_id' => 1],
            ['code' => '306', 'libelle' => 'Prestation Familiale',               'salariale' => 0, 'patronale' => 1, 'base' => $base_at_pf,'taux' => '5.75%', 'amount' => $pf,             'ordre' => 13, 'type_id' => 1],
            ['code' => '307', 'libelle' => 'CMU Employeur',                      'salariale' => 0, 'patronale' => 1, 'base' => 1000,      'taux' => '',      'amount' => $coticmu_pat,     'ordre' => 14, 'type_id' => 1],
        ];

        return $rubriques;
    }

    /**
     * Enregistre (crée ou met à jour) les retenues légales d'un salarié pour une période :
     * ITS, CNPS, CMU et charges patronales. Remplace l'application manuelle « Voir/Appliquer ».
     */
    public function appliquerRetenuesLegales(Employee $employee, PaiePeriode $periode): void
    {
        foreach ($this->getDefaultDeductionsDetails($employee, $periode->id) as $ded) {
            // Colonne « NOMBRE » du bulletin : parts pour la réduction, bénéficiaires pour la CMU
            $nombre = null;
            if ($ded['code'] === '402') {
                $nombre = $employee->parts;
            } elseif (in_array($ded['code'], ['302', '307'], true)) {
                $nombre = $employee->cmu;
            }

            \Modules\PaieSalaries\Models\Retenue::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'periode_id' => $periode->id,
                    'code' => $ded['code'],
                ],
                [
                    'libelle' => $ded['libelle'],
                    'type_retenue_id' => $ded['type_id'],
                    'ordre' => $ded['ordre'],
                    'salariale' => $ded['salariale'],
                    'patronale' => $ded['patronale'],
                    'base' => $ded['base'],
                    'taux' => $ded['taux'],
                    'amount' => $ded['amount'],
                    'jours_work' => $nombre,
                    'date_application' => now(),
                    'is_active' => 1,
                    'type' => 'default',
                    'month_paie' => \Carbon\Carbon::parse($periode->date_debut)->format('Y-m'),
                    'company_id' => $employee->company_id,
                ]
            );
        }
    }

    /**
     * Applique les retenues légales à tous les salariés actifs de la période.
     * Sans effet une fois les bulletins générés ou la période payée : l'historique ne bouge plus.
     *
     * @return int Nombre de salariés traités
     */
    public function appliquerRetenuesLegalesPeriode(PaiePeriode $periode): int
    {
        if (in_array($periode->statut, ['payee', 'cloture', 'annulee'], true) || $periode->bulletins()->exists()) {
            return 0;
        }

        $employees = Employee::active()
            ->where('company_id', $periode->company_id)
            ->where('start_date', '<=', $periode->date_fin)
            ->where(function ($query) use ($periode) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $periode->date_debut);
            })
            ->get();

        $traites = 0;
        foreach ($employees as $employee) {
            try {
                $this->appliquerRetenuesLegales($employee, $periode);
                $traites++;
            } catch (\Throwable $e) {
                \Log::warning("Retenues légales non appliquées pour l'employé #{$employee->id}, période #{$periode->id} : " . $e->getMessage());
            }
        }

        return $traites;
    }

    // -------------------------------------------------------
    // HEURES SUPPLÉMENTAIRES — Taux légaux (Décret n°96-203)
    // -------------------------------------------------------
    /**
     * Calcule la rémunération des heures supplémentaires selon les taux légaux ivoiriens.
     *
     * @param  float $taux_horaire      Salaire horaire réel (FCFA) = salaire_réel / 173.33
     * @param  float $h_15             Heures à +15%  (41e–46e heure/semaine)
     * @param  float $h_50             Heures à +50%  (47e–55e heure/semaine)
     * @param  float $h_75             Heures nuit JO (21h–5h) ou dimanche/jour
     * @param  float $h_100            Heures nuit dimanche/jours fériés
     * @return float                   Montant total des HS
     */
    public function calculHeuresSupplementaires(
        float $taux_horaire,
        float $h_15 = 0,
        float $h_50 = 0,
        float $h_75 = 0,
        float $h_100 = 0
    ): float {
        return (float) round(
            ($h_15  * $taux_horaire * 1.15) +
            ($h_50  * $taux_horaire * 1.50) +
            ($h_75  * $taux_horaire * 1.75) +
            ($h_100 * $taux_horaire * 2.00)
        );
    }

    // -------------------------------------------------------
    // INDEMNITÉS JOURNALIERS — CCI Art. 25.9, 53 / Art. 15.8 CT
    // -------------------------------------------------------
    /**
     * Calcule les 3 indemnités légales d'un travailleur journalier.
     *
     * @param  Employee $employee
     * @param  int      $jours_travailles   Nombre de jours travaillés sur la période
     * @param  int      $mois_anciennete    Ancienneté totale en mois chez l'employeur
     * @return array    [conge, fin_annee, precarite, eligible_precarite]
     */
    public function calculIndemniteJournalier(
        \Modules\Employees\Models\Employee $employee,
        int $jours_travailles,
        int $mois_anciennete = 0
    ): array {
        $salaire_base = (float) $employee->salary; // salaire catégoriel mensuel complet

        // ---- 1. Indemnité compensatrice de congé (Art. 25.9 CT) ----
        // = 1/12 du salaire brut de la quinzaine de travail
        // Base = salaire catégoriel × (jours/30)
        $salaire_quinzaine = ($salaire_base / 30) * $jours_travailles;
        $conge = round($salaire_quinzaine / 12);

        // ---- 2. Prime de fin d'année prorata (Art. 53 CCI) ----
        // = 75% du salaire catégoriel mensuel × (jours travaillés / 30)
        $fin_annee = round(($salaire_base * 0.75 / 30) * $jours_travailles);

        // ---- 3. Indemnité de précarité (Art. 15.8 CT) ----
        // = 3% de toutes les rémunérations brutes (salaire + primes inclus)
        // Condition : contrat journalier depuis au moins 3 mois (90 jours)
        $eligible_precarite = ($mois_anciennete >= 3);
        $base_precarite = $salaire_quinzaine; // base = brut de la période
        $precarite = $eligible_precarite ? round($base_precarite * 0.03) : 0;

        return [
            'conge'              => $conge,
            'fin_annee'          => $fin_annee,
            'precarite'          => $precarite,
            'eligible_precarite' => $eligible_precarite,
            'mois_anciennete'    => $mois_anciennete,
            'base_quinzaine'     => round($salaire_quinzaine),
        ];
    }

    // -------------------------------------------------------
    // ALLOCATION DE CONGÉ PAYÉ — Méthode 12 derniers mois
    // Art. 25.1-25.12 CT / CCI Art. 71
    // -------------------------------------------------------
    /**
     * Calcule l'allocation financière de congé payé.
     * Utilise la méthode des 12 derniers mois (salaire moyen mensuel).
     *
     * @param  Employee $employee
     * @param  int      $mois_presence       Mois de présence effective (max 12)
     * @param  int      $annees_anciennete   Années d'ancienneté (pour majoration)
     * @return array    [jours_ouvrables, jours_calendaires, smm, allocation]
     */
    public function calculAllocationConge(
        \Modules\Employees\Models\Employee $employee,
        int $mois_presence,
        int $annees_anciennete = 0
    ): array {
        // ---- 1. Durée du congé de base ----
        // 2,2 jours ouvrables par mois de travail effectif
        $jours_base = 2.2 * min($mois_presence, 12);

        // ---- 2. Majoration pour ancienneté (Art. 25.4 CT) ----
        // +1 j/an après 5 ans, +2 j/an après 10 ans, +3 j/an après 15 ans, +5 j/an après 20 ans
        $majoration_anciennete = 0;
        if ($annees_anciennete >= 20) {
            $majoration_anciennete = 5;
        } elseif ($annees_anciennete >= 15) {
            $majoration_anciennete = 3;
        } elseif ($annees_anciennete >= 10) {
            $majoration_anciennete = 2;
        } elseif ($annees_anciennete >= 5) {
            $majoration_anciennete = 1;
        }
        $jours_ouvrables = round($jours_base + $majoration_anciennete, 1);

        // ---- 3. Conversion en jours calendaires ----
        // Ratio légal = jours ouvrables × 1.25 (6 jours ouvrables = 7 jours calendaires)
        $jours_calendaires = round($jours_ouvrables * 1.25);

        // ---- 4. Salaire Moyen Mensuel (SMM) — 12 derniers mois PaySlip ----
        $payslips = \Modules\PaieSalaries\Models\PaySlip
            ::where('employee_id', $employee->id)
            ->orderBy('salary_month', 'desc')
            ->limit(12)
            ->get();

        if ($payslips->count() > 0) {
            $smm = round($payslips->avg('salary_brut'));
        } else {
            // Fallback sur le salaire actuel si pas d'historique
            $smm = (float) $employee->salary;
        }

        // ---- 5. Allocation = SMM / 30 × jours calendaires ----
        $allocation = round(($smm / 30) * $jours_calendaires);

        return [
            'mois_presence'         => $mois_presence,
            'jours_base'            => $jours_base,
            'majoration_anciennete' => $majoration_anciennete,
            'jours_ouvrables'       => $jours_ouvrables,
            'jours_calendaires'     => $jours_calendaires,
            'smm'                   => $smm,
            'allocation'            => $allocation,
            'annees_anciennete'     => $annees_anciennete,
        ];
    }
}

