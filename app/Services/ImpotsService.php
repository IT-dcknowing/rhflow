<?php

namespace App\Services;

class ImpotsService
{
    /**
     * Calcul des impôts : IPR/IGR + CNPS + CMU + RICF
     */
    public function calcul($brut, $jours, $parts, $cnps, $cmu)
    {
        if ($brut <= 0) {
            return 0;
        }

        // Déterminer mode de calcul (mois complet ou journaliers)
        $modeJournalier = ($jours != 30);

        // Tranches mensuelles (mode normal)
        $tranchesMensuelles = [
            [0,       75000,    0],
            [75000,   240000,   16],
            [240000,  800000,   21],
            [800000,  2400000,  24],
            [2400000, 8000000,  28],
            [8000000, INF,      32],
        ];

        // Tranches journalières (calcule du barème journalier)
        // Equivalent des tranches mensuelles divisé par 30
        $tranchesJournalieres = [
            [0,       2500,     0],
            [2500,    8000,     16],
            [8000,    26667,    21],
            [26667,   80000,    24],
            [80000,   266667,   28],
            [266667,  INF,      32],
        ];

        $base = $modeJournalier ? ($brut / 30) : $brut;
        $tranches = $modeJournalier ? $tranchesJournalieres : $tranchesMensuelles;

        $impotBrut = $this->calculTranches($base, $tranches);

        // Si mode journalier, multiplier par le nombre de jours travaillés
        if ($modeJournalier) {
            $impotBrut = round($impotBrut * $jours);
        }

        // Réduction selon nombre de parts
        $reduction = $this->reductionParts($parts, $modeJournalier, $jours);

        // Impôt net IGR/IPR (jamais négatif)
        $impotNet = max(0, $impotBrut - $reduction);

        // Ajouter CNPS + CMU obligatoires
        return $impotNet + $cnps + $cmu;
    }


    /**
     * Calcul tranche par tranche (générique)
     */
    private function calculTranches($base, $tranches)
    {
        $impot = 0;

        foreach ($tranches as $t) {
            list($min, $max, $taux) = $t;

            if ($base > $min) {
                $portion = min($max, $base) - $min;
                if ($portion > 0) {
                    $impot += ($portion * $taux) / 100;
                }
            }
        }

        return round($impot);
    }


    /**
     * Réduction en fonction des parts (RCF/RICF)
     */
    private function reductionParts($parts, $journalier, $jours)
    {
        $table = [
            1     => 0,
            1.5   => 5500,
            2     => 11000,
            2.5   => 16500,
            3     => 22000,
            3.5   => 27500,
            4     => 33000,
            4.5   => 38500,
            5     => 44000,
        ];

        $valeur = $table[$parts] ?? 0;

        // En mode journalier : conversion journalière
        if ($journalier) {
            return round(($valeur / 30) * $jours);
        }

        return $valeur;
    }
}
