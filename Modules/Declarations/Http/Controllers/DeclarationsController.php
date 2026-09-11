<?php

namespace Modules\Declarations\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PaiePeriode;
use Carbon\Carbon;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Employees\Models\Employee;
use App\Models\AllowanceOption;
use App\Models\Avantage;
use App\Models\Designation;
use App\Models\JobCategory; 
use App\Models\PaieExercice;
use Modules\Employees\Models\EmployeeCmu;
use Modules\PaieSalaries\Models\PaySlip;
use Modules\PaieSalaries\Models\SetSalarie;
use Modules\Declarations\Jobs\GenerateBulkBulletinsPDF;
use Illuminate\Support\Facades\Storage;

class DeclarationsController extends Controller
{
    /**
     * Dashboard
     */
    public function dashboard()
    {
        $companyId = Auth::user()->company_id ?? 1;
        
        // Statistiques réelles depuis les tables
        $currentYear = now()->year;
        $currentMonth = now()->month;
        
        // Bulletins annuels et mensuels
        $totalBulletinsAnnuel = PaySlip::where('company_id', $companyId)
            ->whereYear('salary_month', $currentYear)
            ->count();
            
        $totalBulletinsMensuel = PaySlip::where('company_id', $companyId)
            ->whereYear('salary_month', $currentYear)
            ->whereMonth('salary_month', $currentMonth)
            ->count();
        
        // Statistiques pour les déclarations
        $stats = [
            'total_bulletins_annuel' => $totalBulletinsAnnuel,
            'total_bulletins_mensuel' => $totalBulletinsMensuel,
            'total_declarations_cnps' => PaySlip::where('company_id', $companyId)
                ->whereYear('salary_month', $currentYear)
                ->where('status', 'validated')
                ->count(),
            'total_declarations_its' => PaySlip::where('company_id', $companyId)
                ->whereYear('salary_month', $currentYear)
                ->where('pay_type', 'mensuel')
                ->count(),
            'total_declarations_cmu' => PaySlip::where('company_id', $companyId)
                ->whereYear('salary_month', $currentYear)
                ->where('pay_type', 'journalier')
                ->count(),
            'derniere_mise_a_jour' => PaySlip::where('company_id', $companyId)
                ->latest('updated_at')
                ->value('updated_at') ?? now()->subDays(3),
            'periode_en_cours' => now()->locale('fr_FR')->isoFormat('MMMM Y'),
            'exercice_encours' => $currentYear,
        ];

        // Données pour les graphiques - évolution mensuelle
        $declarationsEvolution = [
            'labels' => [],
            'cnps' => [],
            'its' => [],
            'cmu' => [],
        ];
        
        // Générer les données des 12 derniers mois
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            
            $declarationsEvolution['labels'][] = $date->locale('fr_FR')->isoFormat('MMM');
            $declarationsEvolution['cnps'][] = PaySlip::where('company_id', $companyId)
                ->whereYear('salary_month', $year)
                ->whereMonth('salary_month', $month)
                ->where('status', 'validated')
                ->count();
            $declarationsEvolution['its'][] = PaySlip::where('company_id', $companyId)
                ->whereYear('salary_month', $year)
                ->whereMonth('salary_month', $month)
                ->where('pay_type', 'mensuel')
                ->count();
            $declarationsEvolution['cmu'][] = PaySlip::where('company_id', $companyId)
                ->whereYear('salary_month', $year)
                ->whereMonth('salary_month', $month)
                ->where('pay_type', 'journalier')
                ->count();
        }

        // Bulletins récents depuis la base de données
        $derniersBulletins = PaySlip::with('employee')
            ->where('company_id', $companyId)
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($bulletin) {
                return (object) [
                    'id' => $bulletin->id,
                    'employee' => (object) ['name' => $bulletin->employee->name ?? 'Employé inconnu'],
                    'periode' => $bulletin->salary_month,
                    'type' => ucfirst($bulletin->pay_type ?? 'mensuel'),
                    'salaire_net' => $bulletin->net_payble ?? 0,
                    'statut' => $bulletin->status === 'validated' ? 'Validé' : ($bulletin->status === 'generated' ? 'Généré' : 'Brouillon'),
                    'date_generation' => $bulletin->created_at,
                ];
            });

        // Déclarations récentes (simulées pour l'instant)
        $dernieresDeclarations = collect([
            (object) [
                'id' => 1,
                'type' => 'CNPS',
                'periode' => now()->locale('fr_FR')->isoFormat('MMMM Y'),
                'statut' => 'Soumis',
                'date_soumission' => now()->subDays(5),
                'employes_concernes' => $totalBulletinsMensuel,
                'montant_total' => PaySlip::where('company_id', $companyId)
                    ->whereYear('salary_month', $currentYear)
                    ->whereMonth('salary_month', $currentMonth)
                    ->sum('net_payble') ?? 8500000,
            ],
            (object) [
                'id' => 2,
                'type' => 'ITS',
                'periode' => now()->locale('fr_FR')->isoFormat('MMMM Y'),
                'statut' => 'En cours',
                'date_soumission' => now()->subDays(3),
                'employes_concernes' => PaySlip::where('company_id', $companyId)
                    ->whereYear('salary_month', $currentYear)
                    ->whereMonth('salary_month', $currentMonth)
                    ->where('pay_type', 'mensuel')
                    ->count(),
                'montant_total' => PaySlip::where('company_id', $companyId)
                    ->whereYear('salary_month', $currentYear)
                    ->whereMonth('salary_month', $currentMonth)
                    ->where('pay_type', 'mensuel')
                    ->sum('net_payble') ?? 3200000,
            ],
            (object) [
                'id' => 3,
                'type' => 'CMU',
                'periode' => now()->locale('fr_FR')->isoFormat('MMMM Y'),
                'statut' => 'Brouillon',
                'date_soumission' => now()->subDays(1),
                'employes_concernes' => PaySlip::where('company_id', $companyId)
                    ->whereYear('salary_month', $currentYear)
                    ->whereMonth('salary_month', $currentMonth)
                    ->where('pay_type', 'journalier')
                    ->count(),
                'montant_total' => PaySlip::where('company_id', $companyId)
                    ->whereYear('salary_month', $currentYear)
                    ->whereMonth('salary_month', $currentMonth)
                    ->where('pay_type', 'journalier')
                    ->sum('net_payble') ?? 520000,
            ],
        ]);

        // Échéances à venir
        $echeancesAvenir = collect([
            (object) [
                'type' => 'CNPS',
                'libelle' => 'Déclaration CNPS ' . now()->locale('fr_FR')->isoFormat('MMMM'),
                'date_limite' => now()->addDays(10),
                'priorite' => 'Élevée',
                'statut' => 'En cours',
            ],
            (object) [
                'type' => 'ITS',
                'libelle' => 'Déclaration ITS ' . now()->locale('fr_FR')->isoFormat('MMMM'),
                'date_limite' => now()->addDays(15),
                'priorite' => 'Moyenne',
                'statut' => 'En cours',
            ],
            (object) [
                'type' => 'CMU',
                'libelle' => 'Déclaration CMU ' . now()->locale('fr_FR')->isoFormat('MMMM'),
                'date_limite' => now()->addDays(20),
                'priorite' => 'Normale',
                'statut' => 'Brouillon',
            ],
        ]);

        return view('declarations::dashboard', compact(
            'stats',
            'declarationsEvolution',
            'derniersBulletins',
            'dernieresDeclarations',
            'echeancesAvenir'
        ));
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('declarations::index');
    }

    /**
     * Resume index - Liste des bulletins de paie
     */
    public function resumeIndex()
    {
        $companyId = Auth::user()->company_id ?? 1;
        // Récupérer tous les exercices
        $exercices = PaieExercice::where('company_id', Auth::user()->company_id)->get();
        // Récupérer tous les bulletins avec pagination
        $paySlips = PaySlip::with('employee')
            ->where('company_id', $companyId)
            ->orderBy('salary_month', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Statistiques
        $totalBulletins = PaySlip::where('company_id', $companyId)->count();
        $bulletinsValides = PaySlip::where('company_id', $companyId)->where('status', 1)->count();
        $bulletinsEnCours = PaySlip::where('company_id', $companyId)->where('status', 0)->count();
        $masseSalariale = PaySlip::where('company_id', $companyId)->sum('net_payble');
        
        return view('declarations::resume.index', compact(
            'paySlips',
            'totalBulletins',
            'bulletinsValides',
            'bulletinsEnCours',
            'masseSalariale',
            'exercices'
        ));
    }

    /**
     * Resume edit - Formulaire de modification d'un bulletin
     */
    public function resumeEdit($id)
    {
        $paySlip = PaySlip::with('employee')->findOrFail($id);
        
        // Vérifier que l'utilisateur a le droit de modifier ce bulletin
        if ($paySlip->company_id !== (Auth::user()->company_id ?? 1)) {
            abort(403, 'Accès non autorisé');
        }
        
        return view('declarations::resume.edit', compact('paySlip'));
    }

    /**
     * Resume update - Mise à jour d'un bulletin
     */
    public function resumeUpdate(Request $request, $id)
    {
        $paySlip = PaySlip::findOrFail($id);
        
        // Vérifier que l'utilisateur a le droit de modifier ce bulletin
        if ($paySlip->company_id !== (Auth::user()->company_id ?? 1)) {
            abort(403, 'Accès non autorisé');
        }
        
        // Validation des données
        $validated = $request->validate([
            'emploi' => 'nullable|string|max:255',
            'num_cnps_emp' => 'nullable|string|max:50',
            'phone_emp' => 'nullable|string|max:20',
            'situation_emp' => 'nullable|string|max:50',
            'enfant_emp' => 'nullable|integer|min:0',
            'address_emp' => 'nullable|string|max:500',
            'salary_month' => 'required|date',
            'pay_type' => 'required|in:mensuel,journalier,horaire',
            'nbre_jour' => 'nullable|integer|min:0|max:31',
            'parts_emp' => 'nullable|numeric|min:0|max:10',
            'anciennete_emp' => 'nullable|string|max:100',
            'categories_emp' => 'nullable|string|max:255',
            'status' => 'required|integer|in:0,1,2',
            'basic_salary' => 'required|integer|min:0',
            'salary_brut' => 'required|integer|min:0',
            'net_imposable' => 'nullable|integer|min:0',
            'net_sociale' => 'nullable|integer|min:0',
            'total_retenue' => 'nullable|integer|min:0',
            'total_patronale' => 'nullable|integer|min:0',
            'avtg_real' => 'nullable|integer|min:0',
            'net_payble' => 'required|integer|min:0',
            // Retenues sociales
            'cnps_sal' => 'nullable|integer|min:0',
            'cnps_emp' => 'nullable|integer|min:0',
            'cmu_sal' => 'nullable|integer|min:0',
            'cmu_emp' => 'nullable|integer|min:0',
            'acc_trav' => 'nullable|integer|min:0',
            'pf_emp' => 'nullable|integer|min:0',
            // Retenues fiscales
            'Imp_brut' => 'nullable|integer|min:0',
            'ricf' => 'nullable|integer|min:0',
            'imp_net' => 'nullable|integer|min:0',
            'taxe_appr' => 'nullable|integer|min:0',
            'taxe_fpc' => 'nullable|integer|min:0',
            'ce_emp' => 'nullable|integer|min:0',
            'ce_exp_emp' => 'nullable|integer|min:0',
            // Autres éléments
            'allowance' => 'nullable|integer|min:0',
            'commission' => 'nullable|integer|min:0',
            'loan' => 'nullable|integer|min:0',
            'saturation_deduction' => 'nullable|integer|min:0',
            'other_payment' => 'nullable|integer|min:0',
            'overtime' => 'nullable|integer|min:0',
            'pay_leave' => 'nullable|integer|min:0',
            'pay_right' => 'nullable|integer|min:0',
            'avtg_real2' => 'nullable|integer|min:0',
            'avtg_bareme' => 'nullable|integer|min:0',
            'autre_retenue' => 'nullable|integer|min:0',
            'autre_retenue_type' => 'nullable|string|max:255',
            // Entreprise
            'nom_etp' => 'nullable|string|max:255',
            'adresse_etp' => 'nullable|string|max:500',
            'phone_etp' => 'nullable|string|max:20',
            'btp_etp' => 'nullable|string|max:100',
            'code' => 'nullable|string|max:50',
        ]);
        
        // Mise à jour du bulletin
        $paySlip->update($validated);
        
        return redirect()
            ->route('company.declarations.resume.index')
            ->with('success', 'Bulletin de paie mis à jour avec succès');
    }

    /**
     * Resume show - Affichage d'un bulletin
     */
    /**
     * Récupérer les bulletins pour une période donnée (AJAX)
     */
    public function getBulletins(Request $request)
    {
        $periodeId = $request->get('periode');
        $exerciceId = $request->get('exercice');
        $companyId = Auth::user()->company_id ?? 1;
        
        try {
            // Récupérer la période
            $periode = PaiePeriode::where('id', $periodeId)
                ->where('company_id', $companyId)
                ->first();
                
            if (!$periode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Période non trouvée'
                ]);
            }
            
            // Récupérer les bulletins pour cette période
            $bulletins = PaySlip::with('employee')
                ->where('company_id', $companyId)
                ->where('periode_id', $periodeId)
                ->orderBy('salary_month', 'desc')
                ->get();
            
            // Calculer les statistiques
            $stats = [
                'total' => $bulletins->count(),
                'valides' => $bulletins->where('status', 1)->count(),
                'encours' => $bulletins->where('status', 0)->count(),
                'masse_salariale' => $bulletins->sum('net_payble')
            ];
            
            // Formater les bulletins pour l'affichage
            $bulletinsFormatted = $bulletins->map(function ($bulletin) {
                return [
                    'id' => $bulletin->id,
                    'employee_name' => $bulletin->employee ? $bulletin->employee->name : 'Employé inconnu',
                    'emploi' => $bulletin->emploi,
                    'salary_month_formatted' => Carbon::parse($bulletin->salary_month)->translatedFormat('M Y'),
                    'basic_salary' => $bulletin->basic_salary,
                    'salary_brut' => $bulletin->salary_brut,
                    'total_retenue' => $bulletin->total_retenue,
                    'net_payble' => $bulletin->net_payble,
                    'status' => $bulletin->status
                ];
            });
            
            return response()->json([
                'success' => true,
                'bulletins' => $bulletinsFormatted,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des bulletins: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Récupérer les périodes pour un exercice donné (AJAX)
     */
    public function getPeriodes(Request $request)
    {
        $exerciceId = $request->get('exercice');
        $companyId = Auth::user()->company_id ?? 1;
        
        try {
            $periodes = PaiePeriode::where('exercice_id', $exerciceId)
                ->where('company_id', $companyId)
                ->orderBy('date_debut', 'asc')
                ->get();
                
            return response()->json($periodes);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des périodes: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Afficher tous les bulletins pour une période donnée dans le modal
     */
    public function bulletinsAll($periodeId)
    {
        $companyId = Auth::user()->company_id ?? 1;
        
        // Récupérer la période
        $periode = PaiePeriode::where('id', $periodeId)
            ->where('company_id', $companyId)
            ->firstOrFail();
            
        // Récupérer tous les bulletins pour cette période avec toutes les données nécessaires
        $bulletins = PaySlip::with('employee')
            ->where('company_id', $companyId)
            ->where('periode_id', $periodeId)
            ->orderBy('salary_month', 'desc')
            ->get();
            
        // Récupérer tous les bulletins de l'année pour les calculs cumulés (YTD)
        $year = Carbon::parse($periode->date_fin)->year;
        $paySlipss = PaySlip::where('company_id', $companyId)
            ->whereBetween('salary_month', [$year . '-01', $year . '-12'])
            ->get();
            
        // Calculer les totaux pour les statistiques
        $totalBulletins = $bulletins->count();
        $bulletinsValides = $bulletins->where('status', 1)->count();
        $bulletinsEnCours = $bulletins->where('status', 0)->count();
        $masseSalariale = $bulletins->sum('net_payble');

        $company = Company::findOrFail($companyId);
        
        return view('declarations::resume.bulletins_all', compact(
            'bulletins',
            'periode',
            'totalBulletins',
            'bulletinsValides',
            'bulletinsEnCours',
            'masseSalariale',
            'company',
            'paySlipss'
        ));
    }

    public function resumeShow($id)
    {
        $paySlip = PaySlip::with('employee')->findOrFail($id);
        $periode = PaiePeriode::findOrFail($paySlip->periode_id);

        $company = Company::findOrFail($paySlip->company_id);
        // Vérifier que l'utilisateur a le droit de voir ce bulletin
        if ($paySlip->company_id !== (Auth::user()->company_id ?? 1)) {
            abort(403, 'Accès non autorisé');
        }

        // Libellé du type de contrat : employees.contrat référence contracts.id,
        // dont le type_id porte le libellé (CDI, CDD, ...).
        $typeContrat = null;
        if (!empty($paySlip->employee->contrat)) {
            $typeContrat = DB::table('contracts')
                ->join('contract_types', 'contracts.type_id', '=', 'contract_types.id')
                ->where('contracts.id', $paySlip->employee->contrat)
                ->value('contract_types.name');
        }

        return view('declarations::resume.show', compact('paySlip', 'periode', 'company', 'typeContrat'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('declarations::create');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $companyId = Auth::user()->company_id ?? 1;
        
        $paySlip = PaySlip::where('id', $id)
            ->where('company_id', $companyId)
            ->firstOrFail();
            
        try {
            $paySlip->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bulletin supprimé avec succès'
                ]);
            }
            
            return redirect()
                ->route('company.declarations.resume.index')
                ->with('success', 'Bulletin supprimé avec succès');
                
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Suppression groupée de bulletins
     */
    public function bulkDestroy(Request $request)
    {
        $companyId = Auth::user()->company_id ?? 1;
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun bulletin sélectionné'
            ], 400);
        }
        
        try {
            PaySlip::whereIn('id', $ids)
                ->where('company_id', $companyId)
                ->delete();
                
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' bulletin(s) supprimé(s) avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression groupée : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('declarations::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('declarations::edit');
    }

    public function livrepaieMensuel(Request $request)
    {
        // Récupérer les données pour les listes déroulantes
        $company = Company::findOrFail(Auth::user()->company_id ?? 1);
        
        // Récupérer tous les exercices
        $exercices = PaieExercice::where('company_id', Auth::user()->company_id)->get();

        // Récupérer l'exercice et la période actifs depuis la session
        $paieController = app(\Modules\PaieSalaries\Http\Controllers\PaieSalariesController::class);
        $activeExercice = $paieController->getActiveExercice();
        $activePeriode = $paieController->getActivePeriode();

        // Récupérer les employés avec contrat valide pour la période
        $employeeQuery = Employee::where('company_id', Auth::user()->company_id)
            ->where('salary_type', 1)
            ->where('is_active', 1);

        if ($activePeriode) {
            $firstDayOfMonth = Carbon::parse($activePeriode->date_debut)->startOfMonth()->toDateString();
            $lastDayOfMonth = Carbon::parse($activePeriode->date_debut)->endOfMonth()->toDateString();
            
            $employeeQuery->whereHas('contracts', function($query) use ($firstDayOfMonth, $lastDayOfMonth) {
                $query->where('start_date', '<=', $lastDayOfMonth)
                      ->where(function($q) use ($firstDayOfMonth) {
                          $q->where('end_date', '>=', $firstDayOfMonth)
                            ->orWhereNull('end_date');
                      });
            });
        }

        $employee = $employeeQuery->get();
        
        return view('declarations::livrepaie.mensuel', compact('company', 'exercices', 'employee', 'activeExercice', 'activePeriode'));
    }

    public function livrepaieAnnuel(Request $request)
    {
        // Récupérer les données pour les listes déroulantes
        $company = Company::findOrFail(Auth::user()->company_id ?? 1);
        
        // Récupérer les mois disponibles
        $exercices = PaieExercice::where('company_id', Auth::user()->company_id)->get();

        
        // Récupérer l'exercice et la période actifs depuis la session
        $paieController = app(\Modules\PaieSalaries\Http\Controllers\PaieSalariesController::class);
        $activeExercice = $paieController->getActiveExercice();
        $activePeriode = $paieController->getActivePeriode();

        // Récupérer les employés avec contrat valide pour la période
        $employeeQuery = Employee::where('company_id', Auth::user()->company_id)
            ->where('salary_type', 1)
            ->where('is_active', 1);

        if ($activePeriode) {
            $firstDayOfMonth = Carbon::parse($activePeriode->date_debut)->startOfMonth()->toDateString();
            $lastDayOfMonth = Carbon::parse($activePeriode->date_debut)->endOfMonth()->toDateString();
            
            $employeeQuery->whereHas('contracts', function($query) use ($firstDayOfMonth, $lastDayOfMonth) {
                $query->where('start_date', '<=', $lastDayOfMonth)
                      ->where(function($q) use ($firstDayOfMonth) {
                          $q->where('end_date', '>=', $firstDayOfMonth)
                            ->orWhereNull('end_date');
                      });
            });
        }

        $employee = $employeeQuery->get();
        
        return view('declarations::livrepaie.annuel', compact('company', 'exercices', 'activeExercice', 'activePeriode'));
    }

    public function livrepaieIndividuel(Request $request)
    {
        // Récupérer les données pour les listes déroulantes
        $company = Company::findOrFail(Auth::user()->company_id ?? 1);

        // Récupérer tous les exercices
        $exercices = PaieExercice::where('company_id', Auth::user()->company_id)->get();

        // Récupérer l'exercice et la période actifs depuis la session
        $paieController = app(\Modules\PaieSalaries\Http\Controllers\PaieSalariesController::class);
        $activeExercice = $paieController->getActiveExercice();
        $activePeriode = $paieController->getActivePeriode();

        // Récupérer les employés actifs avec contrat valide pour la période
        $employeeQuery = Employee::where('company_id', Auth::user()->company_id)
            ->where(function($query) {
                $query->where('salary_type', 1)
                      ->orWhereNull('salary_type');
            })
            ->where('is_active', 1);

        if ($activePeriode) {
            $firstDayOfMonth = Carbon::parse($activePeriode->date_debut)->startOfMonth()->toDateString();
            $lastDayOfMonth = Carbon::parse($activePeriode->date_debut)->endOfMonth()->toDateString();
            
            $employeeQuery->whereHas('contracts', function($query) use ($firstDayOfMonth, $lastDayOfMonth) {
                $query->where('start_date', '<=', $lastDayOfMonth)
                      ->where(function($q) use ($firstDayOfMonth) {
                          $q->where('end_date', '>=', $firstDayOfMonth)
                            ->orWhereNull('end_date');
                      });
            });
        }

        $employee = $employeeQuery->get();

        return view('declarations::livrepaie.individuel', compact('company', 'exercices', 'employee', 'activeExercice', 'activePeriode'));
    }

    public function cotisation(Request $request)
    {
        // Récupérer les données pour les listes déroulantes
        $company = Company::findOrFail(Auth::user()->company_id ?? 1);
        
        // Récupérer les mois disponibles
        $exercices = PaieExercice::where('company_id', Auth::user()->company_id)->get();

        
        // Récupérer l'exercice et la période actifs depuis la session
        $paieController = app(\Modules\PaieSalaries\Http\Controllers\PaieSalariesController::class);
        $activeExercice = $paieController->getActiveExercice();
        $activePeriode = $paieController->getActivePeriode();

        // Récupérer les employés avec contrat valide pour la période
        $employeeQuery = Employee::where('company_id', Auth::user()->company_id)
            ->where('salary_type', 1)
            ->where('is_active', 1);

        if ($activePeriode) {
            $firstDayOfMonth = Carbon::parse($activePeriode->date_debut)->startOfMonth()->toDateString();
            $lastDayOfMonth = Carbon::parse($activePeriode->date_debut)->endOfMonth()->toDateString();
            
            $employeeQuery->whereHas('contracts', function($query) use ($firstDayOfMonth, $lastDayOfMonth) {
                $query->where('start_date', '<=', $lastDayOfMonth)
                      ->where(function($q) use ($firstDayOfMonth) {
                          $q->where('end_date', '>=', $firstDayOfMonth)
                            ->orWhereNull('end_date');
                      });
            });
        }

        $employee = $employeeQuery->get();

        return view('declarations::cotisation.index', compact('company', 'exercices', 'employee', 'activeExercice', 'activePeriode'));
    }

    public function declarationMensuelle(Request $request)
    {
        // Récupérer les données pour les listes déroulantes
        $company = Company::findOrFail(Auth::user()->company_id ?? 1);
        
        // Récupérer les mois disponibles
        $exercices = PaieExercice::where('company_id', Auth::user()->company_id)->get();

        
        // Récupérer l'exercice et la période actifs depuis la session
        $paieController = app(\Modules\PaieSalaries\Http\Controllers\PaieSalariesController::class);
        $activeExercice = $paieController->getActiveExercice();
        $activePeriode = $paieController->getActivePeriode();

        // Récupérer les employés avec contrat valide pour la période
        $employeeQuery = Employee::where('company_id', Auth::user()->company_id)
            ->where('salary_type', 1)
            ->where('is_active', 1);

        if ($activePeriode) {
            $firstDayOfMonth = Carbon::parse($activePeriode->date_debut)->startOfMonth()->toDateString();
            $lastDayOfMonth = Carbon::parse($activePeriode->date_debut)->endOfMonth()->toDateString();
            
            $employeeQuery->whereHas('contracts', function($query) use ($firstDayOfMonth, $lastDayOfMonth) {
                $query->where('start_date', '<=', $lastDayOfMonth)
                      ->where(function($q) use ($firstDayOfMonth) {
                          $q->where('end_date', '>=', $firstDayOfMonth)
                            ->orWhereNull('end_date');
                      });
            });
        }

        $employee = $employeeQuery->get();

        return view('declarations::declaration.mensuelle', compact('company', 'exercices', 'activeExercice', 'activePeriode'));
    }

    public function declarationAnnuelle(Request $request)
    {
        // Récupérer les données pour les listes déroulantes
        $company = Company::findOrFail(Auth::user()->company_id ?? 1);
        
        // Récupérer les mois disponibles
        $exercices = PaieExercice::where('company_id', Auth::user()->company_id)->get();

        
        // Récupérer l'exercice et la période actifs depuis la session
        $paieController = app(\Modules\PaieSalaries\Http\Controllers\PaieSalariesController::class);
        $activeExercice = $paieController->getActiveExercice();
        $activePeriode = $paieController->getActivePeriode();

        // Récupérer les employés avec contrat valide pour la période
        $employeeQuery = Employee::where('company_id', Auth::user()->company_id)
            ->where('salary_type', 1)
            ->where('is_active', 1);

        if ($activePeriode) {
            $firstDayOfMonth = Carbon::parse($activePeriode->date_debut)->startOfMonth()->toDateString();
            $lastDayOfMonth = Carbon::parse($activePeriode->date_debut)->endOfMonth()->toDateString();
            
            $employeeQuery->whereHas('contracts', function($query) use ($firstDayOfMonth, $lastDayOfMonth) {
                $query->where('start_date', '<=', $lastDayOfMonth)
                      ->where(function($q) use ($firstDayOfMonth) {
                          $q->where('end_date', '>=', $firstDayOfMonth)
                            ->orWhereNull('end_date');
                      });
            });
        }

        $employee = $employeeQuery->get();

        return view('declarations::declaration.annuelle', compact('company', 'exercices', 'activeExercice', 'activePeriode'));
    }

    public function get_periodes(Request $request){
        $exercice = $request->exercice;
        $periodes = PaiePeriode::where('company_id', Auth::user()->company_id)
                                    ->where('exercice_id', '=', $exercice)
                                    ->orderBy('id', 'desc')
                                    ->get();
        return response()->json($periodes);
    }

    public function get_paylists(Request $request)
    {
        $periodeId = $request->periode_id;
        
        if (!$periodeId) {
            return response()->json([]);
        }
        
        $paylip_employee = PaySlip::select(
            [
                'employees.id',
                'employees.employee_id as num_emp',
                'employees.name',
                'employees.salary',
                'employees.user_id',
                'employees.tax_payer_id',
                'pay_slips.salary_month',
                'pay_slips.salary_brut',
                'pay_slips.net_imposable',
                'pay_slips.net_sociale',
                'pay_slips.basic_salary',
                'pay_slips.total_retenue',
                'pay_slips.net_payble',
                'pay_slips.allowances',
                'pay_slips.retenues',
                'pay_slips.nbre_jour',
                'pay_slips.avtg_real',
                'pay_slips.avtg_real2',
                'pay_slips.avtg_bareme',
            ]
        )->join(
            'employees',
            function ($join) use ($periodeId) {
                $join->on('employees.id', '=', 'pay_slips.employee_id');
            }
        )->where('pay_slips.periode_id', '=', $periodeId)
        ->where('employees.company_id', Auth::user()->company_id)
        ->get();

        $data = [];
        foreach ($paylip_employee as $employee) {
            $allowanceData = json_decode($employee->allowances, true) ?: [];
            $retenueData = json_decode($employee->retenues, true) ?: [];
            
            $data[] = [
                'employee_id' => Auth::user()->employeeIdFormat($employee->num_emp),
                'emp_id' => $employee->id,
                'name' => $employee->name,
                'salary_month' => $employee->salary_month,
                'basic_salary' => !empty($employee->basic_salary) ? number_format($employee->basic_salary, 0 ,'.',' ') : '0',
                'tax_payer_id' => $employee->tax_payer_id,
                'allowance' => $allowanceData,
                'retenue' => $retenueData,
                'total_retenue' => !empty($employee->total_retenue) ? number_format($employee->total_retenue, 0 ,'.',' ') : '0',
                'net_payble' => !empty($employee->net_payble) ? number_format($employee->net_payble, 0 ,'.',' ') : '0',
                'nbre_jour'  => $employee->nbre_jour ?? 30,
                'avtg_real'  => !empty($employee->avtg_real) ? number_format($employee->avtg_real, 0 ,'.',' ') : '0',
                'avtg_real2' => !empty($employee->avtg_real2) ? number_format($employee->avtg_real2, 0 ,'.',' ') : '0',
                'avtg_bareme'=> !empty($employee->avtg_bareme) ? number_format($employee->avtg_bareme, 0 ,'.',' ') : '0',
                'salary_brut' => number_format($employee->salary_brut, 0 ,'.',' '),
            ];
        }
        
        return response()->json($data);
    }

    public function get_disa(Request $request)
    {
        // Récupérer l'année à partir de la requête
        $year = $request->datePicker;

        // Définir le début et la fin de l'intervalle
        $start_month = $year . '-01';
        $end_month = $year . '-12';

        // Rechercher tous les bulletins de paie pour l'année spécifiée
        $validatePayslip = PaySlip::whereBetween('salary_month', [$start_month, $end_month])
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->toArray();

        if (empty($validatePayslip)) {
            return [];
        } else {
            // Récupérer la liste des employés avec leurs bulletins dans l'année
            $employees = Employee::select('employees.*')
                ->where('employees.company_id', Auth::user()->company_id)
                ->where('employees.salary_type', '=', '1')
                ->where('employees.contrat', '<>', '2')
                ->where('employees.charge_its', '!=', 2)
                ->whereExists(function ($query) use ($start_month, $end_month) {
                    $query->select(DB::raw(1))
                        ->from('pay_slips')
                        ->whereColumn('pay_slips.employee_id', 'employees.id')
                        ->whereBetween('pay_slips.salary_month', [$start_month, $end_month]);
                })
                ->get();

            $data = [];

            foreach ($employees as $employee) {
                // Récupérer tous les bulletins de l'employé pour l'année
                $payslips = PaySlip::where('employee_id', $employee->id)
                    ->whereBetween('salary_month', [$start_month, $end_month])
                    ->get();

                // Initialiser les variables pour les montants cumulés
                $cumul_nbre_jour = 0;
                $cumul_salary_brut = 0;
                $cumul_net_imposable = 0;
                $cumul_net_sociale = 0;
                $cumul_basic_salary = 0;
                $cumul_total_retenue = 0;
                $cumul_net_payble = 0;
                $cumul_Imp_brut = 0;
                $cumul_ricf = 0;
                $cumul_imp_net = 0;
                $cumul_cnps_sal = 0;
                $cumul_cmu_sal = 0;
                $cumul_overtime = 0;
                $cumul_loan = 0;
                $cumul_avtg_real = 0;
                $cumul_avtg_real2 = 0;
                $cumul_avtg_bareme = 0;
                $cumul_net_nonimposble = 0;
                $allowanceData = '';
                $allowanceDataExpat = '';
                $cumul_allow = 0;
                $cumul_allow_expat = 0;

                // Cumuler les montants de tous les bulletins
                foreach ($payslips as $payslip) {
                    $cumul_nbre_jour += floatval($payslip->nbre_jour);
                    $cumul_salary_brut += floatval($payslip->salary_brut);
                    $cumul_net_imposable += floatval($payslip->net_imposable);
                    $cumul_net_sociale += floatval($payslip->net_sociale);
                    $cumul_basic_salary += floatval($payslip->basic_salary);
                    $cumul_total_retenue += floatval($payslip->total_retenue);
                    $cumul_net_payble += floatval($payslip->net_payble);
                    $cumul_Imp_brut += floatval($payslip->Imp_brut);
                    $cumul_ricf += floatval($payslip->ricf);
                    $cumul_imp_net += floatval($payslip->imp_net);
                    $cumul_cnps_sal += floatval($payslip->cnps_sal);
                    $cumul_cmu_sal += floatval($payslip->cmu_sal);
                    $cumul_overtime += floatval($payslip->overtime);
                    $cumul_loan += floatval($payslip->loan);
                    $cumul_avtg_real += floatval($payslip->avtg_real);
                    $cumul_avtg_real2 += floatval($payslip->avtg_real2);
                    $cumul_avtg_bareme += floatval($payslip->avtg_bareme);

                    if($employee->charge_expat == 'local'){
                        $allowanceData = json_decode($payslip->allowance, true);
                        $total_brut_local = floatval($payslip->salary_brut+$payslip->avtg_real+$payslip->avtg_real2+$payslip->avtg_bareme);
                        $found = false;
                        $resltexo = 0;
                        $exo = 0;
                        $toto = 0;
                        $toto2 = 0;
                        $totalbase = 0; // Déclaration de toto ici
                        if($allowanceData){
                            foreach($allowanceData as $key => $value){
                                if(isset($value['allowance_option']) && ($value['allowance_option'] == 26 || $value['allowance_option'] == 11)){
                                    $totalbase += isset($value['amount']) ? floatval($value['amount']) : 0;
                                    $found = true;
                                }

                                if (strpos($value['trait_fisc'], '10% - Art 116') === 0) {
                                    $toto += isset($value['amount']) ? floatval($value['amount']) : 0;
                                    $found = true;
                                }

                                if (strpos($value['trait_fisc'], '100% - Art 116') === 0) {
                                    if($value['allowance_option'] == 11 && isset($value['amount']) && floatval($value['amount']) > 30000){
                                        $toto2 += 30000;
                                    } else {
                                        $toto2 += isset($value['amount']) ? floatval($value['amount']) : 0;
                                    }
                                    $found = true;
                                }
                            }

                            $exo = ((($total_brut_local - $totalbase) * 10) / 100);

                            if($toto > $exo) {
                                $resltexo += ($exo + $toto2);
                            } else {
                                $resltexo += ($toto + $toto2);
                            }
                            $cumul_allow += $resltexo;
                        }
                    }else{
                        $allowanceDataExpat = json_decode($payslip->allowance, true);
                        $total_brut_expat = floatval($payslip->salary_brut+$payslip->avtg_real+$payslip->avtg_real2+$payslip->avtg_bareme);
                        $found_expat = false;
                        $resltexo_expat = 0;
                        $exo_expat = 0;
                        $toto_expat = 0;
                        $toto2_expat = 0;
                        $totalbase_expat = 0;
                        if($allowanceDataExpat){
                            foreach($allowanceDataExpat as $key => $value){
                                if(isset($value['allowance_option']) && ($value['allowance_option'] == 26 || $value['allowance_option'] == 11)){
                                    $totalbase_expat += isset($value['amount']) ? floatval($value['amount']) : 0;
                                    $found_expat = true;
                                }

                                if (strpos($value['trait_fisc'], '10% - Art 116') === 0) {
                                    $toto_expat += isset($value['amount']) ? floatval($value['amount']) : 0;
                                    $foufound_expatnd = true;
                                }

                                if (strpos($value['trait_fisc'], '100% - Art 116') === 0) {
                                    if($value['allowance_option'] == 11 && isset($value['amount']) && floatval($value['amount']) > 30000){
                                        $toto2_expat += 30000;
                                    } else {
                                        $toto2_expat += isset($value['amount']) ? floatval($value['amount']) : 0;
                                    }
                                    $found_expat = true;
                                }
                            }

                            $exo_expat = ((($total_brut_expat - $totalbase_expat) * 10) / 100);

                            if($toto_expat > $exo_expat) {
                                $resltexo_expat += ($exo_expat + $toto2_expat);
                            } else {
                                $resltexo_expat += ($toto_expat + $toto2_expat);
                            }
                            $cumul_allow_expat += $resltexo_expat;
                        }
                    }

                    // Calculer le net non imposable pour chaque bulletin
                    $net_nonimposable = floatval($payslip->salary_brut) - floatval($payslip->net_imposable);
                    $cumul_net_nonimposble += $net_nonimposable;
                }

                // Récupérer le poste et la catégorie de l'employé
                $designation = Designation::find($employee->designation_id);
                $jobCategory = JobCategory::find($employee->categorie);

                // Somme des avantages pour l'employé
                $somme_montant_reel = Avantage::where('company_id', Auth::user()->company_id)
                    ->where('employee_Id', $employee->id)
                    ->where('type_avantage', 2)
                    ->sum('montant_reel');

                $somme_montant_bare = Avantage::where('company_id', Auth::user()->company_id)
                    ->where('employee_Id', $employee->id)
                    ->where('type_avantage', 1)
                    ->sum('amount');

                $somme_montant_reel1 = Avantage::where('company_id', Auth::user()->company_id)
                    ->where('employee_Id', $employee->id)
                    ->where('type_avantage', '=', 1)
                    ->sum('montant_reel');

                // Stocker les données cumulées
                $data[] = [
                    'employee_id' => Auth::user()->employeeIdFormat($employee->id),
                    'emp_id' => $employee->id,
                    'num_cnps' => $employee->num_cnps,
                    'name' => $employee->name,
                    'salary_type' => $employee->salary_type,
                    'sous_categorie' => $employee->sous_categorie,
                    'gender' => $employee->gender,
                    'martalstatu_id' => $employee->martalstatu_id,
                    'charge_expat' => $employee->charge_expat,
                    'parts' => $employee->parts,
                    'dob' => $employee->dob,
                    'start_date' => $employee->company_doj,
                    'nationality' => $employee->nationality,
                    'montant_reel' => $somme_montant_reel,
                    'montant_reel1' => $somme_montant_reel1,
                    'amount_bareme' => $somme_montant_bare,
                    'avtg_real' => $cumul_avtg_real,
                    'avtg_real2' => $cumul_avtg_real2,
                    'avtg_bareme' => $cumul_avtg_bareme,
                    'enfant' => $employee->enfant,
                    'basic_salary' => $cumul_basic_salary,
                    'tax_payer_id' => $cumul_nbre_jour, // Moyenne des jours travaillés
                    'allowance' => $payslips->first()->allowance, // On garde le dernier format d'allocation
                    'allowances_option' => AllowanceOption::all(),
                    'allowanceamount' => $cumul_allow, // Collect all allowances from all payslips
                    'allowanceExpat' => $cumul_allow_expat,
                    'salary_brut' => $cumul_salary_brut,
                    'net_nonimposble' => $cumul_net_nonimposble,
                    'net_imposable' => $cumul_net_imposable,
                    'Imp_brut' => $cumul_Imp_brut,
                    'ricf' => $cumul_ricf,
                    'imp_net' => $cumul_imp_net,
                    'total_retenue' => $cumul_total_retenue,
                    'cnps_sal' => $cumul_cnps_sal,
                    'cmu_sal' => $cumul_cmu_sal,
                    'overtime' => $cumul_overtime,
                    'loan' => $cumul_loan,
                    'net_payble' => $cumul_net_payble,
                    'poste' => $designation ? $designation->name : '',
                    'categ_emp' => $jobCategory ? $jobCategory->title : '',
                ];
            }

            return response()->json($data);
        }
    }

    public function get_paylists_individuel(Request $request)
    {
        $exerciceId = $request->exercice_id;
        $data = [];
        
        // Récupérer l'exercice avec ses périodes
        $exercice = PaieExercice::with('periodes')->find($exerciceId);
            
        if (!$exercice || $exercice->company_id != Auth::user()->company_id) {
            return [];
        }

        // Extraire les IDs des périodes
        $periodeIds = $exercice->periodes->pluck('id');
        
        if ($periodeIds->isEmpty()) {
            return [];
        }

        // Recherche des fiches de paie de l'employé pour l'exercice sélectionné
        $payslips = PaySlip::select(
            [
                'employees.id',
                'employees.employee_id as num_emp',
                'employees.name',
                'employees.salary',
                'employees.user_id',
                'employees.tax_payer_id',
                'employees.gender',
                'pay_slips.salary_month',
                'pay_slips.salary_brut',
                'pay_slips.basic_salary',
                'pay_slips.total_retenue',
                'pay_slips.net_payble',
                'pay_slips.allowances',
                'pay_slips.nbre_jour',
                'pay_slips.avtg_real',
                'pay_slips.avtg_real2',
                'pay_slips.avtg_bareme',
                'pay_slips.retenues',
            ]
        )->leftJoin('employees', 'employees.id', '=', 'pay_slips.employee_id')
         ->whereIn('pay_slips.periode_id', $periodeIds)
         ->where('employees.company_id', Auth::user()->company_id)
         ->get();


        // Si aucune donnée n'est trouvée, renvoyer un tableau vide
        if ($payslips->isEmpty()) {
            return [];
        }

        // Formater les données pour chaque mois
        foreach ($payslips as $employee) {
            $allowanceData = json_decode($employee->allowances, true);
            $retenueData = json_decode($employee->retenues, true);

            $data[] = [
                'employee_id' => Auth::user()->employeeIdFormat($employee->num_emp),
                'emp_id' => $employee->id,
                'emp_user_id' => $employee->user_id,
                'name' => $employee->name,
                'sexe' => $employee->gender,
                'salary_month' => $employee->salary_month,
                'basic_salary' => !empty($employee->basic_salary) ? number_format($employee->basic_salary, 0 ,'.',' ') : '0',
                'tax_payer_id' => $employee->tax_payer_id,
                'allowance' => $allowanceData,
                'retenue' => $retenueData,
                'nbre_jour'  => $employee->nbre_jour,
                'avtg_real'  => !empty($employee->avtg_real) ? $employee->avtg_real : '0',
                'avtg_real2' => !empty($employee->avtg_real2) ? $employee->avtg_real2 : '0',
                'avtg_bareme'=> !empty($employee->avtg_bareme) ? $employee->avtg_bareme : '0',
                'salary_brut' => number_format($employee->salary_brut, 0 ,'.',' '),
                'total_retenue' => number_format($employee->total_retenue, 0 ,'.',' '),
                'net_payble' => number_format(($employee->net_payble), 0 ,'.',' '),
            ];
        }

        return response()->json($data);
    }

    public function get_paylists_annuel(Request $request)
    {
        // Récupérer l'exercice à partir de la requête
        $exerciceId = $request->exercice_id;
        
        // Récupérer l'exercice avec ses périodes
        $exercice = PaieExercice::with('periodes')->find($exerciceId);
        
        if (!$exercice || $exercice->company_id != Auth::user()->company_id) {
            return [];
        }
        
        // Extraire les IDs des périodes
        $periodeIds = $exercice->periodes->pluck('id');
        
        if ($periodeIds->isEmpty()) {
            return [];
        }

        // Rechercher tous les bulletins de paie pour l'exercice spécifié
        $paylip_employee = PaySlip::select(
            [
                'employees.id',
                'employees.employee_id as num_emp',
                'employees.name',
                'employees.salary',
                'employees.user_id',
                'employees.tax_payer_id',
                'employees.gender',
                'pay_slips.salary_month',
                'pay_slips.salary_brut',
                'pay_slips.net_imposable',
                'pay_slips.net_sociale',
                'pay_slips.basic_salary',
                'pay_slips.total_retenue',
                'pay_slips.net_payble',
                'pay_slips.allowances',
                'pay_slips.nbre_jour',
                'pay_slips.avtg_real',
                'pay_slips.avtg_real2',
                'pay_slips.avtg_bareme',
                'pay_slips.retenues'
            ]
        )
            ->leftJoin('employees', 'employees.id', '=', 'pay_slips.employee_id')
            ->whereIn('pay_slips.periode_id', $periodeIds)
            ->where('employees.company_id', Auth::user()->company_id)
            ->get();

        // Vérifier si aucune donnée n'est trouvée
        if ($paylip_employee->isEmpty()) {
            return []; // Retourner un tableau vide
        }

        $data = [];

        // Grouper les données par employé et cumuler les montants
        foreach ($paylip_employee as $employee) {
            $employeeId = $employee->id;
            $allowanceData = json_decode($employee->allowances, true);
            $retenueData = json_decode($employee->retenues, true);

            // Initialiser ou mettre à jour les données cumulées
            if (!isset($data[$employeeId])) {
                $data[$employeeId] = [
                    'employee_id' => Auth::user()->employeeIdFormat($employee->num_emp),
                    'emp_id' => $employee->id,
                    'name' => $employee->name,
                    'gender' => $employee->gender,
                    'tax_payer_id' => $employee->tax_payer_id,
                    'emp_user_id' => $employee->user_id,
                    'allowances' => [], // Initialiser comme tableau vide
                    'retenues' => [], // Initialiser comme tableau vide
                    'date_debut' => $exercice->date_debut,
                    'date_fin' => $exercice->date_fin,
                    'net_imposable' => 0,
                    'net_sociale' => 0,
                    'basic_salary' => 0,
                    'salary_brut' => 0,
                    'total_retenue' => 0,
                    'total_allowances' => 0,
                    'net_payble' => 0,
                    'nbre_jour' => 0,
                    'avtg_real' => 0,
                    'avtg_real2' => 0,
                    'avtg_bareme' => 0,
                ];
            }

            // Traiter les allowances et les additionner par type
            if ($allowanceData) {
                foreach ($allowanceData as $allowance) {
                    $allowanceKey = isset($allowance['code']) ? $allowance['code'] : $allowance['title'];
                    $amount = isset($allowance['amount']) ? floatval($allowance['amount']) : 0;

                    // Vérifier si cette prime existe déjà pour cet employé
                    $existingIndex = -1;
                    foreach ($data[$employeeId]['allowances'] as $index => $existingAllowance) {
                        if ((isset($existingAllowance['code']) && isset($allowance['code']) && $existingAllowance['code'] === $allowance['code']) ||
                            (isset($existingAllowance['title']) && isset($allowance['title']) && $existingAllowance['title'] === $allowance['title'])) {
                            $existingIndex = $index;
                            break;
                        }
                    }

                    if ($existingIndex >= 0) {
                        // Additionner au montant existant
                        $data[$employeeId]['allowances'][$existingIndex]['amount'] += $amount;
                    } else {
                        // Ajouter une nouvelle prime
                        $data[$employeeId]['allowances'][] = [
                            'title' => $allowance['title'],
                            'code' => isset($allowance['code']) ? $allowance['code'] : '',
                            'amount' => $amount,
                            'allowance_option' => isset($allowance['allowance_option_id']) ? $allowance['allowance_option_id'] : ''
                        ];
                    }
                }
            }

            // Traiter les retenues et les additionner par type
            if ($retenueData) {
                foreach ($retenueData as $retenue) {
                    $retenueKey = isset($retenue['code']) ? $retenue['code'] : $retenue['libelle'];
                    $amount = isset($retenue['amount']) ? floatval($retenue['amount']) : 0;

                    // Vérifier si cette retenue existe déjà pour cet employé
                    $existingIndex = -1;
                    foreach ($data[$employeeId]['retenues'] as $index => $existingRetenue) {
                        if ((isset($existingRetenue['code']) && isset($retenue['code']) && $existingRetenue['code'] === $retenue['code']) ||
                            (isset($existingRetenue['libelle']) && isset($retenue['libelle']) && $existingRetenue['libelle'] === $retenue['libelle'])) {
                            $existingIndex = $index;
                            break;
                        }
                    }

                    if ($existingIndex >= 0) {
                        // Additionner au montant existant
                        $data[$employeeId]['retenues'][$existingIndex]['amount'] += $amount;
                    } else {
                        // Ajouter une nouvelle retenue
                        $data[$employeeId]['retenues'][] = [
                            'libelle' => $retenue['libelle'],
                            'code' => isset($retenue['code']) ? $retenue['code'] : '',
                            'amount' => $amount,
                            'type' => isset($retenue['type']) ? $retenue['type'] : 'retenue'
                        ];
                    }
                }
            }

            // Calculer le total des allowances pour cette période
            $total_allowances = 0;
            if ($allowanceData) {
                foreach ($allowanceData as $allowance) {
                    $total_allowances += isset($allowance['amount']) ? floatval($allowance['amount']) : 0;
                }
            }

            // Cumuler les montants pour l'employé
            $data[$employeeId]['total_allowances'] += $total_allowances;
            $data[$employeeId]['net_imposable'] += $employee->net_imposable ?? 0;
            $data[$employeeId]['net_sociale'] += $employee->net_sociale ?? 0;
            $data[$employeeId]['basic_salary'] += $employee->basic_salary ?? 0;
            $data[$employeeId]['salary_brut'] += $employee->salary_brut ?? 0;
            $data[$employeeId]['total_retenue'] += $employee->total_retenue ?? 0;
            $data[$employeeId]['net_payble'] += $employee->net_payble ?? 0;
            $data[$employeeId]['nbre_jour'] += $employee->nbre_jour ?? 0;
            $data[$employeeId]['avtg_real'] += $employee->avtg_real ?? 0;
            $data[$employeeId]['avtg_real2'] += $employee->avtg_real2 ?? 0;
            $data[$employeeId]['avtg_bareme'] += $employee->avtg_bareme ?? 0;
        }

        // Réindexer le tableau pour supprimer les clés associatives
        return response()->json(array_values($data));
    }

	public function get_cotisations(Request $request)
    {
        $exerciceId = $request->exercice_id;
        $periodeId = $request->periode_id;
        $employeeId = $request->employee_id;

        // Récupérer l'exercice avec ses périodes
        $exercice = PaieExercice::with('periodes')->find($exerciceId);
        
        if (!$exercice || $exercice->company_id != Auth::user()->company_id) {
            return [];
        }
        
        // Extraire les IDs des périodes
        $periodeIds = $exercice->periodes->pluck('id');
        
        if ($periodeIds->isEmpty()) {
            return [];
        }

        // Si un employé spécifique est sélectionné, filtrer par employé
        if ($employeeId && $employeeId !== "--") {
            if ($periodeId && $periodeId !== "--") {
                $paylip_employee = PaySlip::select(
                    [
                        'employees.id',
                        'employees.employee_id',
                        'employees.name',
                        'employees.gender',
                        'employees.salary',
                        'employees.user_id',
                        'employees.tax_payer_id',
                        'employees.charge_expat',
                        'pay_slips.salary_brut',
                        'pay_slips.net_imposable',
                        'pay_slips.net_sociale',
                        'pay_slips.basic_salary',
                        'pay_slips.total_retenue',
                        'pay_slips.net_payble',
                        'pay_slips.allowances',
                        'pay_slips.retenues',
                        'pay_slips.nbre_jour',
                    ]
                ) ->leftJoin('employees', 'employees.id', '=', 'pay_slips.employee_id')
                ->where('pay_slips.periode_id', $periodeId)
                ->where('employees.company_id', Auth::user()->company_id)
                ->where('employees.user_id', $employeeId)
                ->get();
            }else{
                $paylip_employee = PaySlip::select(
                    [
                        'employees.id',
                        'employees.employee_id',
                        'employees.name',
                        'employees.gender',
                        'employees.salary',
                        'employees.user_id',
                        'employees.tax_payer_id',
                        'employees.charge_expat',
                        'pay_slips.salary_brut',
                        'pay_slips.net_imposable',
                        'pay_slips.net_sociale',
                        'pay_slips.basic_salary',
                        'pay_slips.total_retenue',
                        'pay_slips.net_payble',
                        'pay_slips.allowances',
                        'pay_slips.retenues',
                        'pay_slips.nbre_jour',
                    ]
                ) ->leftJoin('employees', 'employees.id', '=', 'pay_slips.employee_id')
                ->whereIn('pay_slips.periode_id', $periodeIds)
                ->where('employees.company_id', Auth::user()->company_id)
                ->where('employees.user_id', $employeeId)
                ->get();
            }
        } else {
            // Pour tous les employés, cumuler les données
            if ($periodeId && $periodeId !== "--") {
                $paylip_employee = PaySlip::select(
                    [
                        'employees.id',
                        'employees.employee_id',
                        'employees.name',
                        'employees.gender',
                        'employees.salary',
                        'employees.user_id',
                        'employees.tax_payer_id',
                        'employees.charge_expat',
                        'pay_slips.salary_brut',
                        'pay_slips.net_imposable',
                        'pay_slips.net_sociale',
                        'pay_slips.basic_salary',
                        'pay_slips.total_retenue',
                        'pay_slips.net_payble',
                        'pay_slips.allowances',
                        'pay_slips.retenues',
                        'pay_slips.nbre_jour',
                    ]
                ) ->leftJoin('employees', 'employees.id', '=', 'pay_slips.employee_id')
                ->where('pay_slips.periode_id', $periodeId)
                ->where('employees.company_id', Auth::user()->company_id)
                ->get();
            }else{
                $paylip_employee = PaySlip::select(
                    [
                        'employees.id',
                        'employees.employee_id',
                        'employees.name',
                        'employees.gender',
                        'employees.salary',
                        'employees.user_id',
                        'employees.tax_payer_id',
                        'employees.charge_expat',
                        'pay_slips.salary_brut',
                        'pay_slips.net_imposable',
                        'pay_slips.net_sociale',
                        'pay_slips.basic_salary',
                        'pay_slips.total_retenue',
                        'pay_slips.net_payble',
                        'pay_slips.allowances',
                        'pay_slips.retenues',
                        'pay_slips.nbre_jour',
                    ]
                ) ->leftJoin('employees', 'employees.id', '=', 'pay_slips.employee_id')
                ->whereIn('pay_slips.periode_id', $periodeIds)
                ->where('employees.company_id', Auth::user()->company_id)
                ->get();
            }
        }

        $data = [];
        
        // Grouper et cumuler les données par employé
        foreach ($paylip_employee as $employee) {
            $allowanceData = json_decode($employee->allowances, true);
            $retenueData = json_decode($employee->retenues, true);
            
            $empKey = $employee->user_id;
            
            if (!isset($data[$empKey])) {
                // Initialiser pour un nouvel employé
                $data[$empKey] = [
                    'employee_id' => Auth::user()->employeeIdFormat($employee->id),
                    'emp_id' => $employee->user_id,
                    'name' => $employee->name,
                    'salary_brut' => 0,
                    'allowance' => [],
                    'retenue' => [],
                    'sexe' => $employee->gender,
                    'charge_expat' => $employee->charge_expat,
                    'total_retenue' => 0,
                    'nbre_jour' => 0,
                    'net_payble' => 0,
                    'net_imposable' => 0,
                    'net_sociale' => 0,
                ];
            }
            
            // Cumuler les valeurs
            $data[$empKey]['salary_brut'] += $employee->salary_brut ?? 0;
            $data[$empKey]['total_retenue'] += $employee->total_retenue ?? 0;
            $data[$empKey]['nbre_jour'] += $employee->nbre_jour ?? 0;
            $data[$empKey]['net_payble'] += $employee->net_payble ?? 0;
            $data[$empKey]['net_imposable'] += $employee->net_imposable ?? 0;
            $data[$empKey]['net_sociale'] += $employee->net_sociale ?? 0;
            
            // Cumuler les allowances par type
            if ($allowanceData) {
                foreach ($allowanceData as $allowance) {
                    $allowanceKey = isset($allowance['code']) ? $allowance['code'] : $allowance['title'];
                    $amount = isset($allowance['amount']) ? floatval($allowance['amount']) : 0;
                    
                    $existingIndex = -1;
                    foreach ($data[$empKey]['allowance'] as $index => $existingAllowance) {
                        if ((isset($existingAllowance['code']) && isset($allowance['code']) && $existingAllowance['code'] === $allowance['code']) ||
                            (isset($existingAllowance['title']) && isset($allowance['title']) && $existingAllowance['title'] === $allowance['title'])) {
                            $existingIndex = $index;
                            break;
                        }
                    }
                    
                    if ($existingIndex >= 0) {
                        $data[$empKey]['allowance'][$existingIndex]['amount'] += $amount;
                    } else {
                        $data[$empKey]['allowance'][] = [
                            'title' => $allowance['title'],
                            'code' => isset($allowance['code']) ? $allowance['code'] : '',
                            'amount' => $amount,
                            'allowance_option' => isset($allowance['allowance_option']) ? $allowance['allowance_option'] : '',
                            'trait_fisc' => isset($allowance['trait_fisc']) ? $allowance['trait_fisc'] : '',
                        ];
                    }
                }
            }
            
            // Cumuler les retenues par type
            if ($retenueData) {
                foreach ($retenueData as $retenue) {
                    $retenueKey = isset($retenue['code']) ? $retenue['code'] : $retenue['libelle'];
                    $amount = isset($retenue['amount']) ? floatval($retenue['amount']) : 0;
                    
                    $existingIndex = -1;
                    foreach ($data[$empKey]['retenue'] as $index => $existingRetenue) {
                        if ((isset($existingRetenue['code']) && isset($retenue['code']) && $existingRetenue['code'] === $retenue['code']) ||
                            (isset($existingRetenue['libelle']) && isset($retenue['libelle']) && $existingRetenue['libelle'] === $retenue['libelle'])) {
                            $existingIndex = $index;
                            break;
                        }
                    }
                    
                    if ($existingIndex >= 0) {
                        $data[$empKey]['retenue'][$existingIndex]['amount'] += $amount;
                    } else {
                        $data[$empKey]['retenue'][] = [
                            'libelle' => $retenue['libelle'],
                            'code' => isset($retenue['code']) ? $retenue['code'] : '',
                            'amount' => $amount,
                            'type' => isset($retenue['type']) ? $retenue['type'] : 'retenue'
                        ];
                    }
                }
            }
        }
        
        return response()->json(array_values($data));
    }

    public function get_decla_efi(Request $request)
    {
        $exerciceId = $request->exercice_id;
        $periodeId = $request->periode_id;

        // Récupérer l'exercice avec ses périodes
        $exercice = PaieExercice::with('periodes')->find($exerciceId);
        
        if (!$exercice || $exercice->company_id != Auth::user()->company_id) {
            return [];
        }
        
        // Extraire les IDs des périodes
        $periodeIds = $exercice->periodes->pluck('id');
        
        if ($periodeIds->isEmpty()) {
            return [];
        }

        $paylip_employee = PaySlip::select(
            [
                'employees.id',
                'employees.user_id',
                'employees.employee_id',
                'employees.name',
                'employees.parts',
                'employees.enfant',
                'employees.charge_expat',
                'employees.salary_type',
                'employees.salary',
                'pay_slips.nbre_jour',
                'pay_slips.salary_brut',
                'pay_slips.net_imposable',
                'pay_slips.net_sociale',
                'pay_slips.basic_salary',
                'pay_slips.total_retenue',
                'pay_slips.net_payble',
                'pay_slips.retenues',
                'pay_slips.allowances',
                'pay_slips.avtg_real',
                'pay_slips.avtg_real2',
                'pay_slips.avtg_bareme',
            ]
        )->leftJoin('employees', 'employees.id', '=', 'pay_slips.employee_id')
            ->where('pay_slips.periode_id', $periodeId)
            ->where('employees.company_id', Auth::user()->company_id)
            ->get();
        $data = [];
        foreach ($paylip_employee as $employee) {
            $allowanceData = json_decode($employee->allowances, true);
            $retenueData = json_decode($employee->retenues, true);
            $net_nonimposble = $employee->salary_brut - $employee->net_imposable;
            $nombreEmployesAvecAvantage = \Modules\NatureAvantage\Models\Avantage::where('company_id', Auth::user()->company_id)->where('is_active', 1)->distinct()->count('employee_id');
            $data[] = [
                'employee_id' => \Auth::user()->employeeIdFormat($employee->id),
                'emp_id' => $employee->id,
                'name' => $employee->name,
                'enfant' => $employee->enfant,
                'parts' => $employee->parts,
                'charge_expat' => $employee->charge_expat,
                'salary_type' => $employee->salary_type,
                'basic_salary' => !empty($employee->basic_salary) ? $employee->salary : '0',
                'tax_payer_id' => $employee->nbre_jour,
                'allowance' => $allowanceData,
                'retenue' => $retenueData,
                'nbrempavtg' => $nombreEmployesAvecAvantage,
                'avtg_real'  => !empty($employee->avtg_real) ? $employee->avtg_real : '0',
                'avtg_real2' => !empty($employee->avtg_real2) ? $employee->avtg_real2 : '0',
                'avtg_bareme'=> !empty($employee->avtg_bareme) ? $employee->avtg_bareme : '0',
                'salary_brut' => $employee->salary_brut,
                'net_nonimposble' => $net_nonimposble,
                'net_imposable' => $employee->net_imposable,
                'total_retenue' => $employee->total_retenue,
                'net_payble' => $employee->net_payble,
            ];
        }
        return response()->json($data);
    }
    
    public function get_decla_edi(Request $request)
    {
        $exerciceId = $request->exercice_id;
        $periodeId = $request->periode_id;

        // Récupérer l'exercice avec ses périodes
        $exercice = PaieExercice::with('periodes')->find($exerciceId);
        
        if (!$exercice || $exercice->company_id != Auth::user()->company_id) {
            return [];
        }
        
        // Extraire les IDs des périodes
        $periodeIds = $exercice->periodes->pluck('id');
        
        if ($periodeIds->isEmpty()) {
            return [];
        }

        $paylip_employee = PaySlip::with(['employee.categorieEmp', 'employee.designation'])->select(
            [
                'employees.id as empId',
                'employees.user_id',
                'employees.employee_id',
                'employees.num_cnps',
                'employees.name',
                'employees.enfant',
                'employees.parts',
                'employees.nationality',
                'employees.salary_type',
                'employees.sous_categorie',
                'employees.categorie',
                'employees.gender',
                'employees.martalstatu_id',
                'employees.charge_expat',
                'employees.salary',
                'employees.tax_payer_id',
                'pay_slips.nbre_jour',
                'pay_slips.salary_brut',
                'pay_slips.net_imposable',
                'pay_slips.net_sociale',
                'pay_slips.basic_salary',
                'pay_slips.total_retenue',
                'pay_slips.net_payble',
                'pay_slips.retenues',
                'pay_slips.allowances',
                'pay_slips.avtg_real',
                'pay_slips.avtg_real2', 
                'pay_slips.avtg_bareme',
            ]
        )->leftJoin('employees', 'employees.id', '=', 'pay_slips.employee_id')
            ->where('pay_slips.periode_id', $periodeId)
            ->where('employees.company_id', Auth::user()->company_id)
            ->get();

        $data = [];
        foreach ($paylip_employee as $paySlip) {
            $allowanceData = json_decode($paySlip->allowances, true);
            $retenueData = json_decode($paySlip->retenues, true);
            $net_nonimposble = $paySlip->salary_brut - $paySlip->net_imposable;
            
            $data[] = [
                'employee_id' => Auth::user()->employeeIdFormat($paySlip->employee_id),
                'emp_id' => $paySlip->empId,
                'num_cnps' => $paySlip->num_cnps,
                'name' => $paySlip->name,
                'enfant' => $paySlip->enfant ?? 0,
                'parts' => $paySlip->parts ?? 0,
                'nationality' => $paySlip->nationality,
                'salary_type' => $paySlip->salary_type,
                'sous_categorie' => $paySlip->sous_categorie,
                'categ_emp' => $paySlip->employee && $paySlip->employee->categorieEmp ? $paySlip->employee->categorieEmp->title : null,      
                'poste' => $paySlip->employee && $paySlip->employee->designation ? $paySlip->employee->designation->name : null,
                'gender' => $paySlip->gender,
                'martalstatu_id' => $paySlip->martalstatu_id,
                'charge_expat' => $paySlip->charge_expat,
                'salary' => $paySlip->salary,
                'basic_salary' => !empty($paySlip->basic_salary) ? $paySlip->basic_salary : '0',
                'tax_payer_id' => $paySlip->nbre_jour,
                'allowance' => $allowanceData,
                'retenue' => $retenueData,
                'avtg_real'  => !empty($paySlip->avtg_real) ? $paySlip->avtg_real : '0',
                'avtg_real2' => !empty($paySlip->avtg_real2) ? $paySlip->avtg_real2 : '0',
                'avtg_bareme'=> !empty($paySlip->avtg_bareme) ? $paySlip->avtg_bareme : '0',
                'salary_brut' => $paySlip->salary_brut,
                'net_nonimposble' => $net_nonimposble,
                'net_imposable' => $paySlip->net_imposable,
                'total_retenue' => $paySlip->total_retenue,
                'net_payble' => $paySlip->net_payble,
            ];
        }
        return response()->json($data);
    }

    public function get_decla_cnps(Request $request)
    {
        $exerciceId = $request->exercice_id;
        $periodeId = $request->periode_id;

        // Récupérer l'exercice avec ses périodes
        $exercice = PaieExercice::with('periodes')->find($exerciceId);
        
        if (!$exercice || $exercice->company_id != Auth::user()->company_id) {
            return [];
        }
        
        // Extraire les IDs des périodes
        $periodeIds = $exercice->periodes->pluck('id');
        
        if ($periodeIds->isEmpty()) {
            return [];
        }
        
        $paylip_employee = PaySlip::select(
            [
                'employees.id',
                'employees.user_id',
                'employees.employee_id',
                'employees.num_cnps',
                'employees.name',
                'employees.salary_type',
                'employees.dob',
                'employees.gender',
                'employees.martalstatu_id',
                'employees.company_doj',
                'employees.parts',
                'employees.enfant',
                'employees.nationality',
                'employees.salary',
                'employees.tax_payer_id',
                'pay_slips.salary_brut',
                'pay_slips.net_sociale',
            ]
        )->leftJoin('employees', 'employees.id', '=', 'pay_slips.employee_id')
            ->where('pay_slips.periode_id', $periodeId)
            ->where('employees.company_id', Auth::user()->company_id)
            ->get();
            
        $data = [];
        foreach ($paylip_employee as $employee) {
            $data[] = [
                'employee_id' => Auth::user()->employeeIdFormat($employee->id),
                'emp_id' => $employee->id,
                'num_cnps' => $employee->num_cnps,
                'name' => $employee->name,
                'salary_type' => $employee->salary_type,
                'dob' => $employee->dob,
                'gender' => $employee->gender,
                'martalstatu_id' => $employee->martalstatu_id,
                'company_doj' => $employee->company_doj,
                'parts' => $employee->parts,
                'nationality' => $employee->nationality,
                'enfant' => $employee->enfant,
                'basic_salary' => !empty($employee->basic_salary) ? number_format($employee->basic_salary, 0 ,'.',' ') : '0',
                'tax_payer_id' => $employee->tax_payer_id,
                'salary_brut' => $employee->salary_brut,
                'net_sociale'  => $employee->net_sociale,
            ];
        }
        return response()->json($data);
    }

    public function get_decla_cmu(Request $request)
    {
        $exerciceId = $request->exercice_id;
        $periodeId = $request->periode_id;

        // Récupérer l'exercice avec ses périodes
        $exercice = PaieExercice::with('periodes')->find($exerciceId);
        
        if (!$exercice || $exercice->company_id != Auth::user()->company_id) {
            return [];
        }
        
        // Extraire les IDs des périodes
        $periodeIds = $exercice->periodes->pluck('id');
        
        if ($periodeIds->isEmpty()) {
            return [];
        }

        $paylip_employee = PaySlip::select(
            [
                'employees.id',
                'employees.user_id',
                'employees.employee_id',
                'employees.num_cnps',
                'employees.num_secu_soc',
                'employees.name',
                'employees.salary_type',
                'employees.dob',
                'employees.gender',
                'employees.martalstatu_id',
                'employees.company_doj',
                'employees.parts',
                'employees.enfant',
                'employees.nationality',
                'employees.salary',
                'employees.tax_payer_id',
                'pay_slips.salary_brut',
            ]
        )->leftJoin('employees', 'employees.id', '=', 'pay_slips.employee_id')
            ->where('pay_slips.periode_id', $periodeId)
            ->where('employees.company_id', Auth::user()->company_id)
            ->get();

        $data = [];
        foreach ($paylip_employee as $employee) {
            $Datalib_enfant = EmployeeCmu::where('employee_id',$employee->id)->get();
            $data[] = [
                'employee_id' => Auth::user()->employeeIdFormat($employee->id),
                'emp_id' => $employee->id,
                'num_cnps' => $employee->num_cnps,
                'famille' => $Datalib_enfant,
                'num_secu_soc' => !empty($employee->num_secu_soc) ? $employee->num_secu_soc : ' ',
                'name' => $employee->name,
                'salary_type' => $employee->salary_type,
                'dob' => $employee->dob,
                'gender' => $employee->gender,
                'martalstatu_id' => $employee->martalstatu_id,
                'company_doj' => $employee->company_doj,
                'parts' => $employee->parts,
                'nationality' => $employee->nationality,
                'enfant' => $employee->enfant,
                'basic_salary' => !empty($employee->basic_salary) ? number_format($employee->basic_salary, 0 ,'.',' ') : '0',
                'tax_payer_id' => $employee->tax_payer_id,
                'salary_brut' => number_format($employee->salary_brut, 0 ,'.',' '),
            ];
        }
        return response()->json($data);
    }

    /**
     * Déclencher la génération de PDF en queue
     */
    public function generateBulletinsPDF(Request $request)
    {
        try {
            $bulletinType = $request->input('bulletin_type');
            $periodeId = $request->input('periode_id');
            $filename = $request->input('filename', 'bulletins_' . time());

            if (!in_array($bulletinType, [1, 2, 3])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type de bulletin invalide'
                ], 400);
            }

            GenerateBulkBulletinsPDF::dispatch(
                $bulletinType,
                $periodeId,
                Auth::id(),
                $filename
            );

            return response()->json([
                'success' => true,
                'message' => 'Génération des bulletins lancée en arrière-plan',
                'job_id' => 'bulletin_' . Auth::id() . '_' . $bulletinType
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer la progression du traitement
     */
    public function getBulletinProgress(Request $request)
    {
        try {
            $bulletinType = $request->input('bulletin_type');
            $cacheKey = "bulletin_generation_" . Auth::id() . "_" . $bulletinType;

            $progress = cache()->get($cacheKey, [
                'progress' => 0,
                'message' => 'En attente...',
                'status' => 'pending',
                'file_path' => null
            ]);

            return response()->json($progress);

        } catch (\Exception $e) {
            return response()->json([
                'progress' => 0,
                'message' => 'Erreur: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Télécharger le PDF généré
     */
    public function downloadBulletinFile(Request $request)
    {
        try {
            $filename = $request->input('filename');
            $filePath = "bulletins/{$filename}.pdf";

            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé'
                ], 404);
            }

            $file = Storage::disk('public')->path($filePath);
            return response()->download($file, "{$filename}.pdf");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
