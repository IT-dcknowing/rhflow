<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Company;
use Modules\Employees\Models\Employee;
use Modules\PaieSalaries\Models\PaySlip;
use Modules\Contracts\Models\Contract;
use Modules\Leaves\Models\Leave;

class DashboardApiController extends Controller
{
    public function getCompanies(Request $request)
    {
        $companies = Company::select('id', 'name')->get();
        return response()->json([
            'status' => 'success',
            'data' => $companies
        ]);
    }

    public function getExercices(Request $request)
    {
        $companyId = $request->query('company_id') ?? $request->header('X-Company-Id');
        
        $monthsFromDb = PaySlip::when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->selectRaw('DISTINCT salary_month')
            ->pluck('salary_month')
            ->filter()
            ->toArray();
            
        $yearsWithMonths = [];
        foreach ($monthsFromDb as $sm) {
            $parts = explode('-', $sm);
            if (count($parts) == 2) {
                $year = (int)$parts[0];
                $month = $parts[1];
                if (!isset($yearsWithMonths[$year])) {
                    $yearsWithMonths[$year] = [];
                }
                $yearsWithMonths[$year][] = $month;
            }
        }

        $currentYear = (int) date('Y');
        $defaultYears = [$currentYear, $currentYear - 1, $currentYear - 2];
        $allYears = array_unique(array_merge(array_keys($yearsWithMonths), $defaultYears));
        rsort($allYears);

        $data = array_map(function($y) use ($yearsWithMonths) {
            $months = $yearsWithMonths[$y] ?? [];
            sort($months);
            return [
                'id' => (string)$y,
                'name' => (string)$y,
                'months' => $months
            ];
        }, $allYears);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getKpis(Request $request)
    {
        // Le Hub envoie le company_id via l'en-tête X-Company-Id (pas en query string)
        $companyId = $request->query('company_id') ?? $request->header('X-Company-Id');
        $company = $companyId ? Company::find($companyId) : null;
        $currentMonth = date('Y-m');
        $today = Carbon::today();

        // ---------- EMPLOYÉS ACTIFS ----------
        $employeesQuery = Employee::query()->active();
        if ($companyId) {
            $employeesQuery->where('company_id', $companyId);
        }
        $employees = $employeesQuery->get();
        $effectifTotal = $employees->count();

        $ageMoyen = 0;
        if ($effectifTotal > 0) {
            $ages = $employees->filter(fn($e) => !empty($e->dob))
                ->map(fn($e) => Carbon::parse($e->dob)->age);
            $ageMoyen = $ages->count() > 0 ? round($ages->avg(), 1) : 0;
        }

        // Répartition par type de contrat : on passe par le modèle Contract (avec son type),
        // car Employee.contrat ne contient qu'un identifiant numérique, pas un libellé.
        $employeeIds = $employees->pluck('id');
        $contratsQuery = Contract::whereIn('employee_id', $employeeIds)
            ->where('status', 'accept')
            ->with('type');
        if ($companyId) {
            $contratsQuery->where('company_id', $companyId);
        }
        $contratsGroup = $contratsQuery->get()
            ->groupBy(fn($c) => $c->type->name ?? 'Non renseigné')
            ->map->count();
        $chartContrats = [
            'labels' => $contratsGroup->keys()->values()->toArray(),
            'data' => $contratsGroup->values()->toArray(),
        ];

        $employeesQuery2 = Employee::query()->active()->with('department');
        if ($companyId) {
            $employeesQuery2->where('company_id', $companyId);
        }
        $deptGroup = $employeesQuery2->get()
            ->groupBy(fn($e) => $e->department->name ?? 'Non affecté')
            ->map->count();
        $chartDept = [
            'labels' => $deptGroup->keys()->values()->toArray(),
            'data' => $deptGroup->values()->toArray(),
        ];

        // ---------- TURN-OVER (12 derniers mois) ----------
        // Départs = employés inactifs dont la date de sortie (end_date) tombe dans les 12 derniers mois.
        // ⚠️ Approximation : on utilise l'effectif actif actuel comme effectif moyen de référence,
        // faute d'historique d'effectif figé mois par mois. À affiner si un tel historique existe.
        $oneYearAgo = $today->copy()->subMonths(12);
        $departsQuery = Employee::where('is_active', 0)
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$oneYearAgo, $today]);
        if ($companyId) {
            $departsQuery->where('company_id', $companyId);
        }
        $departs = $departsQuery->count();
        $turnover = $effectifTotal > 0 ? round(($departs / $effectifTotal) * 100, 1) : 0;

        // ---------- FINANCIER ----------
        // Le mois "calendaire" sert uniquement à l'alerte "paie en retard" (toujours le vrai mois en cours).
        // Le mois "affiché" peut être choisi explicitement par l'utilisateur via le sélecteur du Hub
        // (paramètre ?month=YYYY-MM ou en-tête X-Kpi-Month), pour éviter toute confusion sur la période visualisée.
        $exerciceId = $request->query('exercice_id');
        $monthInput = $request->query('month');
        $requestedMonth = null;

        if ($exerciceId && $monthInput) {
            $requestedMonth = sprintf('%s-%02d', $exerciceId, (int)$monthInput);
        } elseif ($monthInput && strpos($monthInput, '-') !== false) {
            $requestedMonth = $monthInput;
        }

        $payslipsCalendarQuery = PaySlip::where('salary_month', $currentMonth);
        if ($companyId) {
            $payslipsCalendarQuery->where('company_id', $companyId);
        }
        $payslipsCalendar = $payslipsCalendarQuery->get();
        $paieRetard = $payslipsCalendar->isEmpty();

        if ($requestedMonth) {
            // Mois choisi explicitement pour un exercice (ex: 2026-03)
            $q = PaySlip::where('salary_month', $requestedMonth);
            if ($companyId) {
                $q->where('company_id', $companyId);
            }
            $payslipsForKpi = $q->get();
            $kpiMonth = $requestedMonth;
            
            try {
                $dt = Carbon::createFromFormat('Y-m', $requestedMonth);
                $periodeLabel = ucfirst($dt->translatedFormat('F Y'));
                $periodeDates = $dt->startOfMonth()->format('d/m/Y') . ' au ' . $dt->endOfMonth()->format('d/m/Y');
            } catch (\Throwable $e) {
                $periodeLabel = "Mois " . $requestedMonth;
                $periodeDates = "Mois " . $requestedMonth;
            }
        } elseif ($exerciceId) {
            // Exercice complet (toute l'année)
            $q = PaySlip::where('salary_month', 'like', $exerciceId . '-%');
            if ($companyId) {
                $q->where('company_id', $companyId);
            }
            $payslipsForKpi = $q->get();
            $kpiMonth = $exerciceId;
            $periodeLabel = "Exercice " . $exerciceId;
            $periodeDates = "01/01/" . $exerciceId . " au 31/12/" . $exerciceId;
        } else {
            // Premier chargement (aucun mois ni exercice choisi) : mois calendaire
            $payslipsForKpi = $payslipsCalendar;
            $kpiMonth = $currentMonth;
            $periodeLabel = "Mois en cours (" . $currentMonth . ")";
            $periodeDates = "Mois " . $currentMonth;
            if ($payslipsForKpi->isEmpty()) {
                for ($i = 1; $i <= 6; $i++) {
                    $month = Carbon::now()->subMonths($i)->format('Y-m');
                    $q = PaySlip::where('salary_month', $month);
                    if ($companyId) {
                        $q->where('company_id', $companyId);
                    }
                    $found = $q->get();
                    if ($found->isNotEmpty()) {
                        $payslipsForKpi = $found;
                        $kpiMonth = $month;
                        $periodeLabel = "Dernier mois clôturé (" . $month . ")";
                        $periodeDates = "Mois " . $month;
                        break;
                    }
                }
            }
        }

        $masseSalariale = (float) $payslipsForKpi->sum('salary_brut');
        $totalNet = (float) $payslipsForKpi->sum('net_payble');
        $cotisations = (float) $payslipsForKpi->sum('total_retenue') + (float) $payslipsForKpi->sum('total_patronale');
        $coutMoyen = $effectifTotal > 0 ? round($masseSalariale / $effectifTotal) : 0;

        $labels = [];
        $data = [];
        if ($exerciceId) {
            // Afficher les 12 mois de l'année sélectionnée
            for ($i = 1; $i <= 12; $i++) {
                $monthStr = sprintf('%s-%02d', $exerciceId, $i);
                $labels[] = Carbon::createFromFormat('Y-m', $monthStr)->translatedFormat('M');
                $q = PaySlip::where('salary_month', $monthStr);
                if ($companyId) {
                    $q->where('company_id', $companyId);
                }
                $data[] = (float) $q->sum('salary_brut');
            }
        } else {
            // Par défaut : les 6 derniers mois
            for ($i = 5; $i >= 0; $i--) {
                $monthStr = Carbon::now()->subMonths($i)->format('Y-m');
                $labels[] = Carbon::now()->subMonths($i)->translatedFormat('M');
                $q = PaySlip::where('salary_month', $monthStr);
                if ($companyId) {
                    $q->where('company_id', $companyId);
                }
                $data[] = (float) $q->sum('salary_brut');
            }
        }
        $chartMasse = ['labels' => $labels, 'data' => $data];

        // ---------- CONTRATS : CDD expirant sous 15 jours ----------
        $cddQuery = Contract::where('status', 'accept')
            ->whereBetween('end_date', [$today, $today->copy()->addDays(15)])
            ->whereHas('type', function ($q) {
                $q->where('name', 'like', '%CDD%');
            });
        if ($companyId) {
            $cddQuery->where('company_id', $companyId);
        }
        $cddExpire = $cddQuery->count();

        // ---------- CONTRATS : Fin de période d'essai (hypothèse 3 mois) ----------
        // ⚠️ Hypothèse à valider : pas de champ dédié "essai" trouvé dans Contract.
        // On suppose ici que la période d'essai dure 3 mois depuis start_date.
        $essaiQuery = Contract::where('status', 'accept');
        if ($companyId) {
            $essaiQuery->where('company_id', $companyId);
        }
        $finPeriodeEssai = $essaiQuery->get()
            ->filter(function ($c) use ($today) {
                if (!$c->start_date)
                    return false;
                $finEssai = Carbon::parse($c->start_date)->addMonths(3);
                return $finEssai->between($today, $today->copy()->addDays(15));
            })->count();

        // ---------- CONGÉS EN COURS (aujourd'hui, statut validé) ----------
        $congesQuery = Leave::whereIn('status', ['Approuvé', 'Démarré'])
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->with('employee');
        if ($companyId) {
            $congesQuery->where('company_id', $companyId);
        }
        $congesEnCours = $congesQuery->get()->map(function ($leave) {
            $emp = $leave->employee;
            $initiales = $emp ? collect(explode(' ', $emp->name))->map(fn($p) => strtoupper(substr($p, 0, 1)))->implode('') : '--';
            return [
                'nom' => $emp->name ?? 'Inconnu',
                'initiales' => $initiales,
                'poste' => $emp->designation->name ?? '-',
                'date_fin' => Carbon::parse($leave->end_date)->format('d/m/Y'),
            ];
        })->values()->toArray();

        // Demandes de congé en attente
        $demandesQuery = Leave::where('status', 'Pending');
        if ($companyId) {
            $demandesQuery->where('company_id', $companyId);
        }
        $demandesEnAttente = $demandesQuery->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'company_name' => $company->name ?? null,
                'demographie' => [
                    'effectif_total' => $effectifTotal,
                    'taux_absenteisme' => 0, // TODO: nécessite le modèle TimeSheet
                    'turnover' => $turnover,
                    'age_moyen' => $ageMoyen,
                    'chart_contrats' => $chartContrats,
                    'chart_dept' => $chartDept,
                ],
                'financier' => [
                    'masse_salariale' => $masseSalariale,
                    'total_net' => $totalNet,
                    'cotisations' => $cotisations,
                    'cout_moyen' => $coutMoyen,
                    'heures_sup_cout' => 0, // TODO: nécessite le modèle Overtime
                    'chart_masse' => $chartMasse,
                    'kpi_month' => $kpiMonth, // mois réellement affiché
                    'periode_label' => $periodeLabel,
                    'periode_dates' => $periodeDates,
                ],
                'climat' => [
                    'conges_en_cours' => $congesEnCours,
                    'demandes_en_attente' => $demandesEnAttente,
                    'taux_satisfaction' => 0, // TODO
                    'provision_conges' => 0, // TODO
                ],
                'alertes' => [
                    'paie_retard' => $paieRetard,
                    'cdd_expire' => $cddExpire,
                    'fin_periode_essai' => $finPeriodeEssai,
                ],
                'conformite' => [
                    'visites_medicales_retard' => 0, // TODO
                    'contrats_expirant' => $cddExpire,
                ],
            ]
        ]);
    }
}
