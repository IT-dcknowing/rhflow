<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Evenements\Models\Announcement;
use Modules\NatureAvantage\Models\Avantage;
use Modules\Evenements\Models\Event;
use Modules\Evenements\Models\EventParticipant;
use Modules\Evenements\Models\EventType;
use Modules\Evenements\Models\Meeting;
use Modules\Employees\Models\Employee;
use Modules\PaieSalaries\Models\PaySlip;
use Modules\Contracts\Models\Contract;
use Modules\Ruptures\Models\Rupture;
use Modules\Time\Models\TimeSheet;
use Modules\Time\Models\Overtime;
use Modules\Leaves\Models\Leave;
use Modules\Loans\Models\Loan;
use App\Models\Allowance;
use App\Models\Company;
use App\Models\Coupon;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Notification;
use App\Models\JobCategorie;
use App\Models\Plan;
use App\Models\Order;
use App\Models\PaiePeriode;
use App\Models\Sector;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**   
     * Dashboard de l'entreprise
     */
    public function dashboard()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        // 📊 DONNÉES FICTIVES POUR LE DASHBOARD

        if(Auth::user()->config_company == 0){

            return redirect()->route('company.settings.config');

        }else{

            $companyId = Auth::user()->company_id;
            $company = Company::find($companyId);
            if($company->industry == null){
                return redirect()->route('company.settings.config');
            }
            $periode = PaiePeriode::where('company_id', '=', $companyId)
                ->where('statut', 'en_cours')
                ->orderBy('date_fin', 'desc')
                ->first() 
                ?? PaiePeriode::where('company_id', '=', $companyId)
                ->orderBy('date_fin', 'desc')
                ->first();
            $events    = Event::where('company_id', '=', $companyId)->get();
                $arrEvents = [];

                foreach ($events as $event) {
                    $arr['id']    = $event['id'];
                    $arr['title'] = $event['title'];
                    $arr['start'] = $event['start_date'];
                    $arr['end']   = $event['end_date'];
                    $arr['className'] = $event['color'];
                    $arr['url'] = route('company.evenements.events.edit', $event['id']);

                    $arrEvents[] = $arr;
                }

                $announcements = Announcement::orderBy('announcements.id', 'desc')->take(5)->where('company_id', '=', $companyId)->get();

                $paySlip = PaySlip::where('company_id', Auth::user()->company_id)->get();
                $derniersMois2 = PaySlip::where('company_id', '=', $companyId)->orderByDesc('salary_month')->pluck('salary_month')->first();
                $derniersMois = PaySlip::where('company_id', '=', $companyId)
                    ->orderByDesc('salary_month')
                    ->pluck('salary_month')
                    ->unique()
                    ->values()
                    ->get(1);

                $emp           = User::where('type', '=', 'employee')->where('company_id', '=', $companyId)->get();
                $countEmployee = count($emp);

                $user      = User::where('type', '!=', 'employee')->where('company_id', '=', $companyId)->get();
                $countUser = count($user);

                $countPaylist = PaySlip::where('company_id', '=', $companyId)->count();
                $countContract      = Contract::where('company_id', '=', $companyId)->count();

                $currentDate = date('Y-m-d');

                $contractsCDD = Contract::where('company_id', '=', $companyId)->where('status', '=', 'accept')->where('type_id', '=', '4')->count();
                $contractsCDI = Contract::where('company_id', '=', $companyId)->where('status', '=', 'accept')->where('type_id', '=', '3')->count();
                $contractsStages = Contract::where('company_id', '=', $companyId)->where('status', '=', 'accept')->where('type_id', '=', '2')->count();
                $contractsAutres = Contract::where('company_id', '=', $companyId)->where('status', '=', 'accept')->where('type_id', '>', '4')->count();

                $secteurs = $company->sector ? $company->sector->name : null;
                $categories = $secteurs ? JobCategorie::where('id_secteur', $secteurs)->orderby('title')->get() : collect();

                $categorieHomme = Employee::where('company_id', '=', $companyId)
                                            ->where('gender', 'Male')
                                            ->select('categorie', \DB::raw('count(*) as total'))
                                            ->groupBy('categorie')
                                            ->get();

                $categorieFemme = Employee::where('company_id', '=', $companyId)
                                            ->where('gender', 'Female')
                                            ->select('categorie', \DB::raw('count(*) as total'))
                                            ->groupBy('categorie')
                                            ->get();

                // Contrats acceptés pour les hommes
                $contractsHomme = Contract::where('company_id', '=', $companyId)
                    ->where('status', '=', 'accept')
                    ->whereHas('employee', function($q) {
                        $q->where('gender', '=', 'Male');
                    })
                    ->count();

                // Contrats acceptés pour les femmes
                $contractsFemme = Contract::where('company_id', '=', $companyId)
                    ->where('status', '=', 'accept')
                    ->whereHas('employee', function($q) {
                        $q->where('gender', '=', 'Female');
                    })
                    ->count();

                // Contrats acceptés CDD pour les hommes
                $contractsCDDHomme = Contract::where('company_id', '=', $companyId)
                    ->where('status', '=', 'accept')
                    ->where('type_id', '=', '4')
                    ->whereHas('employee', function($q) {
                        $q->where('gender', '=', 'Male');
                    })
                    ->count();

                // Contrats acceptés CDD pour les femmes
                $contractsCDDFemme = Contract::where('company_id', '=', $companyId)
                    ->where('status', '=', 'accept')
                    ->where('type_id', '=', '4')
                    ->whereHas('employee', function($q) {
                        $q->where('gender', '=', 'Female');
                    })
                    ->count();

                // Contrats acceptés CDI pour les hommes
                $contractsCDIHomme = Contract::where('company_id', '=', $companyId)
                    ->where('status', '=', 'accept')
                    ->where('type_id', '=', '3')
                    ->whereHas('employee', function($q) {
                        $q->where('gender', '=', 'Male');
                    })
                    ->count();

                // Contrats acceptés CDI pour les femmes
                $contractsCDIFemme = Contract::where('company_id', '=', $companyId)
                    ->where('status', '=', 'accept')
                    ->where('type_id', '=', '3')
                    ->whereHas('employee', function($q) {  
                        $q->where('gender', '=', 'Female');
                    })
                    ->count();

                // Contrats acceptés STAGE pour les hommes
                $contractsSTAGEHomme = Contract::where('company_id', '=', $companyId)
                    ->where('status', '=', 'accept')
                    ->where('type_id', '=', '2')
                    ->whereHas('employee', function($q) {
                        $q->where('gender', '=', 'Male');   
                    })
                    ->count();

                // Contrats acceptés STAGE pour les femmes
                $contractsSTAGEFemme = Contract::where('company_id', '=', $companyId)
                    ->where('status', '=', 'accept')
                    ->where('type_id', '=', '2')
                    ->whereHas('employee', function($q) {
                        $q->where('gender', '=', 'Female');
                    })
                    ->count();

                // Contrats acceptés AUTRE pour les hommes
                $contractsAUTREHomme = Contract::where('company_id', '=', $companyId)
                    ->where('status', '=', 'accept')
                    ->where('type_id', '=', '5')
                    ->whereHas('employee', function($q) {
                        $q->where('gender', '=', 'Male');
                    })
                    ->count();

                // Contrats acceptés AUTRE pour les femmes
                $contractsAUTREFemme = Contract::where('company_id', '=', $companyId)
                    ->where('status', '=', 'accept')
                    ->where('type_id', '=', '5')
                    ->whereHas('employee', function($q) {
                        $q->where('gender', '=', 'Female');
                    })
                    ->count();

                $color = Event::where('company_id', '=', $companyId)->get();

                $employees     = User::where('type', '=', 'employee')->where('company_id', '=', $companyId)->get();
                $countEmployee = count($employees);
                $getEmployee = Employee::where('company_id', '=', $companyId)->where('is_active', '1')->get();
                // Employés actifs par type de salaire. Les fiches antérieures au choix Mensuel/Journalier
                // ont salary_type à NULL : elles sont mensuelles (même règle que la liste des employés).
                $getEmployeeDay = Employee::where('company_id', '=', $companyId)->where('is_active', '1')->where('salary_type', 2)->count();
                $empMensuel = Employee::where('company_id', '=', $companyId)->where('is_active', '1')
                    ->where(fn ($q) => $q->where('salary_type', 1)->orWhereNull('salary_type'))->count();
  
                // Effectif = employes (table employees) + comptes internes non-employes
                // (compte entreprise, RH, paie) qui font aussi partie du personnel en poste.
                $totalEmployes = Employee::where('company_id', '=', $companyId)->count() + $countUser;
                $inActiveJOb = Employee::where('is_active', '0')->where('company_id', '=', $companyId)->count();
                // Employes dont un conge valide couvre la date du jour
                $employesEnConge = Leave::where('company_id', '=', $companyId)
                    ->whereIn('status', ['Approuvé', 'Démarré'])
                    ->whereDate('start_date', '<=', $currentDate)
                    ->whereDate('end_date', '>=', $currentDate)
                    ->distinct('employee_id')
                    ->count('employee_id');
                $activeJob   = max(0, $totalEmployes - $inActiveJOb - $employesEnConge);
                
                $countLeaves = Leave::where('company_id', '=', $companyId)->count();
                $countTimeSheet = TimeSheet::where('motif_justify', 'Oui')->where('statut', '0')->where('company_id', '=', $companyId)->count();
                $countTimeSheet2 = TimeSheet::where('motif_justify', 'Non')->where('statut', '0')->where('company_id', '=', $companyId)->count();

                $totalOvertime = Overtime::where('company_id', '=', $companyId)->where('paid','=','0')->count();
                $totalLoan = Loan::where('company_id', '=', $companyId)->where('statut','!=','2')->count();
                $totalAvantage = Avantage::where('company_id', '=', $companyId)->where('status','=','approved')->where('is_active','=','1')->count();
                $meetings = Meeting::where('company_id', '=', $companyId)->limit(8)->get();

                $plan = Plan::find($company->plan_id);
                if ($plan && $plan->storage_limit > 0) {
                    $storage_limit = ($company->storage_limit / $plan->storage_limit) * 100;
                } else {
                    $storage_limit = 0;
                }

                // Prochaines échéances (Paie)
                $targetDate = $periode ? ($periode->date_paiement ?? $periode->date_fin) : now()->addMonth()->endOfMonth();
                $nextPayrollDays = (int)now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($targetDate)->startOfDay(), false);
                $isPayrollLate = $nextPayrollDays < 0;

                $stats = [
                    // Effectifs
                    // Employés actifs uniquement (les comptes entreprise, RH et paie ne sont pas des journaliers)
                    'total_employees' => $empMensuel + $getEmployeeDay,
                    'monthly_employees' => $empMensuel,
                    'monthly_growth' => 5,
                    'daily_employees' => $getEmployeeDay,
                    'daily_growth' => 0,
                    'new_employees_this_month' => Employee::where('company_id', $companyId)
                        ->whereMonth('created_at', now()->month)
                        ->count(),

                    // Finances
                    'total_salary' => PaySlip::where('company_id', $companyId)
                        ->where('salary_month', 'like', now()->format('Y-m') . '%')
                        ->sum('net_payble'),
                    'salary_trend' => 5.2,
                    
                    // Activité
                    'current_leaves' => $employesEnConge,
                    'recent_departures' => Rupture::where('company_id', $companyId)
                        ->whereMonth('notice_date', now()->month)
                        ->whereYear('notice_date', now()->year)
                        ->count(),
                    'payroll_processed' => $countEmployee > 0 ? (int)(($countPaylist / $countEmployee) * 100) : 0,
                    'pending_payroll' => $countEmployee > 0 ? max(0, 100 - (int)(($countPaylist / $countEmployee) * 100)) : 0,
                    'generated_payrolls' => $countPaylist,
                    'payroll_anomalies' => $countTimeSheet2,

                    // Échéance
                    'next_payroll_date' => $targetDate,
                    'next_payroll_days' => abs($nextPayrollDays),
                    'is_payroll_late' => $isPayrollLate,
                    'periode_name' => $periode ? $periode->nom : 'N/A',
                ];

            // Graphiques - Évolution des effectifs (12 mois)
            $revenueData = [
                'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                'values' => [
                    Employee::where('company_id', '=', $companyId)->whereMonth('created_at', 1)->count(),
                    Employee::where('company_id', '=', $companyId)->whereMonth('created_at', 2)->count(),
                    Employee::where('company_id', '=', $companyId)->whereMonth('created_at', 3)->count(),
                    Employee::where('company_id', '=', $companyId)->whereMonth('created_at', 4)->count(),
                    Employee::where('company_id', '=', $companyId)->whereMonth('created_at', 5)->count(),
                    Employee::where('company_id', '=', $companyId)->whereMonth('created_at', 6)->count(),
                    Employee::where('company_id', '=', $companyId)->whereMonth('created_at', 7)->count(),
                    Employee::where('company_id', '=', $companyId)->whereMonth('created_at', 8)->count(),
                    Employee::where('company_id', '=', $companyId)->whereMonth('created_at', 9)->count(),
                    Employee::where('company_id', '=', $companyId)->whereMonth('created_at', 10)->count(),
                    Employee::where('company_id', '=', $companyId)->whereMonth('created_at', 11)->count(),
                    Employee::where('company_id', '=', $companyId)->whereMonth('created_at', 12)->count(),
                ],
                'targets' => [5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5]
            ];

            // Répartition des contrats
            $expensesData = [
                'labels' => ['CDI', 'CDD', 'Stage', 'Autres'],
                'values' => [$contractsCDI, $contractsCDD, $contractsStages, $contractsAutres],
                'percentages' => [
                    $countContract > 0 ? (int)(($contractsCDI / $countContract) * 100) : 0,
                    $countContract > 0 ? (int)(($contractsCDD / $countContract) * 100) : 0,
                    $countContract > 0 ? (int)(($contractsStages / $countContract) * 100) : 0,
                    $countContract > 0 ? (int)(($contractsAutres / $countContract) * 100) : 0
                ]
            ];

            // Activité récente basée sur les données réelles
            $recentActivities = collect();
            
            // Nouveaux employés récents
            $newEmployees = Employee::where('company_id', '=', $companyId)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
                
            foreach ($newEmployees as $employee) {
                $recentActivities->push([
                    'type' => 'employee_added',
                    'icon' => 'user-plus',
                    'color' => 'success',
                    'title' => 'Nouveau employé ajouté',
                    'description' => $employee->first_name . ' ' . $employee->last_name . ' a été ajouté en tant que ' . ($employee->job_title ?? 'Employé'),
                    'time' => $employee->created_at->diffForHumans()
                ]);
            }
            
            // Dernières fiches de paie
            $recentPayrolls = PaySlip::where('company_id', '=', $companyId)
                ->orderBy('created_at', 'desc')
                ->take(2)
                ->get();
                
            foreach ($recentPayrolls as $payroll) {
                $recentActivities->push([
                    'type' => 'payroll_processed',
                    'icon' => 'dollar',
                    'color' => 'warning',
                    'title' => 'Paie traitée',
                    'description' => 'Paie de ' . $payroll->salary_month . ' - ' . $payroll->employee->first_name . ' ' . $payroll->employee->last_name,
                    'time' => $payroll->created_at->diffForHumans()
                ]);
            }
            
            $recentActivity = $recentActivities->take(5)->toArray();

            // Alertes basées sur les données réelles
            $alerts = collect();
            
            // Alertes sur les pointages non justifiés
            if ($countTimeSheet2 > 0) {
                $alerts->push([
                    'type' => 'danger',
                    'icon' => 'exclamation-triangle',
                    'title' => 'Pointages à justifier',
                    'message' => $countTimeSheet2 . ' pointages ne sont pas justifiés',
                    'deadline' => 'Immédiat'
                ]);
            }
            
            // Alertes sur les prêts en attente
            if ($totalLoan > 0) {
                $alerts->push([
                    'type' => 'warning',
                    'icon' => 'dollar',
                    'title' => 'Prêts en attente',
                    'message' => $totalLoan . ' prêts sont en attente de validation',
                    'deadline' => 'Cette semaine'
                ]);
            }
            
            // Alertes sur les heures supplémentaires
            if ($totalOvertime > 0) {
                $alerts->push([
                    'type' => 'info',
                    'icon' => 'clock',
                    'title' => 'Heures supplémentaires',
                    'message' => $totalOvertime . ' heures supplémentaires non payées',
                    'deadline' => 'À traiter ce mois'
                ]);
            }
            
            // Alertes sur les avantages en attente
            if ($totalAvantage > 0) {
                $alerts->push([
                    'type' => 'success',
                    'icon' => 'gift',
                    'title' => 'Avantages approuvés',
                    'message' => $totalAvantage . ' avantages ont été approuvés ce mois',
                    'deadline' => 'Félicitations !'
                ]);
            }
            
            $alerts = $alerts->toArray();

            // Actions rapides disponibles
            $quickActions = [
                'monthly_employee' => [
                    'title' => 'Nouvel Employé',
                    'subtitle' => 'Mensuel',
                    'route' => isModuleActive('employee') ? route('company.employees.create') : '#',
                    'icon' => 'user-plus',
                    'color' => 'primary'
                ],
                'daily_employee' => [
                    'title' => 'Nouvel Employé',
                    'subtitle' => 'Journalier',
                    'route' => isModuleActive('employee') ? route('company.employees.create') : '#',
                    'icon' => 'user-plus',
                    'color' => 'info'
                ],
                'generate_payroll' => [
                    'title' => 'Générer Paie',
                    'subtitle' => 'Mois en cours',
                    'route' => isModuleActive('salary') ? route('company.paiesalaries.exercices.index') : '#',
                    'icon' => 'dollar',
                    'color' => 'success'
                ],
                'approve_leaves' => [
                    'title' => 'Approuver Congés',
                    'subtitle' => 'En attente',
                    'route' => isModuleActive('leaves') ? route('company.leaves.index') : '#',
                    'icon' => 'calendar',
                    'color' => 'warning'
                ],
                'monthly_report' => [
                    'title' => 'Rapport Mensuel',
                    'subtitle' => 'Livre de paie',
                    'route' => isModuleActive('Declarations') ? route('company.declarations.livrepaie.mensuel') : '#',
                    'icon' => 'file-text',
                    'color' => 'secondary'
                ],
                'company_settings' => [
                    'title' => 'Paramètres',
                    'subtitle' => 'Entreprise',
                    'route' => route('company.settings.settings'),
                    'icon' => 'cog',
                    'color' => 'dark'
                ],
                'support' => [
                    'title' => 'Support',
                    'subtitle' => 'Besoin d\'aide ?',
                    'route' => '#',
                    'icon' => 'headset',
                    'color' => 'danger',
                    'is_active' => false
                ],
                'analytics' => [
                    'title' => 'Analytics',
                    'subtitle' => 'Tableaux de bord',
                    'route' => isModuleActive('PaieSalaries') ? route('company.paiesalaries.dashboard') : '#',
                    'icon' => 'chart-bar',
                    'color' => 'primary',
                    'is_active' => false
                ]
            ];

            return view('company.dashboard', compact(
                'stats',
                'periode',
                'revenueData',
                'expensesData',
                'recentActivity',
                'alerts',
                'quickActions',
                'arrEvents',
                'getEmployeeDay',
                'empMensuel',
                'categorieHomme', 
                'categorieFemme',
                'paySlip', 
                'contractsHomme', 
                'contractsFemme', 
                'announcements', 
                'derniersMois', 
                'derniersMois2', 
                'totalLoan', 
                'totalAvantage', 
                'totalOvertime', 
                'countTimeSheet2', 
                'contractsCDD', 
                'contractsCDI', 
                'contractsStages', 
                'contractsAutres', 
                'getEmployee',
                'countTimeSheet', 
                'color', 
                'employesEnConge', 
                'employees', 
                'activeJob', 
                'inActiveJOb',  
                'meetings', 
                'countEmployee', 
                'countUser', 
                'totalEmployes', 
                'countContract', 
                'countPaylist', 
                'countEmployee',
                'plan', 
                'storage_limit',
                'contractsCDDHomme', 
                'contractsCDDFemme', 
                'contractsCDIHomme', 
                'contractsCDIFemme', 
                'contractsSTAGEHomme', 
                'contractsSTAGEFemme', 
                'contractsAUTREHomme', 
                'contractsAUTREFemme', 
                'secteurs', 
                'categories'
            ));
        }
    }

    public function pricing() {
        return view('company.packs.pack');
    }
}
