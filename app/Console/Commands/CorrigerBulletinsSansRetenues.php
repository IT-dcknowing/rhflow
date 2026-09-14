<?php

namespace App\Console\Commands;

use App\Models\PaiePeriode;
use App\Services\SalaryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Employees\Models\Employee;
use Modules\PaieSalaries\Models\PaySlip;

/**
 * Corrige les bulletins générés alors que les retenues légales (ITS, CNPS, CMU)
 * n'avaient pas été appliquées au salarié.
 *
 * Le bulletin est corrigé sur place : on applique les retenues légales puis on
 * recalcule ses montants. Pas de régénération : ni traitement d'abonnement
 * décompté, ni effet rejoué (ruptures, congés), ni perte du statut de paiement.
 */
class CorrigerBulletinsSansRetenues extends Command
{
    protected $signature = 'paie:corriger-bulletins-sans-retenues
        {--periode=* : Limiter la correction à ces identifiants de période}
        {--appliquer : Enregistrer les corrections (sans cette option : simulation, rien n\'est modifié)}';

    protected $description = 'Applique les retenues légales aux bulletins générés sans ITS/CNPS/CMU et recalcule leurs montants';

    public function handle(SalaryService $salaryService): int
    {
        $appliquer = (bool) $this->option('appliquer');
        $periodes = array_filter((array) $this->option('periode'));

        $bulletins = PaySlip::query()
            ->whereNull('pay_slips.deleted_at')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('retenues')
                    ->whereColumn('retenues.employee_id', 'pay_slips.employee_id')
                    ->whereColumn('retenues.periode_id', 'pay_slips.periode_id')
                    ->where('retenues.type', 'default')
                    ->where('retenues.code', '301');
            })
            ->when($periodes, fn ($q) => $q->whereIn('periode_id', $periodes))
            ->orderBy('periode_id')
            ->orderBy('id')
            ->get();

        $this->info(($appliquer ? 'CORRECTION' : 'SIMULATION (rien n\'est modifié)') . ' — ' . $bulletins->count() . ' bulletin(s) sans retenues légales');
        if ($bulletins->isEmpty()) {
            return self::SUCCESS;
        }

        if ($appliquer) {
            $fichier = 'corrections/bulletins-sans-retenues-' . now()->format('Ymd-His') . '.json';
            \Storage::disk('local')->put($fichier, $bulletins->map->getAttributes()->values()->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->line('Sauvegarde des bulletins avant correction : storage/app/' . $fichier);
        }

        $lignes = [];
        $totaux = ['corriges' => 0, 'ignores' => 0, 'net_avant' => 0, 'net_apres' => 0];

        foreach ($bulletins as $bulletin) {
            $periode = PaiePeriode::withTrashed()->find($bulletin->periode_id);
            $employee = Employee::find($bulletin->employee_id);
            $etat = null;
            $apres = null;

            DB::beginTransaction();
            try {
                if (!$periode || !$employee) {
                    $etat = 'ignoré : ' . (!$periode ? 'période' : 'salarié') . ' introuvable';
                } elseif ((int) $bulletin->salary_brut <= 0) {
                    // Sans salaire, rien à retenir : la CMU donnerait un net négatif
                    $etat = 'ignoré : brut nul';
                } elseif (!$employee->company) {
                    $etat = 'ignoré : entreprise du salarié introuvable';
                } else {
                    $salaryService->appliquerRetenuesLegales($employee, $periode);
                    $employee->refresh();

                    $brut = (int) round($employee->get_brut_salary($periode->id));
                    if (abs($brut - (int) $bulletin->salary_brut) > 1) {
                        // Le salarié a changé depuis (salaire, primes) : correction à revoir à la main
                        $etat = "ignoré : brut recalculé {$brut} ≠ brut du bulletin {$bulletin->salary_brut}";
                    } else {
                        $apres = [
                            'net_payble' => (int) round($employee->get_net_salary($periode->id)),
                            'net_imposable' => (int) round($employee->get_salary_imposable($periode->id)),
                            'net_sociale' => (int) round($employee->get_salary_social($periode->id)),
                            'total_retenue' => $employee->get_retenue($periode->id),
                            'total_patronale' => $employee->get_patronale($periode->id),
                            'retenues' => Employee::retenue($employee->id, $periode->id),
                        ];
                        $etat = $appliquer ? 'corrigé' : 'à corriger';
                    }
                }

                if ($apres && $appliquer) {
                    $bulletin->update($apres);
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            } catch (\Throwable $e) {
                DB::rollBack();
                $etat = 'ignoré : erreur — ' . $e->getMessage();
                $apres = null;
            }

            if ($apres) {
                $totaux['corriges']++;
                $totaux['net_avant'] += $bulletin->getOriginal('net_payble');
                $totaux['net_apres'] += $apres['net_payble'];
            } else {
                $totaux['ignores']++;
            }

            $lignes[] = [
                $bulletin->id,
                $periode ? "{$periode->id} {$periode->nom}" : $bulletin->periode_id,
                $periode->company_id ?? $bulletin->company_id,
                $employee->name ?? ('#' . $bulletin->employee_id),
                number_format($bulletin->salary_brut, 0, ',', ' '),
                number_format($bulletin->getOriginal('net_payble'), 0, ',', ' ') . ($apres ? ' → ' . number_format($apres['net_payble'], 0, ',', ' ') : ''),
                number_format($bulletin->getOriginal('total_retenue'), 0, ',', ' ') . ($apres ? ' → ' . number_format($apres['total_retenue'], 0, ',', ' ') : ''),
                $bulletin->status == 1 ? 'payé' : 'non payé',
                $etat,
            ];
        }

        $this->table(['Bulletin', 'Période', 'Société', 'Salarié', 'Brut', 'Net', 'Retenues salariales', 'Paiement', 'Résultat'], $lignes);

        $this->info(sprintf(
            '%s : %d bulletin(s) · ignorés : %d · net total %s → %s FCFA',
            $appliquer ? 'Corrigés' : 'À corriger',
            $totaux['corriges'],
            $totaux['ignores'],
            number_format($totaux['net_avant'], 0, ',', ' '),
            number_format($totaux['net_apres'], 0, ',', ' ')
        ));

        if (!$appliquer) {
            $this->comment('Pour enregistrer : php artisan paie:corriger-bulletins-sans-retenues --appliquer' . ($periodes ? ' --periode=' . implode(' --periode=', $periodes) : ''));
        }

        return self::SUCCESS;
    }
}
