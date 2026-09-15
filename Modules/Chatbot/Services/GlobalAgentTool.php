<?php

namespace Modules\Chatbot\Services;

use App\Models\Allowance;
use App\Models\PaieExercice;
use App\Models\PaiePeriode;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use Modules\Employees\Models\Employee;
use Modules\PaieSalaries\Models\PaySlip;
use Modules\PaieSalaries\Models\Retenue;
use Modules\Leaves\Models\Leave;
use Modules\Evenements\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class GlobalAgentTool
{
    /**
     * Audit global de l'entreprise
     */
    public function audit_company(int $companyId)
    {
        $employees = Employee::where('company_id', $companyId)->where('is_active', 1)->get();
        $pendingLeaves = Leave::where('company_id', $companyId)->where('status', 'Pending')->count();

        $payrollIssues = [];
        foreach ($employees as $employee) {
            $hasContract = $employee->contracts()->where('status', 'accept')->exists();
            if (!$hasContract) {
                $payrollIssues[] = "{$employee->name} n'a pas de contrat actif.";
            }
        }

        return [
            'total_employees' => $employees->count(),
            'pending_leaves' => $pendingLeaves,
            'payroll_alerts' => $payrollIssues,
            'message' => "Audit terminé. " . count($payrollIssues) . " alertes de contrat et $pendingLeaves congés en attente."
        ];
    }

    /**
     * Gérer le recrutement / Création d'employé
     */
    public function hire_employee(int $companyId, array $data)
    {
        Log::info("GlobalAgentTool: Hiring employee", $data);

        return DB::transaction(function () use ($companyId, $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                // Même format que le formulaire de création (ex. KOUA15)
                'username' => $data['username'] ?? User::genererUsername($data['name']),
                'password' => Hash::make($data['password'] ?? 'RHFlow2024!'),
                'type' => 'employee',
                'company_id' => $companyId,
            ]);

            $branch = Branch::where('company_id', $companyId)->first();
            $dept = Department::where('company_id', $companyId)->first();
            $desig = Designation::where('company_id', $companyId)->first();

            $employee = Employee::create([
                'user_id' => $user->id,
                'company_id' => $companyId,
                'name' => $data['name'],
                'gender' => $data['gender'] ?? 'Male',
                'employee_id' => $data['employee_id'] ?? 'EMP-' . rand(1000, 9999),
                'branch_id' => $data['branch_id'] ?? ($branch->id ?? null),
                'department_id' => $data['department_id'] ?? ($dept->id ?? null),
                'designation_id' => $data['designation_id'] ?? ($desig->id ?? null),
                'salary' => $data['salary'] ?? 0,
                'is_active' => 1,
            ]);

            return [
                'status' => 'success',
                'employee_id' => $employee->id,
                'message' => "L'employé {$data['name']} a été créé avec succès (Matricule: {$employee->employee_id})."
            ];
        });
    }

    /**
     * Valider ou Rejeter un congé
     */
    public function manage_leave(int $companyId, int $leaveId, string $status, string $reason = null)
    {
        $leave = Leave::where('id', $leaveId)->where('company_id', $companyId)->first();
        if (!$leave)
            return ['error' => "Congé introuvable."];

        $leave->status = $status;
        $leave->leave_reason = $reason;
        $leave->save();

        return [
            'status' => 'success',
            'new_status' => $status,
            'message' => "La demande de congé de {$leave->employee->name} a été passée à : $status."
        ];
    }

    /**
     * Créer un événement d'entreprise
     */
    public function create_event(int $companyId, string $title, string $startDate, string $description = null)
    {
        $event = Event::create([
            'company_id' => $companyId,
            'title' => $title,
            'start_date' => $startDate,
            'end_date' => $startDate,
            'description' => $description,
            'status' => 'published',
            'color' => 'event-primary',
        ]);

        return [
            'status' => 'success',
            'event_id' => $event->id,
            'message' => "L'événement '$title' a été ajouté au calendrier pour le $startDate."
        ];
    }

    /**
     * Préparer une période de paie intelligemment
     */
    public function prepare_period(int $companyId, string $month_name = null, string $start_date = null, string $end_date = null)
    {
        // 1. Déduction des dates si absentes
        if (empty($start_date) || empty($end_date)) {
            $lastPeriode = PaiePeriode::where('company_id', $companyId)
                ->orderBy('date_fin', 'desc')
                ->first();

            if ($lastPeriode) {
                $nextStart = Carbon::parse($lastPeriode->date_fin)->addDay()->startOfMonth();
            } else {
                $nextStart = now()->startOfMonth();
            }

            $start_date = $nextStart->format('Y-m-d');
            $end_date = $nextStart->copy()->endOfMonth()->format('Y-m-d');
            $month_name = $nextStart->locale('fr')->isoFormat('MMMM YYYY');
        }

        $targetYear = Carbon::parse($start_date)->year;

        // 2. Trouver ou Créer l'exercice
        $exercice = PaieExercice::where('company_id', $companyId)
            ->whereYear('date_debut', $targetYear)
            ->first();

        if (!$exercice) {
            $exercice = PaieExercice::create([
                'nom' => "Exercice $targetYear",
                'code' => PaieExercice::genererCode(),
                'date_debut' => "$targetYear-01-01",
                'date_fin' => "$targetYear-12-31",
                'statut' => 'en_cours',
                'company_id' => $companyId,
                'created_by' => auth()->id() ?? 1,
            ]);
        }

        // 3. Créer la période
        $periode = PaiePeriode::create([
            'exercice_id' => $exercice->id,
            'company_id' => $companyId,
            'nom' => $month_name,
            'code' => PaiePeriode::genererCode($exercice, 'mensuelle'),
            'date_debut' => $start_date,
            'date_fin' => $end_date,
            'date_paiement' => $end_date,
            'type_periode' => 'mensuelle',
            'statut' => 'ouvert',
            'created_by' => auth()->id() ?? 1,
        ]);

        // ✅ CORRECTION : retourner l'ID pour que l'IA puisse enchaîner run_payroll_calculation
        return [
            'status' => 'success',
            'periode_id' => $periode->id,
            'nom' => $periode->nom,
            'message' => "La période {$periode->nom} (ID: #{$periode->id}) a été créée dans l'{$exercice->nom}."
        ];
    }

    /**
     * Lancer le calcul réel des bulletins
     */
    public function run_payroll(int $companyId, int $periodeId)
    {
        $periode = PaiePeriode::where('id', $periodeId)->where('company_id', $companyId)->first();
        if (!$periode)
            return ['error' => "Période introuvable."];

        $month = Carbon::parse($periode->date_debut)->format('Y-m');
        $year = Carbon::parse($periode->date_debut)->year;
        $firstDay = Carbon::parse($periode->date_debut)->startOfMonth()->toDateString();
        $lastDay = Carbon::createFromDate($year, Carbon::parse($periode->date_debut)->month)->endOfMonth()->toDateString();

        $alreadyDone = PaySlip::where('salary_month', $month)->where('company_id', $companyId)->pluck('employee_id');

        $eligible = Employee::where('company_id', $companyId)
            ->where('is_active', 1)
            ->whereHas('contracts', function ($q) use ($firstDay, $lastDay) {
                $q->where('start_date', '<=', $lastDay)
                    ->where(function ($q2) use ($firstDay) {
                        $q2->where('end_date', '>=', $firstDay)->orWhereNull('end_date');
                    });
            })
            ->pluck('id');

        $toProcess = $eligible->diff($alreadyDone);

        if ($toProcess->count() == 0) {
            return [
                'status' => 'already_done',
                'periode_id' => $periode->id,
                'message' => "Les bulletins pour {$periode->nom} ont déjà tous été générés."
            ];
        }

        $count = 0;
        foreach ($toProcess as $empId) {
            $employee = Employee::where('id', $empId)->where('is_active', 1)->first();
            if (!$employee)
                continue;

            $payslip = new PaySlip();
            $payslip->employee_id = $employee->id;
            $payslip->periode_id = $periode->id;
            $payslip->net_payble = $employee->get_net_salary($periode->id);
            $payslip->salary_month = $month;
            $payslip->status = 0;
            $payslip->salary_brut = $employee->get_brut_salary($periode->id);
            $payslip->net_imposable = $employee->get_salary_imposable($periode->id);
            $payslip->net_sociale = $employee->get_salary_social($periode->id);
            $payslip->basic_salary = (float) ($employee->get_Salary_base($periode->id) ?: 0);
            $payslip->nbre_jour = $employee->get_jours_work($periode->id);
            $payslip->total_retenue = $employee->get_retenue($periode->id);
            $payslip->total_patronale = $employee->get_patronale($periode->id);
            $payslip->allowances = Employee::allowance($employee->id, $periode->id);
            $payslip->retenues = Employee::retenue($employee->id, $periode->id);
            $payslip->num_cnps_emp = $employee->get_Num_Cnps();
            $payslip->anciennete_emp = $employee->get_Anciennete();
            $payslip->categories_emp = $employee->get_Categorie();
            $payslip->emploi = $employee->get_Emploi();
            $payslip->nom_etp = $employee->get_Nom_Etp($periode->id);
            $payslip->adresse_etp = $employee->get_Adresse_Etp($periode->id);
            $payslip->company_id = $companyId;
            $payslip->save();
            $count++;
        }

        // ✅ CORRECTION : statut 'en_cours' (pas 'validee') — la validation se fait via validate_payslips
        $periode->update(['statut' => 'en_cours']);

        return [
            'status' => 'success',
            'periode_id' => $periode->id,
            'bulletins_generes' => $count,
            'periode' => $periode->nom,
            'message' => "✅ Calcul terminé. **$count bulletins générés** pour la période {$periode->nom}. Les salaires nets, bruts et cotisations ont été calculés selon la législation en vigueur."
        ];
    }

    /**
     * Valider et marquer les bulletins comme payés
     */
    public function validate_payslips(int $companyId, int $periodeId, string $datePaiement = null)
    {
        $periode = PaiePeriode::where('id', $periodeId)->where('company_id', $companyId)->first();
        if (!$periode)
            return ['error' => "Période introuvable."];

        $bulletinsCount = $periode->bulletins()->count();
        if ($bulletinsCount === 0) {
            return ['error' => "Aucun bulletin généré pour cette période. Lancez d'abord le calcul."];
        }

        $periode->update([
            'statut' => 'payee',
            'date_paiement' => $datePaiement ?? now()->toDateString(),
        ]);

        $periode->bulletins()->update(['status' => 1]);

        return [
            'status' => 'success',
            'bulletins_valides' => $bulletinsCount,
            'message' => "✅ **$bulletinsCount bulletins** ont été validés et marqués comme payés. La période {$periode->nom} est maintenant clôturée."
        ];
    }

    /**
     * Rapport de synthèse de la paie (Livre de Paie)
     */
    public function get_payroll_summary(int $companyId, int $periodeId)
    {
        $periode = PaiePeriode::where('id', $periodeId)->where('company_id', $companyId)->first();
        if (!$periode)
            return ['error' => "Période introuvable."];

        $month = Carbon::parse($periode->date_debut)->format('Y-m');

        $bulletins = PaySlip::where('company_id', $companyId)
            ->where('salary_month', $month)
            ->with('employee:id,name,employee_id')
            ->get();

        $totalBrut = $bulletins->sum('salary_brut');
        $totalNet = $bulletins->sum('net_payble');
        $totalRetenues = $bulletins->sum('total_retenue');
        $totalPatronale = $bulletins->sum('total_patronale');
        $count = $bulletins->count();

        return [
            'status' => 'success',
            'periode' => $periode->nom,
            'nombre_bulletins' => $count,
            'total_brut' => number_format($totalBrut, 0, ',', ' ') . ' FCFA',
            'total_net' => number_format($totalNet, 0, ',', ' ') . ' FCFA',
            'total_retenues_salariales' => number_format($totalRetenues, 0, ',', ' ') . ' FCFA',
            'total_charges_patronales' => number_format($totalPatronale, 0, ',', ' ') . ' FCFA',
            'cout_total_employeur' => number_format($totalBrut + $totalPatronale, 0, ',', ' ') . ' FCFA',
            'message' => "📊 **Livre de Paie – {$periode->nom}** :\n- Effectif payé : $count employés\n- Masse salariale brute : " . number_format($totalBrut, 0, ',', ' ') . " FCFA\n- Net à payer : " . number_format($totalNet, 0, ',', ' ') . " FCFA\n- Charges patronales : " . number_format($totalPatronale, 0, ',', ' ') . " FCFA\n- Coût total employeur : " . number_format($totalBrut + $totalPatronale, 0, ',', ' ') . " FCFA"
        ];
    }

    /**
     * Générer un résumé des déclarations sociales (CNPS, ITS, CMU)
     */
    public function generate_declarations(int $companyId, int $periodeId)
    {
        $periode = PaiePeriode::where('id', $periodeId)->where('company_id', $companyId)->first();
        if (!$periode)
            return ['error' => "Période introuvable."];

        $month = Carbon::parse($periode->date_debut)->format('Y-m');

        $bulletins = PaySlip::where('company_id', $companyId)
            ->where('salary_month', $month)
            ->get();

        $totalNetSocial = $bulletins->sum('net_sociale');
        $totalNetImposable = $bulletins->sum('net_imposable');
        $count = $bulletins->count();

        $cnpsSalarial = round($totalNetSocial * 0.063);
        $cnpsPatronal = round($totalNetSocial * 0.077);

        // ✅ CORRECTION : variable $cnpsSalarial correctement définie dans la closure
        $its = $bulletins->sum(function ($b) {
            $cnpsSalarial = round($b->net_sociale * 0.063);
            return max(0, $b->salary_brut - $b->net_payble - $b->total_retenue - $cnpsSalarial);
        });

        return [
            'status' => 'success',
            'periode' => $periode->nom,
            'effectif' => $count,
            'base_cnps' => number_format($totalNetSocial, 0, ',', ' ') . ' FCFA',
            'cnps_salarial' => number_format($cnpsSalarial, 0, ',', ' ') . ' FCFA',
            'cnps_patronal' => number_format($cnpsPatronal, 0, ',', ' ') . ' FCFA',
            'base_its' => number_format($totalNetImposable, 0, ',', ' ') . ' FCFA',
            'its_estime' => number_format($its, 0, ',', ' ') . ' FCFA',
            'message' => "📋 **Déclarations Sociales – {$periode->nom}** :\n- Base CNPS (Net Social) : " . number_format($totalNetSocial, 0, ',', ' ') . " FCFA\n- CNPS Salarié (6.3%) : " . number_format($cnpsSalarial, 0, ',', ' ') . " FCFA\n- CNPS Patronal (7.7%) : " . number_format($cnpsPatronal, 0, ',', ' ') . " FCFA\n- Base ITS (Net Imposable) : " . number_format($totalNetImposable, 0, ',', ' ') . " FCFA\n\n✅ Ces données sont prêtes pour votre télédéclaration CNPS et DGI."
        ];
    }

    /**
     * Récupérer les variables enregistrées pour une période (Primes & Retenues)
     */
    public function get_payroll_variables(int $companyId, int $periodeId)
    {
        $primes = Allowance::where('company_id', $companyId)
            ->where('periode_id', $periodeId)
            ->with('employee:id,name')
            ->get();

        $retenues = Retenue::where('company_id', $companyId)
            ->where('periode_id', $periodeId)
            ->with('employees:id,name')
            ->get();

        return [
            'total_primes' => $primes->count(),
            'total_retenues' => $retenues->count(),
            'somme_primes' => $primes->sum('amount'),
            'somme_retenues' => $retenues->sum('amount'),
            'details_primes' => $primes->map(fn($p) => ['titre' => $p->title, 'employe' => $p->employee->name ?? 'N/A', 'montant' => $p->amount]),
            'details_retenues' => $retenues->map(fn($r) => ['titre' => $r->libelle, 'employe' => $r->employees->name ?? 'N/A', 'montant' => $r->amount]),
            'message' => "J'ai trouvé " . $primes->count() . " primes et " . $retenues->count() . " retenues enregistrées pour cette période."
        ];
    }

    /**
     * Rechercher une ou plusieurs périodes de paie par nom ou année
     */
    public function search_period(int $companyId, string $query)
    {
        $periods = PaiePeriode::where('company_id', $companyId)
            ->where('nom', 'like', "%$query%")
            ->orderBy('date_debut', 'desc')
            ->get(['id', 'nom', 'statut']);

        if ($periods->isEmpty()) {
            return ['error' => "Aucune période trouvée pour la recherche : '$query'."];
        }

        return [
            'status' => 'success',
            'periods' => $periods->toArray(),
            'message' => "J'ai trouvé {$periods->count()} période(s) correspondant à '$query'."
        ];
    }
}