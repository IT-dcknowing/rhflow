<?php

namespace Modules\Employees\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\EmployeeDocument;
use Modules\Employees\Models\EmployeeCmu;
use Modules\Employees\Models\Family;
use Modules\Employees\Imports\EmployeesImport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Modules\Contracts\Models\Contract;
use Modules\Evenements\Models\Event;
use Modules\PaieSalaries\Models\PaySlip;
use Modules\Ruptures\Models\Rupture;
use App\Models\User;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\MaritalStatus;
use App\Models\Country;
use App\Models\PaiePeriode;
use App\Models\PaieExercice;
use App\Models\JobCategorie;
use App\Models\SecteurBatiment;
use App\Models\SecteurBatimentEmploye;
use App\Models\SecteurIndusAgent;
use App\Models\SecteurIndusCadre;
use App\Models\SecteurIndustrielEmploye;
use App\Models\SecteurIndustrielOuvrier;
use App\Models\SecteurIndusCauffeur;
use App\Models\SecteurAgriAutreChauffeur;
use App\Models\SecteurAgriAutreEmploye;
use App\Models\SecteurAgriAutreOuvrier;
use App\Models\SecteurAgriCrcChauffeur;
use App\Models\SecteurAgriCrcEmploye;
use App\Models\SecteurAgriCrcOuvrier;
use App\Models\SecteurAssurancesAgent;
use App\Models\SecteurAssurancesEmploye;
use App\Models\SecteurBanqueAgent;
use App\Models\SecteurBanqueEmploye;
use App\Models\SecteurBatimentAgent;
use App\Models\SecteurBatimentCadre;
use App\Models\SecteurBatimentChauffeur;
use App\Models\SecteurCommerceAgent;
use App\Models\SecteurCommerceCadre;
use App\Models\SecteurCommerceEmploye;
use App\Models\SecteurDockersEmploye;
use App\Models\SecteurElevageChauffeur;
use App\Models\SecteurElevageEmploye;
use App\Models\SecteurElevageOuvrier;
use App\Models\SecteurForestierChauffeur;
use App\Models\SecteurForestierEmploye;
use App\Models\SecteurForestierOuvrier;
use App\Models\SecteurHotelleri;
use App\Models\SecteurHotellerieMaitrise;
use App\Models\SecteurHotellerisCadre;
use App\Models\SecteurIndustrielAgriAgent;
use App\Models\SecteurIndustrielAgriCadre;
use App\Models\SecteurIndustrielAgriChauffeur;
use App\Models\SecteurIndustrielAgriEmploye;
use App\Models\SecteurIndustrielAgriOuvrier;
use App\Models\SecteurIndustrielBoisAgent;
use App\Models\SecteurIndustrielBoisCadre;
use App\Models\SecteurIndustrielBoisChauffeur;
use App\Models\SecteurIndustrielBoisEmploye;
use App\Models\SecteurIndustrielBoisOuvrier;
use App\Models\SecteurIndustrielPolyAgent;
use App\Models\SecteurIndustrielPolyCadre;
use App\Models\SecteurIndustrielPolyEmploye;
use App\Models\SecteurIndustrielPolyOuvrier;
use App\Models\SecteurIndustrielSucreAgent;
use App\Models\SecteurIndustrielSucreCadre;
use App\Models\SecteurIndustrielSucreChauffeur;
use App\Models\SecteurIndustrielSucreEmploye;
use App\Models\SecteurIndustrielSucreOuvrier;
use App\Models\SecteurIndustrielTextAgent;
use App\Models\SecteurIndustrielTextCadre;
use App\Models\SecteurIndustrielTextChauffeur;
use App\Models\SecteurIndustrielTextEmploye;
use App\Models\SecteurIndustrielTextOuvrier;
use App\Models\SecteurIndustrielThonAgent;
use App\Models\SecteurIndustrielThonCadre;
use App\Models\SecteurIndustrielThonChauffeur;
use App\Models\SecteurIndustrielThonEmploye;
use App\Models\SecteurIndustrielThonOuvrier;
use App\Models\SecteurMaisonEmploye;
use App\Models\SecteurMaritimeCapit;
use App\Models\SecteurMaritimeChefMeca;
use App\Models\SecteurMaritimeMachine;
use App\Models\SecteurMaritimeMaitre;
use App\Models\SecteurMaritimeMatelo;
use App\Models\SecteurMaritimePoly;
use App\Models\SecteurMaritimeSecondCapit;
use App\Models\SecteurMaritimeSecondMeca;
use App\Models\SecteurNettoyageChauffeur;
use App\Models\SecteurNettoyageEmploye;
use App\Models\SecteurNettoyageOuvrier;
use App\Models\SecteurPechesBosc;
use App\Models\SecteurPechesBrevet;
use App\Models\SecteurPechesCapit;
use App\Models\SecteurPechesChefMoteur;
use App\Models\SecteurPechesCotiereBosco;
use App\Models\SecteurPechesCotiereCapi;
use App\Models\SecteurPechesCotiereCapisCapa;
use App\Models\SecteurPechesCotiereEleve;
use App\Models\SecteurPechesCotiereMatlotSimpl;
use App\Models\SecteurPechesCotiereMatlot;
use App\Models\SecteurPechesCotiereMeca;
use App\Models\SecteurPechesCotiereNoviece;
use App\Models\SecteurPechesCotiereSecondBoco;
use App\Models\SecteurPechesEleve;
use App\Models\SecteurPechesGraisseur;
use App\Models\SecteurPechesLargesBoscoElec;
use App\Models\SecteurPechesLargesCuisto;
use App\Models\SecteurPechesLargesEleve;
use App\Models\SecteurPechesLargesGraisse;
use App\Models\SecteurPechesLargesMatlot;
use App\Models\SecteurPechesLargesMatlotsSimple;
use App\Models\SecteurPechesLargesNovice;
use App\Models\SecteurPechesLargesOffPont;
use App\Models\SecteurPechesLargesSecondBosco;
use App\Models\SecteurPechesMecani;
use App\Models\SecteurPechesNovice;
use App\Models\SecteurPechesOffPon;
use App\Models\SecteurPechesSbrevet;
use App\Models\SecteurPechesSceonBosco;
use App\Models\SecteurPechesSconCapit;
use App\Models\SecteurPechesSconMeca;
use App\Models\SecteurPetroDistAgent;
use App\Models\SecteurPetroDistCadre;
use App\Models\SecteurPetroDistChauffeur;
use App\Models\SecteurPetroDistEmploye;
use App\Models\SecteurPetroProdAgent;
use App\Models\SecteurPetroProdCadre;
use App\Models\SecteurPetroProdChauffeur;
use App\Models\SecteurPetroProdEmploye;
use App\Models\SecteurPetroProdOuvrier;
use App\Models\SecteurSecuriteChauffeur;
use App\Models\SecteurSecuriteEmploye;
use App\Models\SecteurTourism;
use App\Models\SecteurTourismsMatrise;
use App\Models\SecteurTourismsCadre;
use App\Models\SecteurTransportAgent;
use App\Models\SecteurTransportCadre;
use App\Models\SecteurTransportChauffeur;
use App\Models\SecteurTransportEmploye;
use App\Models\SecteurTransportOuvrier;
use App\Models\SecteurTrpsAerienAgent;
use App\Models\SecteurTrpsAerienCadre;
use App\Models\SecteurTrpsAerienCadresSup;
use App\Models\SecteurTrpsAerienOuvrier;
use App\Models\SecteurTrpsFondAgent;
use App\Models\SecteurTrpsFondCadre;
use App\Models\SecteurTrpsFondEmploye;

class EmployeesController extends Controller
{
    /**
     * Dashboard des employés
     */
    public function dashboard()
    {
        $companyId = auth()->user()->company_id;

        $stats = [
            'total_monthly' => Employee::where('company_id', $companyId)->where('is_active', 1)->count(),
            'total_employees' => Employee::where('company_id', $companyId)->where('is_active', 1)->count() 
        ];

        // Données pour les tableaux du dashboard
        $Employees = Employee::where('company_id', $companyId)
                                       ->where('is_active', 1)
                                       ->paginate(10);
        // Calcul de la masse salariale totale
        $totalSalary = Employee::where('company_id', $companyId)->where('is_active', 1)->sum('salary');

        // Données pour le graphique de répartition par secteur
        $sectorStats = Employee::where('employees.company_id', $companyId)
            ->where('employees.is_active', 1)
            ->join('departments', 'employees.department_id', '=', 'departments.id')
            ->selectRaw('departments.name as sector, COUNT(*) as count')
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Données pour le graphique de statut matrimonial
        $maritalStats = Employee::where('employees.company_id', $companyId)
            ->where('employees.is_active', 1)
            ->join('marital_statuses', 'employees.martalstatu_id', '=', 'marital_statuses.id')
            ->selectRaw('marital_statuses.name as status, COUNT(*) as count')
            ->groupBy('marital_statuses.id', 'marital_statuses.name')
            ->get();

        // Données pour le top départements
        $topDepartments = Employee::where('employees.company_id', $companyId)
            ->where('employees.is_active', 1)
            ->join('departments', 'employees.department_id', '=', 'departments.id')
            ->selectRaw('departments.name as department, COUNT(*) as employee_count')
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('employee_count')
            ->limit(5)
            ->get();

        return view('employees::dashboard', compact('stats', 'Employees', 'totalSalary', 'sectorStats', 'maritalStats', 'topDepartments'));
    }

    // ==============================================
    // GESTION DES MENSUELS 
    // ==============================================

    /**
     * Liste des employés (onglets Tous / Mensuels / Journaliers)
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $type = in_array($request->query('type'), ['mensuel', 'journalier']) ? $request->query('type') : 'tous';

        // salary_type n'existe que depuis l'ajout du choix Mensuel/Journalier a la
        // creation : les fiches anterieures sont a NULL et venaient toutes du
        // formulaire mensuel, on les rattache donc aux mensuels.
        $mensuels = function ($q) {
            return $q->where(function ($sub) {
                $sub->where('salary_type', 1)->orWhereNull('salary_type');
            });
        };
        $journaliers = function ($q) {
            return $q->where('salary_type', 2);
        };

        // Requête de base pour les employés de l'entreprise
        $filterBase = Employee::where('company_id', $companyId);

        // Filtre Statut (Actif par défaut si non spécifié)
        $status = $request->get('status', 'active');
        if ($status === 'active' || $status === '1') {
            $filterBase->where('is_active', 1);
        } elseif ($status === 'inactive' || $status === '0') {
            $filterBase->where('is_active', 0);
        }

        // Filtre Recherche par mot-clé
        if ($request->filled('search')) {
            $search = trim($request->search);
            $filterBase->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtre Succursale / Branche
        if ($request->filled('branch_id')) {
            $filterBase->where('branch_id', $request->branch_id);
        }

        // Filtre Département / Service
        if ($request->filled('department_id')) {
            $filterBase->where('department_id', $request->department_id);
        }

        // Filtre Type de contrat
        if ($request->filled('contract_type_id')) {
            $filterBase->whereHas('contracts', function ($q) use ($request) {
                $q->where('contract_type_id', $request->contract_type_id);
            });
        }

        // Revoir le nombre d'employés par type selon les filtres actifs
        $countTypes = [
            'tous' => (clone $filterBase)->count(),
            'mensuel' => $mensuels(clone $filterBase)->count(),
            'journalier' => $journaliers(clone $filterBase)->count(),
        ];

        // Application de l'onglet actif (Tous / Mensuels / Journaliers)
        $query = (clone $filterBase)->with(['branch', 'department', 'designation']);
        if ($type === 'mensuel') {
            $mensuels($query);
        } elseif ($type === 'journalier') {
            $journaliers($query);
        }

        $employees = $query->orderBy('name')->paginate(15)->withQueryString();

        $departments = Department::where('company_id', $companyId)->where('is_active', 1)->orderBy('name')->get();
        $designations = Designation::where('company_id', $companyId)->where('is_active', 1)->orderBy('name')->get();
        $branches = Branch::where('company_id', $companyId)->where('is_active', 1)->orderBy('name')->get();
        $contractTypes = \Modules\Contracts\Models\ContractType::where('company_id', $companyId)
            ->orWhere('type', 'default')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('employees::index', compact('employees', 'departments', 'designations', 'branches', 'contractTypes', 'type', 'countTypes', 'status'));
    }

    /**
     * Suggestions du champ « Adresse » : d'abord les adresses déjà saisies dans la société (les plus fréquentes),
     * puis les communes, quartiers et villes de Côte d'Ivoire (config/localites.php). Aucun service externe.
     */
    public function addressSuggestions(Request $request)
    {
        $saisie = trim((string) $request->query('q', ''));
        if (mb_strlen($saisie) < 2) {
            return response()->json([]);
        }

        // Comparaison sans accents, tirets ni majuscules : « port bouet » trouve « Port-Bouët »
        $normaliser = fn ($texte) => trim(preg_replace('/[^a-z0-9]+/', ' ', strtolower(\Illuminate\Support\Str::ascii((string) $texte))));
        $cherche = $normaliser($saisie);
        $commencePar = fn ($cle) => str_starts_with($cle, $cherche) || str_contains($cle, ' ' . $cherche);

        $suggestions = [];

        // 1. Adresses déjà utilisées pour les employés de la société
        $existantes = Employee::where('company_id', auth()->user()->company_id)
            ->whereNotNull('address')
            ->where('address', '!=', '')
            ->select('address', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
            ->groupBy('address')
            ->orderByDesc('total')
            ->limit(500)
            ->get();

        foreach ($existantes as $ligne) {
            $adresse = trim(preg_replace('/\s+/', ' ', $ligne->address));
            $cle = $normaliser($adresse);
            if ($cle !== '' && !isset($suggestions[$cle]) && str_contains($cle, $cherche)) {
                $suggestions[$cle] = ['texte' => $adresse, 'detail' => $ligne->total . ' employé(s)', 'source' => 'societe'];
            }
            if (count($suggestions) >= 5) {
                break;
            }
        }

        // 2. Localités de Côte d'Ivoire, celles qui commencent par la saisie en premier
        $localites = collect(config('localites.cote_ivoire', []))
            ->map(fn ($lieu) => ['texte' => $lieu, 'cle' => $normaliser($lieu)])
            ->filter(fn ($lieu) => !isset($suggestions[$lieu['cle']]) && str_contains($lieu['cle'], $cherche))
            ->sortBy(fn ($lieu) => ($commencePar($lieu['cle']) ? '0' : '1') . $lieu['cle']);

        foreach ($localites as $lieu) {
            $suggestions[$lieu['cle']] = ['texte' => $lieu['texte'], 'detail' => "Côte d'Ivoire", 'source' => 'localite'];
        }

        return response()->json(array_slice(array_values($suggestions), 0, 8));
    }

    /**
     * Créer un employé mensuel
     */
    public function create()
    {
        $companyId = auth()->user()->company_id;
        $company = Company::findOrFail($companyId);
        
        // Vérifier les paramètres nécessaires
        $branches = Branch::where('company_id', $companyId)->where('is_active', 1)->get();
        $departments = Department::where('company_id', $companyId)->where('is_active', 1)->get();
        $designations = Designation::where('company_id', $companyId)->where('is_active', 1)->get();
        
        // Vérifications et redirections si nécessaire
        $missingConfigs = [];
        
        if ($branches->isEmpty()) {
            $missingConfigs[] = [
                'type' => 'succursale',
                'message' => 'Aucune succursale/site de paie configuré',
                'route' => 'company.branches.create',
                'button_text' => 'Configurer une succursale'
            ];
        }
        
        if ($departments->isEmpty()) {
            $missingConfigs[] = [
                'type' => 'service',
                'message' => 'Aucun service configuré',
                'route' => 'company.departments.create',
                'button_text' => 'Configurer un service'
            ];
        }
        
        if ($designations->isEmpty()) {
            $missingConfigs[] = [
                'type' => 'poste',
                'message' => 'Aucun poste configuré',
                'route' => 'company.designations.create',
                'button_text' => 'Configurer un poste'
            ];
        }
        
        // Si des configurations manquent, afficher la page de vérification
        if (!empty($missingConfigs)) {
            return view('employees::config-check', compact('missingConfigs', 'company'));
        }
        
        // Générer un ID employé automatique
        $employeesId = Employee::generateUniqueId($companyId);

        // Récupérer les données nécessaires pour le formulaire
        $countries = Country::All();
        $maritalstatus = MaritalStatus::All();

        // Documents requis (simplifié - à adapter selon les besoins)
        $documents = [
            ['id' => 1, 'name' => 'Carte d\'identité', 'is_required' => 0],
            ['id' => 2, 'name' => 'Extrait de naissance', 'is_required' => 0],
            ['id' => 3, 'name' => 'Photo d\'identité', 'is_required' => 0],
            ['id' => 4, 'name' => 'Contrat de travail (signé)', 'is_required' => 0],
        ];

        // Récupérer les types de payslip (salaire)
        $payslipType = [
            1 => 'Mensuel',
            2 => 'Journalier',
            3 => 'Horaire'
        ];

        return view('employees::create', compact(
            'employeesId',
            'branches',
            'departments',
            'designations',
            'countries',
            'maritalstatus',
            'documents',
            'company',
            'payslipType'
        ));
    }

    /**
     * Enregistrer un employé mensuel
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        \Log::info($request->all());
        try {
            $companyId = auth()->user()->company_id;
            $company = Company::find($companyId);
            $total_employees = Employee::where('company_id', $companyId)->where('is_active', 1)->count();

            // Validation des données
            try {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'dob' => 'nullable|date',
                    'gender' => 'required|in:Male,Female',
                    'charge_expat' => 'required|in:local,expat',
                    'nationality' => 'nullable|string|max:250',
                    'cmu' => 'nullable|integer|min:0',
                    'martalstatu_id' => 'required|integer',
                    'enfant' => 'required|integer|min:0',
                    'personneinf' => 'required|integer|min:0',
                    'parts' => 'required|string|max:5',
                    'email' => 'nullable|email',
                    'phone' => 'nullable|string|max:20',
                    'address' => 'nullable|string|max:255',
                    'username' => 'required|string|unique:users,username',
                    'password' => 'nullable|string|min:8',
                    'employee_id' => [
                        'required',
                        'string',
                        Rule::unique('employees', 'employee_id')->where('company_id', $companyId)
                    ],
                    'branch_id' => 'required|integer|exists:branches,id',
                    'department_id' => 'required|integer|exists:departments,id',
                    'designation_id' => 'required|integer|exists:designations,id',
                    'salary_type' => 'required|in:1,2',
                    'category_job_id' => 'nullable|integer',
                    'category_id' => 'nullable|integer',
                    'salaire_minima_horaire' => 'nullable|integer|min:0',
                    'salaire_minima_mensuel' => 'nullable|integer|min:0',
                    'charge_cmu' => 'nullable|integer|min:0',
                    'num_secu_soc' => 'nullable|digits:13',
                    'charge_cnps' => 'nullable|integer|min:0',
                    'num_cnps' => 'nullable|digits:12',
                    'end_leave' => 'nullable|date',
                    'charge_its' => 'nullable|integer|min:0',
                    'account_holder_name' => 'nullable|string|max:255',
                    'account_number' => 'nullable|string|max:50',
                    'bank_name' => 'nullable|string|max:255',
                    'bank_identifier_code' => 'nullable|string|max:50',
                    'orange_money' => 'nullable|numeric',
                    'mtn_money' => 'nullable|numeric',
                    'moov_money' => 'nullable|numeric',
                    'wave_money' => 'nullable|numeric',
                ], [
                    'username.unique' => 'Le nom d\'utilisateur généré est déjà utilisé. Veuillez en générer un nouveau.',
                    'employee_id.unique' => 'L\'ID Employé est déjà attribué. Veuillez rafraîchir la page pour en générer un nouveau.',
                    'num_secu_soc.digits' => 'Le numéro CMU doit contenir exactement 13 chiffres.',
                    'num_cnps.digits' => 'Le numéro CNPS doit contenir exactement 12 chiffres.',
                    'salary_type.required' => "Veuillez indiquer si l'employé est mensuel ou journalier (onglet Données du poste).",
                    'salary_type.in' => "Le type d'employé doit être Mensuel ou Journalier.",
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                DB::rollBack();
                \Log::error('Erreur de validation : ', [
                    'errors' => $e->validator->errors()->toArray(),
                    'input' => $request->all()
                ]);
                
                // Préparer les données pour le retour avec les sélections
                $companyId = auth()->user()->company_id;
                $company = Company::findOrFail($companyId);
                $employeesId = Employee::generateUniqueId($companyId);
                
                // Récupérer les données nécessaires pour le formulaire
                $branches = Branch::where('company_id', $companyId)->where('is_active', 1)->get();
                $departments = Department::where('company_id', $companyId)->where('is_active', 1)->get();
                $designations = Designation::where('company_id', $companyId)->where('is_active', 1)->get();
                $countries = Country::All();
                $maritalstatus = MaritalStatus::All();
                
                $documents = [
                    ['id' => 1, 'name' => 'Carte d\'identité', 'is_required' => 0],
                    ['id' => 2, 'name' => 'Extrait de naissance', 'is_required' => 0],
                    ['id' => 3, 'name' => 'Photo d\'identité', 'is_required' => 0],
                    ['id' => 4, 'name' => 'Contrat de travail (signé)', 'is_required' => 0],
                ];
                
                $payslipType = [
                    1 => 'Mensuel',
                    2 => 'Journalier',
                    3 => 'Horaire'
                ];
                
                // Compter les erreurs par type pour un meilleur affichage
                $errorCount = $e->validator->errors()->count();
                $firstError = $e->validator->errors()->first();
                
                $request->flash();

                return view('employees::create', compact(
                    'employeesId',
                    'branches',
                    'departments',
                    'designations',
                    'countries',
                    'maritalstatus',
                    'documents',
                    'company',
                    'payslipType'
                ))
                ->withErrors($e->validator)
                ->with('error', "Veuillez corriger les {$errorCount} erreur(s) détectée(s). Premier problème : {$firstError}");
            }

            // Vérification du quota d'employés
            if ($total_employees >= $company->max_employees) {
                return redirect()
                    ->route('company.plan.pricing')
                    ->with('error', __('Votre limite d\'employés est atteinte, veuillez mettre à niveau votre abonnement.'));
            }

            // Création de l'utilisateur
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'type' => 'employee',
                'lang' => 'fr',
                'company_id' => $companyId,
                'email_verified_at' => now(),
                'created_by' => auth()->user()->id,
            ]);

            // Gestion des documents
            $documents = [];
            if ($request->hasFile('document')) {
                foreach ($request->file('document') as $key => $document) {
                    $documentName = time() . '_' . $document->getClientOriginalName();
                    $document->storeAs('documents/' . $user->id, $documentName, 'public');
                    $documents[] = [
                        'name' => $document->getClientOriginalName(),
                        'path' => 'documents/' . $user->id . '/' . $documentName,
                        'type' => $document->getClientMimeType(),
                        'size' => $document->getSize(),
                    ];
                }
            }

            // Création de l'employé
            $employee = Employee::create([
                'user_id' => $user->id,
                'company_id' => $companyId,
                'name' => $validated['name'],
                'dob' => $validated['dob'],
                'gender' => $validated['gender'],
                'nationality' => $validated['nationality'],
                'phone' => $validated['phone'],
                'martalstatu_id' => $validated['martalstatu_id'],
                'enfant' => $validated['enfant'],
                'personneinf' => $validated['personneinf'],
                'parts' => $validated['parts'],
                'cmu' => $validated['cmu'],
                'charge_expat' => $validated['charge_expat'],
                'num_cnps' => $validated['num_cnps'] ?? null,
                'num_secu_soc' => $validated['num_secu_soc'] ?? null,
                'categorie' => $validated['category_job_id'],
                'address' => $validated['address'] ?? null,
                'email' => $validated['email'],
                'employee_id' => $validated['employee_id'],
                'branch_id' => $validated['branch_id'],
                'department_id' => $validated['department_id'],
                'designation_id' => $validated['designation_id'],
                'end_leave' => $validated['end_leave'],
                'documents' => !empty($documents) ? json_encode($documents) : null,
                'account_holder_name' => $validated['account_holder_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'bank_identifier_code' => $validated['bank_identifier_code'] ?? null,
                'orange_money' => $validated['orange_money'] ?? null,
                'mtn_money' => $validated['mtn_money'] ?? null,
                'moov_money' => $validated['moov_money'] ?? null,
                'wave_money' => $validated['wave_money'] ?? null,
                'tax_payer_id' => 30,
                'sous_categorie' => $validated['category_id'] ?? null,
                'salary_type' => $validated['salary_type'],
                'salary_horaire' => !empty($validated['salaire_minima_horaire']) ? $validated['salaire_minima_horaire'] : round($validated['salaire_minima_mensuel'] / 173.33),    
                'salary' => $validated['salaire_minima_mensuel'],
                'charge_its' => $validated['charge_its'] ?? null,
                'charge_cnps' => $validated['charge_cnps'] ?? null,
                'charge_cmu' => $validated['charge_cmu'] ?? null,
                'is_active' => 1,
                'company_id' => $companyId,   
                'created_by' => auth()->user()->id,             
            ]);

            // Création des documents associés
            foreach ($documents as $doc) {
                EmployeeDocument::create([
                    'employee_id' => $employee->id,
                    'company_id' => $companyId,
                    'libelle' => $doc['name'], // Vous pouvez utiliser le nom du document ici
                    'document_value' => json_encode($doc), // Stockez chaque document individuellement
                ]);
            }

            // Création d'un événement d'anniversaire si la date de naissance est fournie
            if (!empty($validated['dob'])) {
                $birthday = date('Y-m-d', strtotime($validated['dob']));
                list($year, $month, $day) = explode('-', $birthday);
                
                Event::create([
                    'branch_id' => $validated['branch_id'],
                    'department_id' => $validated['department_id'],
                    'employee_id' => $user->id,
                    'title' => "Anniversaire " . $validated['name'],
                    'start_date' => date('Y') . '-' . $month . '-' . $day,
                    'end_date' => date('Y') . '-' . $month . '-' . $day,
                    'color' => 'event-success',
                    'event_type_id' => 3,
                    'description' => "Anniversaire de " . $validated['name'],
                    'status' => 'published',
                    'company_id' => $companyId,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('company.contracts.employee', $employee->id)
                ->with('success', 'Employé créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de l\'employé : ' . $e->getMessage());
            
            // Préparer les données pour le retour avec les sélections
            $companyId = auth()->user()->company_id;
            $company = Company::findOrFail($companyId);
            $employeesId = Employee::generateUniqueId($companyId);
            
            // Récupérer les données nécessaires pour le formulaire
            $branches = Branch::where('company_id', $companyId)->where('is_active', 1)->get();
            $departments = Department::where('company_id', $companyId)->where('is_active', 1)->get();
            $designations = Designation::where('company_id', $companyId)->where('is_active', 1)->get();
            $countries = Country::All();
            $maritalstatus = MaritalStatus::All();
            
            $documents = [
                ['id' => 1, 'name' => 'Carte d\'identité', 'is_required' => 0],
                ['id' => 2, 'name' => 'Extrait de naissance', 'is_required' => 0],
                ['id' => 3, 'name' => 'Photo d\'identité', 'is_required' => 0],
                ['id' => 4, 'name' => 'Contrat de travail (signé)', 'is_required' => 0],
            ];
            
            $payslipType = [
                1 => 'Mensuel',
                2 => 'Journalier',
                3 => 'Horaire'
            ];
            
            $request->flash();

            return view('employees::create', compact(
                'employeesId',
                'branches',
                'departments',
                'designations',
                'countries',
                'maritalstatus',
                'documents',
                'company',
                'payslipType'
            ))
            ->with('error', 'Une erreur technique est survenue. Veuillez réessayer. Si le problème persiste, contactez le support. Détail : ' . $e->getMessage());
        }
    }

    /**
     * Afficher un employé mensuel
     */
    public function show($id)
    {
        $employee = Employee::findOrFail($id);

        // Dernière periode de paie
        $periode = PaiePeriode::where('company_id', Auth::user()->company_id)->orderBy('id', 'desc')->first();
        
        // Récupérer les données pour les onglets
        $familyMembers = Family::where('employee_id', $employee->id)->get();
        
        // Événements liés à l'employé
        $events = Event::whereHas('employees', function($query) use ($employee) {
            $query->where('event_employees.employee_id', $employee->id);
        })->get();

        $ruptures = Rupture::where('company_id', Auth::user()->company_id)->where('employee_id', $employee->id)->get();
        
        // Documents de l'employé
        $documents = EmployeeDocument::where('employee_id', $employee->id)
            ->orWhere('employee_id', $employee->id)->get();
        
        // Bulletins de paie (module Declarations)
        $paySlips = \Modules\PaieSalaries\Models\PaySlip::where('employee_id', $employee->id)
            ->orderBy('salary_month', 'desc')
            ->limit(12)
            ->get();
        
        // « Voir détails » ouvre toujours la fiche de l'employé, même sans date d'embauche
        // (sans contrat) : la fiche propose alors d'associer un contrat.
        return view('employees::show', compact('employee', 'ruptures', 'periode', 'familyMembers', 'events', 'documents', 'paySlips'));
    }

    public function showBulletin($id)
    {
        $paySlip = PaySlip::with('employee')->findOrFail($id);
        $periode = PaiePeriode::findOrFail($paySlip->periode_id);
        $company = Company::findOrFail($paySlip->company_id);
        // Vérifier que l'utilisateur a le droit de voir ce bulletin
        if ($paySlip->company_id !== (Auth::user()->company_id ?? 1)) {
            abort(403, 'Accès non autorisé');
        }
        
        return view('employees::bulletins.show', compact('paySlip', 'periode', 'company'));
    }

    /**
     * Importer des employés depuis un fichier Excel
     */
    public function import(Request $request)
    {
        \Log::info('Import attempt - Mime Type: ' . ($request->hasFile('file') ? $request->file('file')->getMimeType() : 'No file'));
        \Log::info('Import attempt - Extension: ' . ($request->hasFile('file') ? $request->file('file')->getClientOriginalExtension() : 'No file'));

        $request->validate([
            'file' => [
                'required',
                'max:20480', // 20MB max
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    $mimeType  = $value->getMimeType();
                    $allowedExtensions = ['xlsx', 'xls', 'csv', 'txt', 'ods'];
                    $allowedMimes = [
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'application/octet-stream',
                        'application/vnd.oasis.opendocument.spreadsheet',
                    ];
                    if (!in_array($extension, $allowedExtensions) && !in_array($mimeType, $allowedMimes)) {
                        $fail('Le fichier doit être au format Excel (.xlsx, .xls), CSV (.csv) ou ODS (.ods). Format détecté : ' . $mimeType . ' (' . $extension . ')');
                    }
                },
            ],
        ]);

        try {
            $company  = Company::find(auth()->user()->company_id);
            $creator  = auth()->user();
            $importer = new EmployeesImport($company, $creator);

            Excel::import($importer, $request->file('file'));

            $importedCount = $importer->importedCount;
            $skippedCount  = $importer->skippedCount;
            $errors        = $importer->errors;

            if ($importedCount > 0 && empty($errors)) {
                return redirect()->back()->with('success', "{$importedCount} employé(s) importé(s) avec succès.");
            } elseif ($importedCount > 0 && !empty($errors)) {
                return redirect()->back()
                    ->with('success', "{$importedCount} employé(s) importé(s). {$skippedCount} ligne(s) ignorée(s).")
                    ->with('import_errors', $errors);
            } else {
                return redirect()->back()
                    ->with('error', 'Aucun employé importé. Vérifiez le format du fichier.')
                    ->with('import_errors', $errors);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur import Excel: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'importation: ' . $e->getMessage());
        }
    }

    /**
     * Télécharger le modèle Excel pour l'importation
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="modele_import_employes.csv"',
        ];

        $columns = [
            'Matricule ID Employe',
            'Nom Complet',
            'Email',
            'Nom dUtilisateur',
            'Mot de Passe',
            'Sexe',
            'Nationalite',
            'Telephone',
            'Situation Matrimoniale',
            'Nombre dEnfants',
            'Personnes infirmes a charge',
            'Nombre de Parts',
            'CMU',
            'Statut Expat local ou expat',
            'Num CNPS',
            'Num CMU',
            'Date de Naissance',
            'Adresse',
            'Succursale',
            'Service',
            'Poste',
            'Type de Categorie',
            'Categorie',
            'Retour du dernier conge',
            'Prise en charge CMU',
            'Prise en charge CNPS',
            'Prise en charge ITS',
            'Salaire de Base',
            'Nom du titulaire du compte',
            'Numero de compte',
            'Nom de la banque',
            'Adresse de domiciliation',
            'Orange Money',
            'MTN Money',
            'Moov Money',
            'Wave Money'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');
            
            // Ligne d'exemple
            fputcsv($file, [
                'EMP0001',
                'Jean Dupont',
                'jean.dupont@example.com',
                'j.dupont',
                'Password123!',
                'Homme',
                'Côte d\'Ivoire',
                '0102030405',
                'Célibataire',
                '0',
                '0',
                '1',
                '1', // CMU
                'local',
                '12345678',
                '87654321',
                '1990-01-01',
                'Abidjan, Cocody',
                'SIEGE',
                'RH',
                'Comptable',
                'Employé', // Type de catégorie
                'Catégorie 1', // Exemple de catégorie
                '', // Retour congé
                'Oui', // Prise en charge CMU
                'Oui', // Prise en charge CNPS
                'Oui', // Prise en charge ITS
                '500000',
                'Jean Dupont',
                'CI123 45678 901234567890 12',
                'NSIA BANQUE',
                'Plateau, Abidjan',
                '0707070707',
                '',
                '',
                ''
            ], ';');
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    /**
     * Afficher dossier l'employé
     */
    public function dossiers(Request $request){

        $companyId = auth()->user()->company_id;
        $search = trim((string) $request->input('search'));

        // Requête de base commune aux deux onglets (actifs / inactifs)
        $baseQuery = function () use ($companyId, $request, $search) {
            $query = Employee::with('designation')->where('company_id', $companyId);

            if (!empty($request->branch)) {
                $query->where('branch_id', $request->branch);
            }
            if (!empty($request->department)) {
                $query->where('department_id', $request->department);
            }
            if (!empty($request->designation)) {
                $query->where('designation_id', $request->designation);
            }

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('employee_id', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhereHas('designation', function ($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%");
                      });
                });
            }

            return $query->orderBy('name');
        };

        // Sans recherche ni filtre on ne montre qu'un apercu de 3 dossiers par
        // onglet : le reste se retrouve via la recherche. Des qu'un critere est
        // saisi, on repasse sur une pagination classique de 10.
        $filtreActif = $search !== ''
            || !empty($request->branch)
            || !empty($request->department)
            || !empty($request->designation);

        $parPage = $filtreActif ? 10 : 3;

        // Un paginateur distinct par onglet, pour que les deux listes se
        // paginent indépendamment l'une de l'autre.
        $employeesActifs = $baseQuery()->where('is_active', 1)
            ->paginate($parPage, ['*'], 'page_actif')
            ->withQueryString()
            ->fragment('actif');

        $employeesInactifs = $baseQuery()->where('is_active', 0)
            ->paginate($parPage, ['*'], 'page_inactif')
            ->withQueryString()
            ->fragment('inactif');

        // Ne charger que les documents des employés réellement affichés
        $employeeIds = collect($employeesActifs->items())
            ->merge($employeesInactifs->items())
            ->pluck('id')
            ->unique();

        $avatar = EmployeeDocument::where('company_id', $companyId)
            ->whereIn('employee_id', $employeeIds)
            ->get();

        $brances = Branch::where('company_id', $companyId)->get()->pluck('name', 'id');
        $brances->prepend('Toutes', '');

        $departments = Department::where('company_id', $companyId)->get()->pluck('name', 'id');
        $departments->prepend('Tous', '');

        $designations = Designation::where('company_id', $companyId)->get()->pluck('name', 'id');
        $designations->prepend('Tous', '');

        return view('employees::dossiers.index', compact(
            'employeesActifs', 'employeesInactifs', 'avatar', 'departments', 'designations', 'brances', 'search', 'filtreActif'
        ));
    }

    /**
     * Afficher profile l'employé
    */
    public function profile($id){
         return view('employees::profile', compact('id'));
    }

    /**
     * Modifier un employé mensuel
     */
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $companyId = auth()->user()->company_id;
        $company = Company::findOrFail($companyId);

        // Générer un ID employé automatique
        $employeesId = $company->employee_prefix . str_pad((Employee::where('company_id', $companyId)->max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        // Récupérer les données nécessaires pour le formulaire
        $branches = Branch::where('company_id', $companyId)->where('is_active', 1)->get();
        $departments = Department::where('company_id', $companyId)->where('is_active', 1)->get();
        $designations = Designation::where('company_id', $companyId)->where('is_active', 1)->get();
        $countries = Country::All();
        $maritalstatus = MaritalStatus::All();

        // Documents requis (simplifié - à adapter selon les besoins)
        $documents = [
            ['id' => 1, 'name' => 'Carte d\'identité', 'is_required' => 0],
            ['id' => 2, 'name' => 'Extrait de naissance', 'is_required' => 0],
            ['id' => 3, 'name' => 'Photo d\'identité', 'is_required' => 0],
            ['id' => 4, 'name' => 'Contrat de travail (signé)', 'is_required' => 0],
        ];
        
        // Listes de l'onglet « Poste » affichées déjà sélectionnées : chargées seulement par le script,
        // elles arrivaient vides, le formulaire redemandait service, poste et catégorie et le salaire était effacé.
        $servicesAgence = Department::where('branch_id', $employee->branch_id)
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->pluck('name', 'id');
        if ($employee->department_id && !$servicesAgence->has($employee->department_id) && ($service = Department::find($employee->department_id))) {
            $servicesAgence->put($service->id, $service->name);
        }

        $postesService = Designation::where('department_id', $employee->department_id)
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->pluck('name', 'id');
        if ($employee->designation_id && !$postesService->has($employee->designation_id) && ($poste = Designation::find($employee->designation_id))) {
            $postesService->put($poste->id, $poste->name);
        }

        // Type et catégorie pré-remplis : valeur enregistrée si elle existe dans la grille, sinon
        // ancienne fiche (catégorie enregistrée par son rang) ou salaire égal à un seul minimum de la grille.
        $grilleDuType = function ($type) {
            try {
                $donnees = $this->getCategoriesByType($type)->getData(true);
                return is_array($donnees) ? array_values($donnees) : [];
            } catch (\Throwable $e) {
                return [];
            }
        };

        $typeCategorieActuel = $employee->categorie;
        $categorieActuelle = $employee->sous_categorie;
        $categoriesType = $typeCategorieActuel ? $grilleDuType($typeCategorieActuel) : [];
        $salaireMensuel = (int) round((float) $employee->salary);

        if ($categoriesType && !in_array((string) $categorieActuelle, array_map('strval', array_column($categoriesType, 'id')), true)) {
            $parSalaire = array_values(array_filter($categoriesType, fn ($c) => (int) $c['salaire_minima_mensuel'] === $salaireMensuel));
            if (count($parSalaire) === 1) {
                $categorieActuelle = $parSalaire[0]['id'];
            } elseif (is_numeric($categorieActuelle) && isset($categoriesType[(int) $categorieActuelle])) {
                $categorieActuelle = $categoriesType[(int) $categorieActuelle]['id'];
            }
        } elseif (!$typeCategorieActuel && $salaireMensuel > 0) {
            $trouves = [];
            foreach (collect(optional($company->sector)->getJobCategorieAttribute() ?? []) as $type) {
                foreach ($grilleDuType($type->id) as $c) {
                    if ((int) $c['salaire_minima_mensuel'] === $salaireMensuel) {
                        $trouves[] = [$type->id, $c['id']];
                    }
                }
            }
            if (count($trouves) === 1) {
                [$typeCategorieActuel, $categorieActuelle] = $trouves[0];
                $categoriesType = $grilleDuType($typeCategorieActuel);
            }
        }

        // Salaires catégoriels : ceux de la fiche, sinon ceux de la catégorie retrouvée
        $categorieGrille = collect($categoriesType)->first(fn ($c) => (string) $c['id'] === (string) $categorieActuelle);
        $salaireHoraireAffiche = (float) $employee->salary_horaire > 0 ? (int) round($employee->salary_horaire) : ($categorieGrille['salaire_minima_horaire'] ?? '');
        $salaireMensuelAffiche = $salaireMensuel > 0 ? $salaireMensuel : ($categorieGrille['salaire_minima_mensuel'] ?? '');

        return view('employees::edit', compact('employee', 'branches', 'employeesId', 'departments', 'designations', 'countries', 'maritalstatus', 'documents', 'company', 'servicesAgence', 'postesService', 'categoriesType', 'typeCategorieActuel', 'categorieActuelle', 'salaireHoraireAffiche', 'salaireMensuelAffiche'));
    }

    /**
     * Mettre à jour un employé mensuel
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $employee = Employee::findOrFail($id);
            $companyId = auth()->user()->company_id;

            // Le nom d'utilisateur vit sur le compte (users) et ne change pas en modification :
            // il n'est exigé que si le compte n'en a pas encore.
            $compte = User::find($employee->user_id);
            $usernameExistant = $compte && !empty($compte->username);

            // Numéros CNPS / CMU déjà enregistrés et non modifiés (souvent « 0 » ou un ancien format) :
            // leur format n'est plus revérifié à chaque modification. Un « 0 » nouvellement saisi vaut « pas de numéro ».
            $numerosInchanges = [];
            foreach (['num_cnps', 'num_secu_soc'] as $champNumero) {
                $saisi = trim((string) $request->input($champNumero));
                if ($saisi !== '' && $saisi === trim((string) $employee->{$champNumero})) {
                    $numerosInchanges[$champNumero] = $employee->{$champNumero};
                    $request->merge([$champNumero => null]);
                } elseif ($saisi === '0') {
                    $request->merge([$champNumero => null]);
                }
            }

            // La catégorie est obligatoire, sauf pour un stagiaire (le formulaire masque alors ce champ)
            $typesCategorie = collect(optional(optional(Company::find($companyId))->sector)->getJobCategorieAttribute() ?? []);
            $typeChoisi = $typesCategorie->firstWhere('id', (int) $request->input('category_job_id'));
            $estStagiaire = $typeChoisi && strcasecmp(trim((string) $typeChoisi->title), 'Stagiaire') === 0;

            // Validation des données
            try {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'dob' => 'nullable|date',    
                    'gender' => 'required|in:Male,Female',
                    'charge_expat' => 'required|in:local,expat',
                    'nationality' => 'nullable|string|max:250',
                    'cmu' => 'nullable|integer|min:0',
                    'martalstatu_id' => 'required|integer',
                    'enfant' => 'required|integer|min:0',
                    'personneinf' => 'required|integer|min:0',
                    'parts' => 'required|string|max:5',
                    'email' => 'nullable|email',
                    'phone' => 'nullable|string|max:20',
                    'address' => 'nullable|string|max:255',
                    'username' => [
                        $usernameExistant ? 'nullable' : 'required',
                        'string',
                        Rule::unique('users', 'username')->ignore($employee->user_id),
                    ],
                    'password' => 'nullable|string|min:8',
                    'employee_id' => [
                        'required',
                        'string',
                        Rule::unique('employees', 'employee_id')
                            ->ignore($id)
                            ->where('company_id', $companyId)
                    ],
                    'branch_id' => 'required|integer|exists:branches,id',
                    'department_id' => 'required|integer|exists:departments,id',
                    'designation_id' => 'required|integer|exists:designations,id',
                    'category_job_id' => 'required|integer',
                    'category_id' => [$estStagiaire ? 'nullable' : 'required', 'integer'],
                    'salaire_minima_horaire' => 'nullable|integer|min:0',
                    'salaire_minima_mensuel' => 'nullable|integer|min:0',
                    'charge_cmu' => 'nullable|integer|min:0',
                    'num_secu_soc' => 'nullable|digits:13',
                    'end_leave' => 'nullable|date',
                    'charge_cnps' => 'nullable|integer|min:0',
                    'num_cnps' => 'nullable|digits:12',
                    'charge_its' => 'nullable|integer|min:0',
                    'account_holder_name' => 'nullable|string|max:255',
                    'account_number' => 'nullable|string|max:50',
                    'bank_name' => 'nullable|string|max:255',
                    'bank_identifier_code' => 'nullable|string|max:50',
                    'orange_money' => 'nullable|numeric',
                    'mtn_money' => 'nullable|numeric',
                    'moov_money' => 'nullable|numeric',
                    'wave_money' => 'nullable|numeric',
                ], [
                    'username.unique' => 'Le nom d\'utilisateur généré est déjà utilisé. Veuillez en générer un nouveau.',
                    'employee_id.unique' => 'L\'ID Employé est déjà attribué. Veuillez rafraîchir la page pour en générer un nouveau.',
                    'num_secu_soc.digits' => 'Le numéro CMU doit contenir exactement 13 chiffres.',
                    'num_cnps.digits' => 'Le numéro CNPS doit contenir exactement 12 chiffres.',
                    'category_job_id.required' => 'Veuillez sélectionner le type de catégorie (onglet Données du poste).',
                    'category_id.required' => 'Veuillez sélectionner la catégorie (onglet Données du poste).',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                DB::rollBack();
                \Log::error('Erreur de validation lors de la mise à jour : ', [
                    'errors' => $e->validator->errors()->toArray(),
                    'input' => $request->all()
                ]);
                
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($e->validator)
                    ->with('error', "Veuillez corriger les erreurs de validation.");
            }

            // Gestion des documents
            $documents = [];
            if ($request->hasFile('document')) {
                foreach ($request->file('document') as $key => $document) {
                    $documentName = time() . '_' . $document->getClientOriginalName();
                    $document->storeAs('documents/' . $employee->user_id, $documentName, 'public');
                    $documents[] = [
                        'name' => $document->getClientOriginalName(),
                        'path' => 'documents/' . $employee->user_id . '/' . $documentName,
                        'type' => $document->getClientMimeType(),
                        'size' => $document->getSize(),
                    ];
                }
            }

            // Numéros inchangés : on les remet tels qu'ils étaient enregistrés
            foreach ($numerosInchanges as $champNumero => $valeurEnregistree) {
                $validated[$champNumero] = $valeurEnregistree;
            }

            // Mise à jour de l'utilisateur
            $user = User::find($employee->user_id);
            if ($user) {
                // L'identifiant de connexion ne suit pas le nom : le modifier ferait
                // perdre a l'employe son acces (notamment cote mobile). On ne le pose
                // que s'il n'en a pas encore.
                $donnees = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ];

                if (empty($user->username)) {
                    $donnees['username'] = $validated['username'];
                }

                $user->update($donnees);

                // Mise à jour du mot de passe si fourni
                if (!empty($validated['password'])) {
                    $user->update([
                        'password' => Hash::make($validated['password']),
                    ]);
                }
            }

            // Mise à jour de l'employé
            $employee->update([
                'name' => $validated['name'],
                'dob' => $validated['dob'],
                'gender' => $validated['gender'],
                'nationality' => $validated['nationality'],
                'phone' => $validated['phone'],
                'martalstatu_id' => $validated['martalstatu_id'],
                'enfant' => $validated['enfant'],
                'personneinf' => $validated['personneinf'],
                'parts' => $validated['parts'],
                'cmu' => $validated['cmu'],
                'charge_expat' => $validated['charge_expat'],
                'num_cnps' => $validated['num_cnps'] ?? null,
                'num_secu_soc' => $validated['num_secu_soc'] ?? null,
                'categorie' => $validated['category_job_id'] ?? $employee->categorie,
                'address' => $validated['address'] ?? null,
                'email' => $validated['email'],
                'employee_id' => $validated['employee_id'],
                'branch_id' => $validated['branch_id'],
                'department_id' => $validated['department_id'],
                'designation_id' => $validated['designation_id'],
                'documents' => !empty($documents) ? json_encode($documents) : $employee->documents,
                'account_holder_name' => $validated['account_holder_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'bank_identifier_code' => $validated['bank_identifier_code'] ?? null,
                'orange_money' => $validated['orange_money'] ?? null,
                'mtn_money' => $validated['mtn_money'] ?? null,
                'moov_money' => $validated['moov_money'] ?? null,
                'wave_money' => $validated['wave_money'] ?? null,
                // Champ vide (catégorie non rechoisie) : on garde la valeur enregistrée au lieu de l'effacer
                'sous_categorie' => filled($validated['category_id'] ?? null) ? $validated['category_id'] : $employee->sous_categorie,
                'salary_horaire' => filled($validated['salaire_minima_horaire'] ?? null)
                    ? $validated['salaire_minima_horaire']
                    : (filled($validated['salaire_minima_mensuel'] ?? null) ? round($validated['salaire_minima_mensuel'] / 173.33) : $employee->salary_horaire),
                'salary' => filled($validated['salaire_minima_mensuel'] ?? null) ? $validated['salaire_minima_mensuel'] : $employee->salary,
                'charge_its' => $validated['charge_its'] ?? null,
                'charge_cnps' => $validated['charge_cnps'] ?? null,
                'charge_cmu' => $validated['charge_cmu'] ?? null,
                'end_leave' => $validated['end_leave'] ?? null,
            ]);

            // Création des nouveaux documents associés
            if (!empty($documents)) {
                foreach ($documents as $doc) {
                    EmployeeDocument::create([
                        'employee_id' => $employee->id,
                        'company_id' => $companyId,
                        'libelle' => $doc['name'],
                        'document_value' => json_encode($doc),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('company.employees.show', $employee->id)
                ->with('success', 'Employé mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour de l\'employé : ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'employé : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un employé mensuel
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update(['is_active' => 0]);

        return redirect()->route('company.employees.index')
                        ->with('success', 'Employé mensuel désactivé avec succès.');
    }

    /**
     * Activer/Désactiver un employé (Toggle)
     */
    public function toggle($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            
            // Vérifier que l'employé appartient à l'entreprise de l'utilisateur
            if ($employee->company_id != Auth::user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas la permission de modifier cet employé.'
                ], 403);
            }
            
            // Inverser le statut
            $newStatus = $employee->is_active == 1 ? 0 : 1;
            $employee->update(['is_active' => $newStatus]);
            
            $statusText = $newStatus == 1 ? 'activé' : 'désactivé';
            
            return response()->json([
                'success' => true,
                'message' => "Employé {$statusText} avec succès.",
                'status' => $newStatus
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les départements d'une branche (AJAX)
     */
    public function getDepartment(Request $request)
    {
        $branchId = $request->branch_id;

        // Récupérer les départements de cette branche pour l'entreprise
        $departments = Department::where('branch_id', $branchId)
                        ->where('company_id', auth()->user()->company_id)
                        ->where('is_active', 1)
                        ->pluck('name', 'id');

        return response()->json($departments);
    }

    /**
     * Récupérer les postes d'un département (AJAX)
     */
    public function getDesignations(Request $request)
    {
        $departmentId = $request->department_id;

        // Récupérer les postes de ce département pour l'entreprise
        $designations = Designation::where('department_id', $departmentId)
                        ->where('company_id', auth()->user()->company_id)
                        ->where('is_active', 1)
                        ->pluck('name', 'id');

        return response()->json($designations);
    }

    /**
     * Télécharger l'attestation de travail
     */
    public function downloadWorkCertificate($id)
    {
        $employee = Employee::findOrFail($id);
        
        // Vérifier que l'employé appartient à l'entreprise
        if ($employee->company_id != auth()->user()->company_id) {
            abort(403);
        }
        
        // Créer le contenu de l'attestation
        $content = $this->generateWorkCertificateContent($employee);
        
        // Générer le PDF
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($content);
        
        return $pdf->download('attestation_travail_' . $employee->name . '.pdf');
    }
    
    /**
     * Télécharger le certificat de travail
     */
    public function downloadWorkCertification($id)
    {
        $employee = Employee::findOrFail($id);
        
        // Vérifier que l'employé appartient à l'entreprise
        if ($employee->company_id != auth()->user()->company_id) {
            abort(403);
        }
        
        // Créer le contenu du certificat
        $content = $this->generateWorkCertificationContent($employee);
        
        // Générer le DOCX (simplifié - pour l'instant PDF)
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($content);
        
        return $pdf->download('certificat_travail_' . $employee->name . '.pdf');
    }
    
    /**
     * Générer le contenu de l'attestation de travail
     */
    private function generateWorkCertificateContent($employee)
    {
        $company = \App\Models\Company::find($employee->company_id);
        $currentDate = now()->format('d/m/Y');
        
        return "
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .header { text-align: center; margin-bottom: 40px; }
            .title { font-size: 20px; font-weight: bold; margin-bottom: 30px; }
            .content { line-height: 1.6; }
            .footer { margin-top: 60px; text-align: right; }
            .signature { margin-top: 80px; }
        </style>
        
        <div class='header'>
            <h2>ATTESTATION DE TRAVAIL</h2>
        </div>
        
        <div class='content'>
            <p>Je soussigné(e), représentant(e) de la société " . $company->name . ", certifie que :</p>
            
            <p><strong>M./Mme " . $employee->name . "</strong>, né(e) le " . ($employee->dob ? $employee->dob->format('d/m/Y') : 'N/A') . "
            à " . ($employee->nationality ?? 'N/A') . ", est employé(e) dans notre entreprise en qualité de 
            <strong>" . ($employee->designation->name ?? 'N/A') . "</strong> au sein du département 
            <strong>" . ($employee->department->name ?? 'N/A') . "</strong>.</p>
            
            <p>Date d'embauche : <strong>" . ($employee->start_date ? $employee->start_date->format('d/m/Y') : 'N/A') . "</strong></p>
            
            <p>Salaire mensuel : <strong>" . number_format($employee->salary, 0, ',', ' ') . " FCFA</strong></p>
            
            <p>Cette attestation est délivrée pour servir et valoir ce que de droit.</p>
        </div>
        
        <div class='footer'>
            <p>Fait à " . ($company->city ?? 'Lieu') . ", le " . $currentDate . "</p>
            <div class='signature'>
                <p>La Direction</p>
                <p>" . $company->name . "</p>
            </div>
        </div>";
    }
    
    /**
     * Générer le contenu du certificat de travail
     */
    private function generateWorkCertificationContent($employee)
    {
        $company = \App\Models\Company::find($employee->company_id);
        $currentDate = now()->format('d/m/Y');
        
        return "
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .header { text-align: center; margin-bottom: 40px; }
            .title { font-size: 20px; font-weight: bold; margin-bottom: 30px; }
            .content { line-height: 1.6; }
            .footer { margin-top: 60px; text-align: right; }
            .signature { margin-top: 80px; }
        </style>
        
        <div class='header'>
            <h2>CERTIFICAT DE TRAVAIL</h2>
        </div>
        
        <div class='content'>
            <p>Je soussigné(e), représentant(e) de la société " . $company->name . ", certifie que :</p>
            
            <p><strong>M./Mme " . $employee->name . "</strong> a travaillé dans notre entreprise 
            du " . ($employee->start_date ? $employee->start_date->format('d/m/Y') : 'N/A') . "
            " . ($employee->end_date ? 'au ' . $employee->end_date->format('d/m/Y') : 'à ce jour') . ".</p>
            
            <p>Poste occupé : <strong>" . ($employee->designation->name ?? 'N/A') . "</strong></p>
            <p>Département : <strong>" . ($employee->department->name ?? 'N/A') . "</strong></p>
            <p>Branche : <strong>" . ($employee->branch->name ?? 'N/A') . "</strong></p>
            
            <p>Lors de son départ, l'intéressé(e) était en règle vis-à-vis de l'entreprise 
            et a rempli toutes ses obligations contractuelles.</p>
            
            <p>Ce certificat est délivré pour servir et valoir ce que de droit.</p>
        </div>
        
        <div class='footer'>
            <p>Fait à " . ($company->city ?? 'Lieu') . ", le " . $currentDate . "</p>
            <div class='signature'>
                <p>La Direction</p>
                <p>" . $company->name . "</p>
            </div>
        </div>";
    }

    /**
     * Récupérer les catégories par type (AJAX)
     */
    public function getCategoriesByType($typeId)
    {
        $secteursbat = '';
        if($typeId == 1){
            $secteursbat = SecteurIndustrielOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 2){
            $secteursbat = SecteurIndustrielEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 3){
            $secteursbat = SecteurIndusAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 4){
            $secteursbat = SecteurIndusCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 5){
            $secteursbat = SecteurIndusCauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 6){
            $secteursbat = SecteurSecuriteEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 7){
            $secteursbat = SecteurSecuriteChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 8){
            $secteursbat = SecteurSecuriteEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 9){
            $secteursbat = SecteurSecuriteChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 10){
            $secteursbat = SecteurTrpsFondEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 11){
            $secteursbat = SecteurTrpsFondAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 12){
            $secteursbat = SecteurTrpsFondCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 13){
            $secteursbat = SecteurIndustrielBoisOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 14){
            $secteursbat = SecteurIndustrielBoisEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 15){
            $secteursbat = SecteurIndustrielBoisAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 16){
            $secteursbat = SecteurIndustrielBoisCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 17){
            $secteursbat = SecteurIndustrielBoisChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 18){
            $secteursbat = SecteurIndustrielTextOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 19){
            $secteursbat = SecteurIndustrielTextEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 20){
            $secteursbat = SecteurIndustrielTextAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 21){
            $secteursbat = SecteurIndustrielTextCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 22){
            $secteursbat = SecteurIndustrielTextChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 23){
            $secteursbat = SecteurIndustrielAgriOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 24){
            $secteursbat = SecteurIndustrielAgriEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 25){
            $secteursbat = SecteurIndustrielAgriAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 26){
            $secteursbat = SecteurIndustrielAgriCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 27){
            $secteursbat = SecteurIndustrielAgriChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 28){
            $secteursbat = SecteurIndustrielSucreOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 29){
            $secteursbat = SecteurIndustrielSucreEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 30){
            $secteursbat = SecteurIndustrielSucreAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 31){
            $secteursbat = SecteurIndustrielSucreCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 32){
            $secteursbat = SecteurIndustrielSucreChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 33){
            $secteursbat = SecteurHotelleri::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 137){
            $secteursbat = SecteurHotellerieMaitrise::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 138){
            $secteursbat = SecteurHotellerisCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 34){
            $secteursbat = SecteurBatiment::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 35){
            $secteursbat = SecteurBatimentEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 36){
            $secteursbat = SecteurBatimentChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 37){
            $secteursbat = SecteurBatimentAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 38){
            $secteursbat = SecteurBatimentCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 39){
            $secteursbat = SecteurDockersEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 136){
            $secteursbat = SecteurCommerceAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 41){
            $secteursbat = SecteurCommerceCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 42){
            $secteursbat = SecteurAgriCrcOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 43){
            $secteursbat = SecteurAgriCrcEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 46){
            $secteursbat = SecteurAgriCrcChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 47){
            $secteursbat = SecteurAgriAutreOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 48){
            $secteursbat = SecteurAgriAutreEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 139){
            $secteursbat = SecteurTourism::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 140){
            $secteursbat = SecteurTourismsCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 141){
            $secteursbat = SecteurTourismsMatrise::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 51){
            $secteursbat = SecteurAgriAutreChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 52){
            $secteursbat = SecteurElevageOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 53){
            $secteursbat = SecteurElevageChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 54){
            $secteursbat = SecteurElevageEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 55){
            $secteursbat = SecteurForestierOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 56){
            $secteursbat = SecteurForestierChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 57){
            $secteursbat = SecteurForestierEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 58){
            $secteursbat = SecteurBanqueEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 59){
            $secteursbat = SecteurBanqueAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 60){
            $secteursbat = SecteurAssurancesEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 61){
            $secteursbat = SecteurAssurancesAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 62){
            $secteursbat = SecteurPetroProdEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 63){
            $secteursbat = SecteurPetroProdChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 64){
            $secteursbat = SecteurPetroProdAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 65){
            $secteursbat = SecteurPetroProdCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 66){
            $secteursbat = SecteurPetroDistAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 67){
            $secteursbat = SecteurPetroDistCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 68){
            $secteursbat = SecteurPetroDistChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 69){
            $secteursbat = SecteurPetroDistEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 70){
            $secteursbat = SecteurMaritimePoly::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 71){
            $secteursbat = SecteurMaritimeMatelo::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 72){
            $secteursbat = SecteurMaritimeMaitre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 73){
            $secteursbat = SecteurMaritimeMachine::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 74){
            $secteursbat = SecteurMaritimeChefMeca::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 75){
            $secteursbat = SecteurMaritimeSecondMeca::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 76){
            $secteursbat = SecteurMaritimeCapit::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 77){
            $secteursbat = SecteurMaritimeSecondCapit::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 78){
            $secteursbat = SecteurPechesNovice::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 79){
            $secteursbat = SecteurPechesEleve::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 80){
            $secteursbat = SecteurPechesSbrevet::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 81){
            $secteursbat = SecteurPechesBrevet::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 82){
            $secteursbat = SecteurPechesBosc::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 83){
            $secteursbat = SecteurPechesChefMoteur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 84){
            $secteursbat = SecteurPechesSceonBosco::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 85){
            $secteursbat = SecteurPechesGraisseur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 86){
            $secteursbat = SecteurPechesCapit::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 87){
            $secteursbat = SecteurPechesSconCapit::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 88){
            $secteursbat = SecteurPechesSconMeca::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 89){
            $secteursbat = SecteurPechesOffPon::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 90){
            $secteursbat = SecteurPechesLargesNovice::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 91){
            $secteursbat = SecteurPechesLargesEleve::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 92){
            $secteursbat = SecteurPechesLargesCuisto::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 93){
            $secteursbat = SecteurPechesLargesMatlot::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 94){
            $secteursbat = SecteurPechesLargesMatlotsSimple::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 95){
            $secteursbat = SecteurPechesLargesOffPont::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 96){
            $secteursbat = SecteurPechesLargesBoscoElec::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 97){
            $secteursbat = SecteurPechesLargesSecondBosco::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 98){
            $secteursbat = SecteurPechesCotiereNoviece::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 99){
            $secteursbat = SecteurPechesCotiereEleve::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 100){
            $secteursbat = SecteurPechesCotiereMatlotSimpl::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 101){
            $secteursbat = SecteurPechesCotiereMatlot::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 102){
            $secteursbat = SecteurPechesCotiereCapi::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 103){
            $secteursbat = SecteurPechesCotiereCapisCapa::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 104){
            $secteursbat = SecteurPechesCotiereMeca::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 105){
            $secteursbat = SecteurPechesCotiereBosco::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 106){
            $secteursbat = SecteurPechesCotiereSecondBoco::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 107){
            $secteursbat = SecteurTransportOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 108){
            $secteursbat = SecteurTransportEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 109){
            $secteursbat = SecteurTransportAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 110){
            $secteursbat = SecteurTransportCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 111){
            $secteursbat = SecteurTrpsAerienOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 112){
            $secteursbat = SecteurTrpsAerienAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 113){
            $secteursbat = SecteurTrpsAerienCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 114){
            $secteursbat = SecteurTrpsAerienCadresSup::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 115){
            $secteursbat = SecteurMaisonEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 116){
            $secteursbat = SecteurNettoyageOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 117){
            $secteursbat = SecteurNettoyageChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 118){
            $secteursbat = SecteurNettoyageEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 119){
            $secteursbat = SecteurIndustrielThonOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 120){
            $secteursbat = SecteurIndustrielThonEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 121){
            $secteursbat = SecteurIndustrielThonAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 122){
            $secteursbat = SecteurIndustrielThonCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 123){
            $secteursbat = SecteurIndustrielThonChauffeur::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 124){
            $secteursbat = SecteurIndustrielPolyOuvrier::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 125){
            $secteursbat = SecteurIndustrielPolyEmploye::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 126){
            $secteursbat = SecteurIndustrielPolyCadre::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 127){
            $secteursbat = SecteurIndustrielPolyAgent::select('*')->where('type_poste', $typeId)->get();
        }elseif($typeId == 40){
            $secteursbat = SecteurCommerceEmploye::select('*')->where('type_poste', $typeId)->get();
        }
        // Retourne les catégories au format JSON
        return response()->json($secteursbat);
    }

    public function getCategoryDetails($typeId, $categoryId)
    {
        // Initialiser les variables pour stocker les valeurs de salaire
        $salaire_minima_horaire = null;
        $salaire_minima_mensuel = null;

        // Déterminer la classe à partir du type_poste
        if($typeId == 1){
            $category = SecteurIndustrielOuvrier::findOrFail($categoryId);
        }elseif($typeId == 2){
            $category = SecteurIndustrielEmploye::findOrFail($categoryId);
        }elseif($typeId == 3){
            $category = SecteurIndusAgent::findOrFail($categoryId);
        }elseif($typeId == 4){
            $category = SecteurIndusCadre::findOrFail($categoryId);
        }elseif($typeId == 5){
            $category = SecteurIndusCauffeur::findOrFail($categoryId);
        }elseif($typeId == 6){
            $category = SecteurSecuriteEmploye::findOrFail($categoryId);
        }elseif($typeId == 7){
            $category = SecteurSecuriteChauffeur::findOrFail($categoryId);
        }elseif($typeId == 8){
            $category = SecteurSecuriteEmploye::findOrFail($categoryId);
        }elseif($typeId == 9){
            $category = SecteurSecuriteChauffeur::findOrFail($categoryId);
        }elseif($typeId == 10){
            $category = SecteurTrpsFondEmploye::findOrFail($categoryId);
        }elseif($typeId == 11){
            $category = SecteurTrpsFondAgent::findOrFail($categoryId);
        }elseif($typeId == 12){
            $category = SecteurTrpsFondCadre::findOrFail($categoryId);
        }elseif($typeId == 13){
            $category = SecteurIndustrielBoisOuvrier::findOrFail($categoryId);
        }elseif($typeId == 14){
            $category = SecteurIndustrielBoisEmploye::findOrFail($categoryId);
        }elseif($typeId == 15){
            $category = SecteurIndustrielBoisAgent::findOrFail($categoryId);
        }elseif($typeId == 16){
            $category = SecteurIndustrielBoisCadre::findOrFail($categoryId);
        }elseif($typeId == 17){
            $category = SecteurIndustrielBoisChauffeur::findOrFail($categoryId);
        }elseif($typeId == 18){
            $category = SecteurIndustrielTextOuvrier::findOrFail($categoryId);
        }elseif($typeId == 19){
            $category = SecteurIndustrielTextEmploye::findOrFail($categoryId);
        }elseif($typeId == 20){
            $category = SecteurIndustrielTextAgent::findOrFail($categoryId);
        }elseif($typeId == 21){
            $category = SecteurIndustrielTextCadre::findOrFail($categoryId);
        }elseif($typeId == 22){
            $category = SecteurIndustrielTextChauffeur::findOrFail($categoryId);
        }elseif($typeId == 23){
            $category = SecteurIndustrielAgriOuvrier::findOrFail($categoryId);
        }elseif($typeId == 24){
            $category = SecteurIndustrielAgriEmploye::findOrFail($categoryId);
        }elseif($typeId == 25){
            $category = SecteurIndustrielAgriAgent::findOrFail($categoryId);
        }elseif($typeId == 26){
            $category = SecteurIndustrielAgriCadre::findOrFail($categoryId);
        }elseif($typeId == 27){
            $category = SecteurIndustrielAgriChauffeur::findOrFail($categoryId);
        }elseif($typeId == 28){
            $category = SecteurIndustrielSucreOuvrier::findOrFail($categoryId);
        }elseif($typeId == 29){
            $category = SecteurIndustrielSucreEmploye::findOrFail($categoryId);
        }elseif($typeId == 30){
            $category = SecteurIndustrielSucreAgent::findOrFail($categoryId);
        }elseif($typeId == 31){
            $category = SecteurIndustrielSucreCadre::findOrFail($categoryId);
        }elseif($typeId == 32){
            $category = SecteurIndustrielSucreChauffeur::findOrFail($categoryId);
        }elseif($typeId == 33){
            $category = SecteurHotelleri::findOrFail($categoryId);
        }elseif($typeId == 137){
            $category = SecteurHotellerieMaitrise::findOrFail($categoryId);
        }elseif($typeId == 138){
            $category = SecteurHotellerisCadre::findOrFail($categoryId);
        }elseif($typeId == 34){
            $category = SecteurBatiment::findOrFail($categoryId);
        }elseif($typeId == 35){
            $category = SecteurBatimentEmploye::findOrFail($categoryId);
        }elseif($typeId == 36){
            $category = SecteurBatimentChauffeur::findOrFail($categoryId);
        }elseif($typeId == 37){
            $category = SecteurBatimentAgent::findOrFail($categoryId);
        }elseif($typeId == 38){
            $category = SecteurBatimentCadre::findOrFail($categoryId);
        }elseif($typeId == 39){
            $category = SecteurDockersEmploye::findOrFail($categoryId);
        }elseif($typeId == 136){
            $category = SecteurCommerceAgent::findOrFail($categoryId);
        }elseif($typeId == 41){
            $category = SecteurCommerceCadre::findOrFail($categoryId);
        }elseif($typeId == 42){
            $category = SecteurAgriCrcOuvrier::findOrFail($categoryId);
        }elseif($typeId == 43){
            $category = SecteurAgriCrcEmploye::findOrFail($categoryId);
        }elseif($typeId == 46){
            $category = SecteurAgriCrcChauffeur::findOrFail($categoryId);
        }elseif($typeId == 47){
            $category = SecteurAgriAutreOuvrier::findOrFail($categoryId);
        }elseif($typeId == 48){
            $category = SecteurAgriAutreEmploye::findOrFail($categoryId);
        }elseif($typeId == 51){
            $category = SecteurAgriAutreChauffeur::findOrFail($categoryId);
        }elseif($typeId == 52){
            $category = SecteurElevageOuvrier::findOrFail($categoryId);
        }elseif($typeId == 53){
            $category = SecteurElevageChauffeur::findOrFail($categoryId);
        }elseif($typeId == 54){
            $category = SecteurElevageEmploye::findOrFail($categoryId);
        }elseif($typeId == 55){
            $category = SecteurForestierOuvrier::findOrFail($categoryId);
        }elseif($typeId == 56){
            $category = SecteurForestierChauffeur::findOrFail($categoryId);
        }elseif($typeId == 57){
            $category = SecteurForestierEmploye::findOrFail($categoryId);
        }elseif($typeId == 58){
            $category = SecteurBanqueEmploye::findOrFail($categoryId);
        }elseif($typeId == 59){
            $category = SecteurBanqueAgent::findOrFail($categoryId);
        }elseif($typeId == 60){
            $category = SecteurAssurancesEmploye::findOrFail($categoryId);
        }elseif($typeId == 61){
            $category = SecteurAssurancesAgent::findOrFail($categoryId);
        }elseif($typeId == 62){
            $category = SecteurPetroProdEmploye::findOrFail($categoryId);
        }elseif($typeId == 63){
            $category = SecteurPetroProdChauffeur::findOrFail($categoryId);
        }elseif($typeId == 64){
            $category = SecteurPetroProdAgent::findOrFail($categoryId);
        }elseif($typeId == 65){
            $category = SecteurPetroProdCadre::findOrFail($categoryId);
        }elseif($typeId == 66){
            $category = SecteurPetroDistAgent::findOrFail($categoryId);
        }elseif($typeId == 67){
            $category = SecteurPetroDistCadre::findOrFail($categoryId);
        }elseif($typeId == 68){
            $category = SecteurPetroDistChauffeur::findOrFail($categoryId);
        }elseif($typeId == 69){
            $category = SecteurPetroDistEmploye::findOrFail($categoryId);
        }elseif($typeId == 70){
            $category = SecteurMaritimePoly::findOrFail($categoryId);
        }elseif($typeId == 71){
            $category = SecteurMaritimeMatelo::findOrFail($categoryId);
        }elseif($typeId == 72){
            $category = SecteurMaritimeMaitre::findOrFail($categoryId);
        }elseif($typeId == 73){
            $category = SecteurMaritimeMachine::findOrFail($categoryId);
        }elseif($typeId == 74){
            $category = SecteurMaritimeChefMeca::findOrFail($categoryId);
        }elseif($typeId == 75){
            $category = SecteurMaritimeSecondMeca::findOrFail($categoryId);
        }elseif($typeId == 76){
            $category = SecteurMaritimeCapit::findOrFail($categoryId);
        }elseif($typeId == 77){
            $category = SecteurMaritimeSecondCapit::findOrFail($categoryId);
        }elseif($typeId == 78){
            $category = SecteurPechesNovice::findOrFail($categoryId);
        }elseif($typeId == 79){
            $category = SecteurPechesEleve::findOrFail($categoryId);
        }elseif($typeId == 80){
            $category = SecteurPechesSbrevet::findOrFail($categoryId);
        }elseif($typeId == 81){
            $category = SecteurPechesBrevet::findOrFail($categoryId);
        }elseif($typeId == 82){
            $category = SecteurPechesBosc::findOrFail($categoryId);
        }elseif($typeId == 83){
            $category = SecteurPechesChefMoteur::findOrFail($categoryId);
        }elseif($typeId == 84){
            $category = SecteurPechesSceonBosco::findOrFail($categoryId);
        }elseif($typeId == 85){
            $category = SecteurPechesGraisseur::findOrFail($categoryId);
        }elseif($typeId == 86){
            $category = SecteurPechesCapit::findOrFail($categoryId);
        }elseif($typeId == 87){
            $category = SecteurPechesSconCapit::findOrFail($categoryId);
        }elseif($typeId == 88){
            $category = SecteurPechesSconMeca::findOrFail($categoryId);
        }elseif($typeId == 89){
            $category = SecteurPechesOffPon::findOrFail($categoryId);
        }elseif($typeId == 90){
            $category = SecteurPechesLargesNovice::findOrFail($categoryId);
        }elseif($typeId == 91){
            $category = SecteurPechesLargesEleve::findOrFail($categoryId);
        }elseif($typeId == 92){
            $category = SecteurPechesLargesCuisto::findOrFail($categoryId);
        }elseif($typeId == 93){
            $category = SecteurPechesLargesMatlot::findOrFail($categoryId);
        }elseif($typeId == 94){
            $category = SecteurPechesLargesMatlotsSimple::findOrFail($categoryId);
        }elseif($typeId == 95){
            $category = SecteurPechesLargesOffPont::findOrFail($categoryId);
        }elseif($typeId == 96){
            $category = SecteurPechesLargesBoscoElec::findOrFail($categoryId);
        }elseif($typeId == 97){
            $category = SecteurPechesLargesSecondBosco::findOrFail($categoryId);
        }elseif($typeId == 98){
            $category = SecteurPechesCotiereNoviece::findOrFail($categoryId);
        }elseif($typeId == 99){
            $category = SecteurPechesCotiereEleve::findOrFail($categoryId);
        }elseif($typeId == 100){
            $category = SecteurPechesCotiereMatlotSimpl::findOrFail($categoryId);
        }elseif($typeId == 101){
            $category = SecteurPechesCotiereMatlot::findOrFail($categoryId);
        }elseif($typeId == 102){
            $category = SecteurPechesCotiereCapi::findOrFail($categoryId);
        }elseif($typeId == 103){
            $category = SecteurPechesCotiereCapisCapa::findOrFail($categoryId);
        }elseif($typeId == 104){
            $category = SecteurPechesCotiereMeca::findOrFail($categoryId);
        }elseif($typeId == 105){
            $category = SecteurPechesCotiereBosco::findOrFail($categoryId);
        }elseif($typeId == 106){
            $category = SecteurPechesCotiereSecondBoco::findOrFail($categoryId);
        }elseif($typeId == 107){
            $category = SecteurTransportOuvrier::findOrFail($categoryId);
        }elseif($typeId == 108){
            $category = SecteurTransportEmploye::findOrFail($categoryId);
        }elseif($typeId == 109){
            $category = SecteurTransportAgent::findOrFail($categoryId);
        }elseif($typeId == 110){
            $category = SecteurTransportCadre::findOrFail($categoryId);
        }elseif($typeId == 111){
            $category = SecteurTrpsAerienOuvrier::findOrFail($categoryId);
        }elseif($typeId == 112){
            $category = SecteurTrpsAerienAgent::findOrFail($categoryId);
        }elseif($typeId == 113){
            $category = SecteurTrpsAerienCadre::findOrFail($categoryId);
        }elseif($typeId == 114){
            $category = SecteurTrpsAerienCadresSup::findOrFail($categoryId);
        }elseif($typeId == 115){
            $category = SecteurMaisonEmploye::findOrFail($categoryId);
        }elseif($typeId == 116){
            $category = SecteurNettoyageOuvrier::findOrFail($categoryId);
        }elseif($typeId == 117){
            $category = SecteurNettoyageChauffeur::findOrFail($categoryId);
        }elseif($typeId == 118){
            $category = SecteurNettoyageEmploye::findOrFail($categoryId);
        }elseif($typeId == 119){
            $category = SecteurIndustrielThonOuvrier::findOrFail($categoryId);
        }elseif($typeId == 120){
            $category = SecteurIndustrielThonEmploye::findOrFail($categoryId);
        }elseif($typeId == 121){
            $category = SecteurIndustrielThonAgent::findOrFail($categoryId);
        }elseif($typeId == 122){
            $category = SecteurIndustrielThonCadre::findOrFail($categoryId);
        }elseif($typeId == 123){
            $category = SecteurIndustrielThonChauffeur::findOrFail($categoryId);
        }elseif($typeId == 124){
            $category = SecteurIndustrielPolyOuvrier::findOrFail($categoryId);
        }elseif($typeId == 125){
            $category = SecteurIndustrielPolyEmploye::findOrFail($categoryId);
        }elseif($typeId == 126){
            $category = SecteurIndustrielPolyCadre::findOrFail($categoryId);
        }elseif($typeId == 127){
            $category = SecteurIndustrielPolyAgent::findOrFail($categoryId);
        }elseif($typeId == 40){
            $category = SecteurCommerceEmploye::findOrFail($categoryId);
        }elseif($typeId == 139){
            $category = SecteurTourism::findOrFail($categoryId);
        }elseif($typeId == 140){
            $category = SecteurTourismsCadre::findOrFail($categoryId);
        }elseif($typeId == 141){
            $category = SecteurTourismsMatrise::findOrFail($categoryId);
        }
        // Récupérer les valeurs de salaire appropriées
        $salaire_minima_horaire = $category->salaire_minima_horaire;
        $salaire_minima_mensuel = $category->salaire_minima_mensuel;

        // Retourner les détails de la catégorie au format JSON
        return response()->json([
            'salaire_minima_horaire' => $salaire_minima_horaire,
            'salaire_minima_mensuel' => $salaire_minima_mensuel,
        ]);
    }

    /**
     * Stocker un nouveau membre de famille
     */
    public function storeFamily(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'prenoms' => 'required|string|max:255',
                'date_naiss_membre' => 'nullable|date',
                'genre_membre' => 'required|string|max:10',
                'type_membre' => 'required|string|max:50',
                'cmu' => 'required|string|in:Oui,Non',
                'num_cmu' => 'nullable|string',
                'cmu_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'employee_id' => 'required|integer',
                'company_id' => 'required|integer',
            ], [
                'nom.required' => 'Le nom est obligatoire',
                'prenoms.required' => 'Les prénoms sont obligatoires',
                'genre_membre.required' => 'Le genre est obligatoire',
                'type_membre.required' => 'Le type de membre est obligatoire',
                'cmu.required' => 'Le statut CMU est obligatoire',
                'cmu.in' => 'Le statut CMU doit être Oui ou Non',
                'employee_id.required' => 'L\'ID de l\'employé est obligatoire',
                'company_id.required' => 'L\'ID de l\'entreprise est obligatoire',
            ]);

            // Si un document CMU est fourni, le traiter
            $documentPath = null;
            if ($request->hasFile('cmu_document')) {
                $document = $request->file('cmu_document');
                $documentPath = $document->store('cmu_documents', 'public');
            }

            // Création du membre de famille
            $familyMember = Family::create([
                'nom' => $validated['nom'],
                'prenoms' => $validated['prenoms'],
                'date_naiss_membre' => $validated['date_naiss_membre'],
                'genre_membre' => $validated['genre_membre'],
                'type_membre' => $validated['type_membre'],
                'cmu' => $validated['cmu'],
                'num_cmu' => $validated['num_cmu'],
                'document' => $documentPath,
                'employee_id' => $validated['employee_id'],
                'company_id' => $validated['company_id'],
            ]);

            // Si CMU est "Oui", créer l'enregistrement dans employee_cmus
            if ($validated['cmu'] === 'Oui') {
                $cmuData = [
                    'employee_id' => $validated['employee_id'],
                    'family_id' => $familyMember->id,
                    'name_cmu' => $familyMember->nom,
                    'prenom_cmu' => $familyMember->prenoms,
                    'date_naiss_cmu' => $familyMember->date_naiss_membre,
                    'genre_cmu' => $familyMember->genre_membre,
                    'num_cmu' => $familyMember->num_cmu,
                    'type_cmu' => $familyMember->type_membre,
                    'company_id' => $validated['company_id'],
                ];
                
                // Insérer dans la table employee_cmus
                \DB::table('employee_cmus')->insert($cmuData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Membre de famille ajouté avec succès'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un membre de famille
     */
    public function updateFamily(Request $request, $id)
    {
        try {
            $familyMember = Family::findOrFail($id);
            
            // Vérifier que le membre appartient à l'entreprise de l'utilisateur
            if ($familyMember->company_id != auth()->user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas la permission de modifier ce membre.'
                ], 403);
            }

            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'prenoms' => 'required|string|max:255',
                'date_naiss_membre' => 'nullable|date',
                'genre_membre' => 'required|string|max:10',
                'type_membre' => 'required|string|max:50',
                'cmu' => 'required|string|in:Oui,Non',
                'num_cmu' => 'nullable|string',
                'cmu_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ], [
                'nom.required' => 'Le nom est obligatoire',
                'prenoms.required' => 'Les prénoms sont obligatoires',
                'genre_membre.required' => 'Le genre est obligatoire',
                'type_membre.required' => 'Le type de membre est obligatoire',
                'cmu.required' => 'Le statut CMU est obligatoire',
                'cmu.in' => 'Le statut CMU doit être Oui ou Non',
            ]);

            // Si un document CMU est fourni, le traiter
            $documentPath = $familyMember->document;
            if ($request->hasFile('cmu_document')) {
                $document = $request->file('cmu_document');
                $documentPath = $document->store('cmu_documents', 'public');
            }

            // Mise à jour du membre de famille
            $familyMember->update([
                'nom' => $validated['nom'],
                'prenoms' => $validated['prenoms'],
                'date_naiss_membre' => $validated['date_naiss_membre'],
                'genre_membre' => $validated['genre_membre'],
                'type_membre' => $validated['type_membre'],
                'cmu' => $validated['cmu'],
                'num_cmu' => $validated['num_cmu'],
                'document' => $documentPath,
            ]);

            // Si CMU est "Oui", mettre à jour ou créer l'enregistrement dans employee_cmus
            if ($validated['cmu'] === 'Oui') {
                $cmuData = [
                    'employee_id' => $familyMember->employee_id,
                    'family_id' => $familyMember->id,
                    'name_cmu' => $familyMember->nom,
                    'prenom_cmu' => $familyMember->prenoms,
                    'date_naiss_cmu' => $familyMember->date_naiss_membre,
                    'genre_cmu' => $familyMember->genre_membre,
                    'num_cmu' => $familyMember->num_cmu,
                    'type_cmu' => $familyMember->type_membre,
                    'company_id' => $familyMember->company_id,
                ];
                
                // Vérifier si l'enregistrement CMU existe déjà
                $existingCmu = \DB::table('employee_cmus')
                    ->where('family_id', $familyMember->id)
                    ->first();
                
                if ($existingCmu) {
                    \DB::table('employee_cmus')
                        ->where('family_id', $familyMember->id)
                        ->update($cmuData);
                } else {
                    \DB::table('employee_cmus')->insert($cmuData);
                }
            } else {
                // Si CMU est "Non", supprimer l'enregistrement CMU s'il existe
                \DB::table('employee_cmus')
                    ->where('family_id', $familyMember->id)
                    ->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Membre de famille mis à jour avec succès'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un membre de famille
     */
    public function destroyFamily($id)
    {
        try {
            $familyMember = Family::findOrFail($id);
            
            // Vérifier que le membre appartient à l'entreprise de l'utilisateur
            if ($familyMember->company_id != auth()->user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas la permission de supprimer ce membre.'
                ], 403);
            }

            // Supprimer l'enregistrement CMU associé s'il existe
            \DB::table('employee_cmus')
                ->where('family_id', $familyMember->id)
                ->delete();

            // Supprimer le membre de famille
            $familyMember->delete();

            return response()->json([
                'success' => true,
                'message' => 'Membre de famille supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stocker un nouveau document
     */
    public function storeDocument(Request $request)
    {
        try {
            $validated = $request->validate([
                'libelle' => 'required|string|max:255',
                'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
                'employee_id' => 'required|integer',
                'company_id' => 'required|integer',
            ]);

            $document = $request->file('document');
            $documentPath = $document->store('employee_documents', 'public');
            
            $documentData = [
                'path' => $documentPath,
                'original_name' => $document->getClientOriginalName(),
                'size' => $document->getSize(),
                'type' => $document->getMimeType(),
            ];

            EmployeeDocument::create([
                'employee_id' => $validated['employee_id'],
                'company_id' => $validated['company_id'],
                'libelle' => $validated['libelle'],
                'document_value' => json_encode($documentData),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document ajouté avec succès'
            ]);
        } catch (\Exception $e) {
           return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un document
     */
    public function destroyDocument($id)
    {
        try {
            $document = EmployeeDocument::findOrFail($id);
            
            // Vérifier que le document appartient à l'entreprise de l'utilisateur
            if ($document->company_id != auth()->user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas la permission de supprimer ce document.'
                ], 403);
            }

            // Supprimer le fichier physique s'il existe
            $documentData = json_decode($document->document_value, true);
            if (isset($documentData['path']) && $documentData['path']) {
                $filePath = storage_path('app/public/' . $documentData['path']);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Supprimer l'enregistrement de la base de données
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }
}
