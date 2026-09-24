<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Company; 
use App\Models\Sector;
use App\Models\Country;
use App\Models\Module;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    /**
     * Afficher le dashboard du Super Admin
     */
    public function dashboard()
    {
        // Vérifier que l'utilisateur est un super admin
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Statistiques du système
        $stats = $this->getDashboardStats();

        return view('super-admin.dashboard', compact('stats'));
    }

    /**
     * Calculer les statistiques du dashboard
     */
    private function getDashboardStats()
    {
        // Statistiques de base
        $total_enterprises = User::companies()->active()->count();
        $total_users = User::active()->count();
        $total_hr_users = User::hrUsers()->active()->count();
        $total_employees = User::employees()->active()->count();

        // Entreprises récentes (5 dernières)
        $recent_enterprises = Company::latest()->limit(5)->get();

        // Calcul des pourcentages de croissance (comparaison avec le mois dernier)
        $current_month_enterprises = Company::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $last_month_enterprises = Company::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $enterprise_growth_rate = $this->calculateGrowthRate($current_month_enterprises, $last_month_enterprises);

        // Pourcentages pour les employés
        $current_month_employees = User::employees()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $last_month_employees = User::employees()
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $employee_growth_rate = $this->calculateGrowthRate($current_month_employees, $last_month_employees);

        // Pourcentages pour les utilisateurs RH
        $current_month_hr_users = User::hrUsers()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $last_month_hr_users = User::hrUsers()
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $hr_user_growth_rate = $this->calculateGrowthRate($current_month_hr_users, $last_month_hr_users);

        // Pourcentage pour utilisateurs totaux
        $current_month_users = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $last_month_users = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $user_growth_rate = $this->calculateGrowthRate($current_month_users, $last_month_users);

        // Données pour le graphique des revenus
        $revenue_data = $this->getRevenueData();

        // Données d'activité récente
        $recent_activity = $this->getRecentActivity();

        return [
            'total_enterprises' => $total_enterprises,
            'total_users' => $total_users,
            'total_hr_users' => $total_hr_users,
            'total_employees' => $total_employees,
            'recent_enterprises' => $recent_enterprises,
            'enterprise_growth_rate' => $enterprise_growth_rate,
            'employee_growth_rate' => $employee_growth_rate,
            'hr_user_growth_rate' => $hr_user_growth_rate,
            'user_growth_rate' => $user_growth_rate,
            'revenue_data' => $revenue_data,
            'recent_activity' => $recent_activity,
        ];
    }

    /**
     * Récupérer les données d'activité récente
     */
    private function getRecentActivity()
    {
        $activities = [];

        // 1. Dernière connexion de l'admin actuel
        $activities[] = [
            'type' => 'login',
            'title' => 'Connexion Admin',
            'description' => Auth::user()->name . ' s\'est connecté au système',
            'time' => now()->diffForHumans(),
            'icon' => 'ti-user-check',
            'color' => 'primary'
        ];

        // 2. Derniers employés ajoutés (max 2)
        $recent_employees = User::employees()
            ->latest()
            ->limit(2)
            ->get();

        foreach ($recent_employees as $employee) {
            $activities[] = [
                'type' => 'employee',
                'title' => 'Nouvel Employé',
                'description' => $employee->name . ' a été ajouté(e) au système',
                'time' => $employee->created_at->diffForHumans(),
                'icon' => 'ti-user-plus',
                'color' => 'success'
            ];
        }

        // 3. Dernières entreprises créées (max 2)
        $recent_companies = User::companies()
            ->latest()
            ->limit(2)
            ->get();

        foreach ($recent_companies as $company) {
            $activities[] = [
                'type' => 'company',
                'title' => 'Entreprise créée',
                'description' => 'Nouvelle entreprise "' . $company->name . '" ajoutée',
                'time' => $company->created_at->diffForHumans(),
                'icon' => 'ti-building',
                'color' => 'info'
            ];
        }

        // 4. Derniers plans modifiés
        $recent_plans = Plan::latest()
            ->limit(1)
            ->get();

        foreach ($recent_plans as $plan) {
            $activities[] = [
                'type' => 'plan',
                'title' => 'Mise à jour Pack',
                'description' => 'Pack ' . $plan->name . ' modifié',
                'time' => $plan->updated_at->diffForHumans(),
                'icon' => 'ti-package',
                'color' => 'warning'
            ];
        }

        // Trier par date et limiter à 6 éléments
        $activities = array_slice($activities, 0, 6);

        return $activities;
    }

    /**
     * Calculer le taux de croissance
     */
    private function calculateGrowthRate($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Récupérer les données de revenus pour le graphique
     */
    private function getRevenueData()
    {
        $current_year = now()->year;
        $months = [];

        // Générer les 12 mois de l'année
        for ($i = 1; $i <= 12; $i++) {
            $date = now()->setYear($current_year)->setMonth($i);

            // Calculer les revenus du mois (basé sur les commandes payées)
            $monthly_revenue = Order::where('status', 'paid')
                ->whereMonth('paid_at', $i)
                ->whereYear('paid_at', $current_year)
                ->sum('total_amount');

            // Calculer les revenus de l'année dernière pour comparaison
            $last_year_revenue = Order::where('status', 'paid')
                ->whereMonth('paid_at', $i)
                ->whereYear('paid_at', $current_year - 1)
                ->sum('total_amount');

            $months[] = [
                'month' => $date->translatedFormat('M'),
                'current_year' => $monthly_revenue,
                'last_year' => $last_year_revenue,
            ];
        }

        return [
            'labels' => array_column($months, 'month'),
            'current_year' => array_column($months, 'current_year'),
            'last_year' => array_column($months, 'last_year'),
        ];
    }

    // ========================================
    // FONCTIONS GESTION DES USERS
    // ========================================

    /**
     * Créations des utilisateurs
     */
    public function createUser(User $entreprise)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if ($entreprise->type !== 'company') {
            abort(404, 'Entreprise non trouvée');
        }

        // Récupérer les détails de l'entreprise depuis la table companies
        $company = Company::where('user_id', $entreprise->id)->first();

        return view('super-admin.users.create', compact('entreprise', 'company'));
    }

    /**
     * Enregistrer un nouvel utilisateur pour une entreprise
     */
    public function storeUser(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'type' => 'required|in:hr,paie,employee',
            'enterprise_id' => 'required|exists:users,id',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'password' => bcrypt($validated['password']),
                'type' => $validated['type'],
                'created_by' => $validated['enterprise_id'],
                'phone' => $validated['phone'],
                'is_active' => $validated['is_active'] ?? false,
            ]);

            return redirect()->route('super-admin.users.show', $user)->with('success', 'Utilisateur créé avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de l\'utilisateur: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
        }
    }

    /**
     * Afficher le formulaire d'édition d'un utilisateur
     */
    public function editUser(User $user)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if (!in_array($user->type, ['hr', 'paie', 'payroll', 'employee'])) {
            abort(404, 'Utilisateur non trouvé');
        }

        return view('super-admin.users.edit', compact('user'));
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function showUser(User $user)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if (!in_array($user->type, ['hr', 'paie', 'payroll', 'employee'])) {
            abort(404, 'Utilisateur non trouvé');
        }

        $entreprise = Company::where('user_id', $user->created_by)->first();

        return view('super-admin.users.show', compact('user', 'entreprise'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(Request $request, User $user)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if (!in_array($user->type, ['hr', 'paie', 'payroll', 'employee'])) {
            abort(404, 'Utilisateur non trouvé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|unique:users,username,' . $user->id,
            'type' => 'required|in:hr,paie,employee',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        try {
            $user->update($validated);
            return redirect()->route('super-admin.users.show', $user)->with('success', 'Utilisateur mis à jour avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour de l\'utilisateur: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser(User $user)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if (!in_array($user->type, ['hr', 'paie', 'payroll', 'employee'])) {
            abort(404, 'Utilisateur non trouvé');
        }

        try {
            $user->delete();
            return redirect()->route('super-admin.users.index')->with('success', 'Utilisateur supprimé avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression de l\'utilisateur: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    /**
     * Activer un utilisateur
     */
    public function activateUser(User $user)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if (!in_array($user->type, ['hr', 'paie', 'payroll', 'employee'])) {
            abort(404, 'Utilisateur non trouvé');
        }

        $user->update([
            'is_active' => true,
            'active_status' => 1,
        ]);

        return redirect()->back()->with('success', 'Utilisateur activé avec succès');
    }

    /**
     * Suspendre un utilisateur
     */
    public function suspendUser(User $user)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if (!in_array($user->type, ['hr', 'paie', 'payroll', 'employee'])) {
            abort(404, 'Utilisateur non trouvé');
        }

        $user->update([
            'is_active' => false,
            'active_status' => 0,
        ]);

        return redirect()->back()->with('success', 'Utilisateur suspendu avec succès');
    }

    // ========================================
    // FONCTIONS GESTION DES ENTREPRISES
    // ========================================

    /**
     * Lister les entreprises
     */
    public function enterprises(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $query = Company::query();

        // Filtres: recherche globale, statut, plan
        $search = trim((string) $request->get('q', ''));
        $status = (string) $request->get('status', '');
        $plan = (string) $request->get('plan', '');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%");
            });
        }

        if ($status !== '') {
            // Map simple status values
            if ($status === 'active') {
                $query->where('is_active', 1)->where(function($q){
                    $q->where('subscription_status', 'active')->orWhereNull('subscription_status');
                });
            } elseif (in_array($status, ['trial','suspended','expired','cancelled','pending'])) {
                $query->where('subscription_status', $status);
            } elseif ($status === 'inactive') {
                $query->where('is_active', 0);
            }
        }

        if ($plan !== '') {
            $query->where('plan_id', (int) $plan);
        }

        $enterprises = $query->with(['sector', 'companyPlan'])->orderByDesc('created_at')->paginate(15)->appends($request->query());

        $packs = Plan::where('is_active', true)->get();

        // Statistiques pour la vue
        $stats = [
            'total_enterprises' => Company::count(),
            'active_enterprises' => Company::where('is_active', 1)->count(),
            'trial_enterprises' => Company::where('subscription_status', 'trial')->count(),
            'expired_enterprises' => Company::where('subscription_status', 'expired')->count(),
        ];

        return view('super-admin.enterprises.index', compact('enterprises', 'packs', 'stats'));
    }

    /**
     * Exporter la liste des entreprises (CSV/XLSX basique via CSV)
     */
    public function exportEnterprisesXlsx(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Reuse the same filters as enterprises()
        $query = Company::query();
        $search = trim((string) $request->get('q', ''));
        $status = (string) $request->get('status', '');
        $plan = (string) $request->get('plan', '');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%");
            });
        }
        if ($status !== '') {
            if ($status === 'active') {
                $query->where('is_active', 1)->where(function($q){
                    $q->where('subscription_status', 'active')->orWhereNull('subscription_status');
                });
            } elseif (in_array($status, ['trial','suspended','expired','cancelled','pending'])) {
                $query->where('subscription_status', $status);
            } elseif ($status === 'inactive') {
                $query->where('is_active', 0);
            }
        }
        if ($plan !== '') {
            $query->where('plan_id', (int) $plan);
        }

        $companies = $query->orderBy('name')->get([
            'name','email','phone','city','country','plan_id','subscription_status','subscription_start_date','subscription_end_date','is_active'
        ]);

        $filename = 'entreprises_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($companies) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Nom','Email','Téléphone','Ville','Pays','Plan','Statut abon.','Début','Fin','Actif']);
            foreach ($companies as $c) {
                fputcsv($handle, [
                    $c->name,
                    $c->email,
                    $c->phone,
                    $c->city,
                    $c->country,
                    $c->plan_id,
                    $c->subscription_status,
                    optional($c->subscription_start_date)->format('Y-m-d'),
                    optional($c->subscription_end_date)->format('Y-m-d'),
                    $c->is_active ? 'Oui' : 'Non',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exporter la liste des entreprises en PDF (si dompdf présent), sinon HTML
     */
    public function exportEnterprisesPdf(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $query = Company::query();
        $search = trim((string) $request->get('q', ''));
        $status = (string) $request->get('status', '');
        $plan = (string) $request->get('plan', '');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%");
            });
        }
        if ($status !== '') {
            if ($status === 'active') {
                $query->where('is_active', 1)->where(function($q){
                    $q->where('subscription_status', 'active')->orWhereNull('subscription_status');
                });
            } elseif (in_array($status, ['trial','suspended','expired','cancelled','pending'])) {
                $query->where('subscription_status', $status);
            } elseif ($status === 'inactive') {
                $query->where('is_active', 0);
            }
        }
        if ($plan !== '') {
            $query->where('plan_id', (int) $plan);
        }

        $companies = $query->orderBy('name')->get();

        $html = view('super-admin.enterprises.export-pdf', compact('companies'))->render();

        // Si DomPDF est installé, générer un PDF, sinon retourner l'HTML avec content-disposition PDF
        if (class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'portrait');
            return $pdf->download('entreprises_' . now()->format('Ymd_His') . '.pdf');
        }

        return response($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="entreprises_' . now()->format('Ymd_His') . '.pdf"'
        ]);
    }

    /**
     * Afficher une entreprise spécifique
     */
    public function showEnterprise(User $enterprise)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if ($enterprise->type !== 'company') {
            abort(404, 'Entreprise non trouvée');
        }

        // Récupérer les utilisateurs de cette entreprise
        $company = Company::where('user_id', $enterprise->id)->first();

        $pack = Plan::where('id', $enterprise->plan)->first(); 

        return view('super-admin.enterprises.show', compact('enterprise', 'company', 'pack'));
    }

    /**
     * Afficher l'activité d'une entreprise
     */
    public function showActivity(User $enterprise)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if ($enterprise->type !== 'company') {
            abort(404, 'Entreprise non trouvée');
        }

        $company = Company::where('user_id', $enterprise->id)->firstOrFail();
        $pack = Plan::find($company->plan_id);

        // Données d'activité de base (faute de logs dédiés)
        $activity = [
            'created_at' => $company->created_at,
            'updated_at' => $company->updated_at,
            'subscription_status' => $company->subscription_status,
            'subscription_start_date' => $company->subscription_start_date,
            'subscription_end_date' => $company->subscription_end_date,
            'is_active' => $company->is_active,
        ];

        // Timeline synthétique
        $timeline = [];
        if ($company->created_at) {
            $timeline[] = [
                'time' => $company->created_at,
                'icon' => 'ti ti-bag',
                'title' => 'Création de l\'entreprise',
                'desc' => 'L\'entreprise a été créée dans le système.'
            ];
        }
        if ($company->subscription_start_date) {
            $timeline[] = [
                'time' => $company->subscription_start_date,
                'icon' => 'ti ti-check',
                'title' => 'Début d\'abonnement',
                'desc' => 'Activation de l\'abonnement.'
            ];
        }
        if ($company->subscription_end_date) {
            $timeline[] = [
                'time' => $company->subscription_end_date,
                'icon' => 'ti ti-control-stop',
                'title' => 'Fin d\'abonnement (prévue)',
                'desc' => 'Date de fin calculée de l\'abonnement.'
            ];
        }
        $timeline[] = [
            'time' => $company->updated_at ?? $company->created_at,
            'icon' => $company->is_active ? 'ti ti-check' : 'ti ti-ban',
            'title' => $company->is_active ? 'Entreprise active' : 'Entreprise inactive',
            'desc' => 'Statut courant de l\'entreprise.'
        ];
        if ($pack) {
            $timeline[] = [
                'time' => $company->updated_at ?? $company->created_at,
                'icon' => 'ti ti-package',
                'title' => 'Plan: ' . $pack->name,
                'desc' => 'Plan actuel associé à l\'entreprise.'
            ];
        }

        // Tri desc par date
        usort($timeline, function($a, $b) {
            $ta = $a['time'] ? strtotime((string)$a['time']) : 0;
            $tb = $b['time'] ? strtotime((string)$b['time']) : 0;
            return $tb <=> $ta;
        });

        return view('super-admin.enterprises.activity', compact('enterprise', 'company', 'pack', 'activity', 'timeline'));
    }

    /**
     * Créer une nouvelle entreprise
     */
    public function createEnterprise()
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $packs = Plan::where('is_active', true)->get();
        $sectors = Sector::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();

        return view('super-admin.enterprises.create', compact('packs', 'sectors', 'countries'));
    }

    /**
     * Enregistrer une nouvelle entreprise
     */
    public function storeEnterprise(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'plan' => 'required|integer|min:1',
            'storage_limit' => 'required|numeric|min:0',
            'phone' => 'nullable|string',
            'phone_country_code' => 'nullable|string',
            'timezone' => 'nullable|string',
            'locale' => 'nullable|string',
            'currency' => 'nullable|string',
            'industry' => 'nullable|string',
            'size' => 'nullable|string',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'location' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'registration_number' => 'nullable|string',
            'working_hours_per_week' => 'nullable|integer|min:1',
            'working_days_per_week' => 'nullable|integer|min:1',
            'max_employees' => 'nullable|integer|min:0',
            // Subscription configuration
            'activation_timing' => 'nullable|in:now,later',
            'billing_period' => 'nullable|in:monthly,yearly',
            'subscription_start_date' => 'nullable|date',
            'subscription_end_date' => 'nullable|date|after_or_equal:subscription_start_date',
        ]);
        
        $logoName = '';
        $logo = $request->file('logo');
        
        if ($logo) {
            // Vérifier que le fichier est valide
            if ($logo->isValid()) {
                // Générer un nom de fichier unique
                $logoName = time() . '.' . $logo->getClientOriginalExtension();
                
                // Déplacer le fichier vers le bon répertoire
                $logo->move(public_path('storage/logos'), $logoName);
            } else {
                // Gérer l'erreur si le fichier n'est pas valide
                return response()->json(['error' => 'Le fichier est invalide.'], 400);
            }
        }
        
        try {
            // Compute subscription activation and dates
            $activationTiming = $request->input('activation_timing', 'now');
            $billingPeriod = $request->input('billing_period', 'monthly');

            $start = $activationTiming === 'later' && $request->filled('subscription_start_date')
                ? Carbon::parse($request->input('subscription_start_date'))
                : Carbon::now();

            $end = $request->filled('subscription_end_date')
                ? Carbon::parse($request->input('subscription_end_date'))
                : ($billingPeriod === 'yearly' ? $start->copy()->addYear() : $start->copy()->addMonth());

            $isActive = $activationTiming === 'now';
            $subscriptionStatus = $isActive ? 'active' : 'pending';
            $plan = Plan::find($validated['plan']);

            // Créer l'entreprise utilisateur
            $company = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'password' => $validated['password'],
                'type' => 'company',
                'plan' => (int) $validated['plan'],
                'storage_limit' => (float) $validated['storage_limit'],
                'plan_expire_date' => $end,
                'created_by' => Auth::id(),
                'is_active' => $isActive ? 1 : 0,
                'active_status' => 1,
                'lang' => $validated['locale'] ?? 'fr',
                'currency' => $validated['currency'] ?? 'XOF',
                'plan_cpte_trait' => $plan->nbre_trait,
            ]);

            // Créer l'entité Company associée
            Company::create([
                'user_id' => $company->id,
                'plan_id' => (int) $validated['plan'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['location'].', '.$validated['address'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'country' => $validated['country'],
                'website' => $validated['website'],
                'logo' => $logoName,
                'industry' => $validated['industry'],
                'size' => $validated['size'],
                'description' => $validated['description'],
                'tax_id' => $validated['tax_id'],
                'registration_number' => $validated['registration_number'],
                'working_hours_per_week' => $validated['working_hours_per_week'] ?? 40,
                'working_days_per_week' => $validated['working_days_per_week'] ?? 5,
                'is_active' => $isActive,
                'subscription_status' => $subscriptionStatus,
                'subscription_start_date' => $start,
                'subscription_end_date' => $end,
                'max_employees' => $plan->max_employees,
                'max_storage_gb' => (float) $validated['storage_limit'],
                'current_storage_used' => 0,
                'settings' => [],
                'created_by' => Auth::id(),
            ]);
            \Log::info('Entreprise créée avec succès');
            return redirect()->route('super-admin.enterprises.show', $company)
                           ->with('success', 'Entreprise créée avec succès');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de l\'entreprise: ' . $e->getMessage());
            return back()->withInput()
                        ->withErrors(['error' => 'Erreur lors de la création de l\'entreprise: ' . $e->getMessage()]);
        }
    }

    /**
     * Afficher le formulaire d'édition d'une entreprise
     */
    public function editEnterprise(User $enterprise)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if ($enterprise->type !== 'company') {
            abort(404, 'Entreprise non trouvée');
        }

        $enterprises = Company::where('user_id', $enterprise->id)->first();
        $packs = Plan::where('is_active', true)->get();
        $sectors = Sector::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();

        return view('super-admin.enterprises.edit', compact('enterprises', 'enterprise', 'packs', 'sectors', 'countries'));
    }

    /**
     * Mettre à jour une entreprise
     */
    public function updateEnterprise(Request $request, User $enterprise)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if ($enterprise->type !== 'company') {
            abort(404, 'Entreprise non trouvée');
        }

        // LOG: payload brut (sans mots de passe) et fichiers présents
        \Log::info('updateEnterprise: incoming payload', [
            'user_id' => $enterprise->id,
            'payload' => $request->except(['password','password_confirmation']),
            'files' => array_keys($request->allFiles() ?? []),
        ]);

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $enterprise->id,
            'username' => 'required|string|unique:users,username,' . $enterprise->id,
            'plan' => 'required|integer|min:1',
            'storage_limit' => 'required|numeric|min:0',
            'phone' => 'nullable|string',
            'timezone' => 'nullable|string',
            'locale' => 'nullable|string',
            'industry' => 'nullable|string',
            'size' => 'nullable|string',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'location' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'registration_number' => 'nullable|string',
            'working_hours_per_week' => 'nullable|integer|min:1',
            'working_days_per_week' => 'nullable|integer|min:1',
            'max_employees' => 'nullable|integer|min:0',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:2048',
            // Subscription config
            'activation_timing' => 'nullable|in:now,later',
            'billing_period' => 'nullable|in:monthly,yearly',
            'subscription_start_date' => 'nullable|date',
            'subscription_end_date' => 'nullable|date|after_or_equal:subscription_start_date',
        ]);

        // LOG: données validées et types des champs critiques
        $locationRaw = $request->input('location');
        $addressRaw = $request->input('address');
        \Log::info('updateEnterprise: validated & types', [
            'validated' => $validated,
            'types' => [
                'name' => gettype($request->input('name')),
                'email' => gettype($request->input('email')),
                'username' => gettype($request->input('username')),
                'plan' => gettype($request->input('plan')),
                'storage_limit' => gettype($request->input('storage_limit')),
                'location' => gettype($locationRaw),
                'address' => gettype($addressRaw),
                'is_active' => gettype($request->input('is_active')),
            ],
            'values_preview' => [
                'location' => $locationRaw,
            ],
        ]);

        // Récupérer l'entité Company liée
        $company = Company::where('user_id', $enterprise->id)->first();

        // Gestion du logo (optionnelle)
        $logoName = $company->logo ?? '';
        $logo = $request->file('logo');
        if ($logo && $logo->isValid()) {
            $logoName = time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('storage/logos'), $logoName);
        }

        try {
            // Statut actif depuis le champ is_active (switch)
            $isActive = $request->boolean('is_active');

            // Casts sûrs
            $planId = (int) $validated['plan'];
            $storageLimit = is_numeric($validated['storage_limit']) ? (float) $validated['storage_limit'] : 0.0;
            $plan = Plan::find($planId);
            // Compute subscription activation/dates
            $activationTiming = $request->input('activation_timing', 'now');
            $billingPeriod = $request->input('billing_period', 'monthly');
            $start = $activationTiming === 'later' && $request->filled('subscription_start_date')
                ? Carbon::parse($request->input('subscription_start_date'))
                : Carbon::now();
            $end = $request->filled('subscription_end_date')
                ? Carbon::parse($request->input('subscription_end_date'))
                : ($billingPeriod === 'yearly' ? $start->copy()->addYear() : $start->copy()->addMonth());
            $isActive = $activationTiming === 'now';
            $subscriptionStatus = $isActive ? 'active' : 'pending';

            // Mise à jour de l'utilisateur (table users)
            $userUpdate = [
                'name' => (string) $validated['name'],
                'email' => (string) $validated['email'],
                'username' => (string) $validated['username'],
                'plan' => $planId,
                'storage_limit' => $storageLimit,
                'is_active' => $isActive,
                'plan_expire_date' => $end,
            ];
            \Log::info('updateEnterprise: updating User', ['user_id' => $enterprise->id, 'update' => $userUpdate]);
            $enterprise->update($userUpdate);

            // Mise à jour de l'entité Company liée
            if ($company) {
                // Composer l'adresse à partir de scalaires uniquement
                $locationStr = is_scalar($request->input('location')) ? (string) $request->input('location') : '';
                $addressStr = is_scalar($request->input('address')) ? (string) $request->input('address') : '';
                $addressCombined = trim(implode(', ', array_filter([trim($locationStr), trim($addressStr)], fn($v) => $v !== '')));

                $companyUpdate = [
                    'plan_id' => $planId,
                    'name' => (string) $validated['name'],
                    'email' => (string) $validated['email'],
                    'phone' => isset($validated['phone']) && is_scalar($validated['phone']) ? (string) $validated['phone'] : $company->phone,
                    'address' => isset($validated['location']),
                    'city' => isset($validated['city']) && is_scalar($validated['city']) ? (string) $validated['city'] : $company->city,
                    'postal_code' => isset($validated['postal_code']) && is_scalar($validated['postal_code']) ? (string) $validated['postal_code'] : $company->postal_code,
                    'country' => isset($validated['country']) && is_scalar($validated['country']) ? (string) $validated['country'] : $company->country,
                    'website' => isset($validated['website']) && is_scalar($validated['website']) ? (string) $validated['website'] : $company->website,
                    'logo' => $logoName,
                    'industry' => isset($validated['industry']) && is_scalar($validated['industry']) ? (string) $validated['industry'] : $company->industry,
                    'size' => isset($validated['size']) && is_scalar($validated['size']) ? (string) $validated['size'] : $company->size,
                    'description' => isset($validated['description']) && is_scalar($validated['description']) ? (string) $validated['description'] : $company->description,
                    'tax_id' => isset($validated['tax_id']) && is_scalar($validated['tax_id']) ? (string) $validated['tax_id'] : $company->tax_id,
                    'registration_number' => isset($validated['registration_number']) && is_scalar($validated['registration_number']) ? (string) $validated['registration_number'] : $company->registration_number,
                    'working_hours_per_week' => isset($validated['working_hours_per_week']) ? (int) $validated['working_hours_per_week'] : $company->working_hours_per_week,
                    'working_days_per_week' => isset($validated['working_days_per_week']) ? (int) $validated['working_days_per_week'] : $company->working_days_per_week,
                    'is_active' => $isActive,
                    'subscription_status' => $subscriptionStatus,
                    'subscription_start_date' => $start,
                    'subscription_end_date' => $end,
                    'max_employees' => $plan->max_employees,
                    'max_storage_gb' => $storageLimit,
                ];
                \Log::info('updateEnterprise: updating Company', ['company_user_id' => $enterprise->id, 'update' => $companyUpdate]);
                $company->update($companyUpdate);
            }

            return redirect()->route('super-admin.enterprises.show', $enterprise)
                           ->with('success', 'Entreprise mise à jour avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour de l\'entreprise', [
                'user_id' => $enterprise->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'validated' => isset($validated) ? $validated : null,
                'request_types' => [
                    'location' => gettype($request->input('location')),
                    'address' => gettype($request->input('address')),
                    'storage_limit' => gettype($request->input('storage_limit')),
                ],
            ]);
            return redirect()->back()->with(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    /**
     * Afficher les employés d'une entreprise
     */
    public function enterpriseUsers(User $enterprise)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if ($enterprise->type !== 'company') {
            abort(404, 'Entreprise non trouvée');
        }

        // Récupérer les utilisateurs de cette entreprise
        $users = Company::where('companies.user_id', $enterprise->id)
                    ->join('users', 'companies.user_id', '=', 'users.created_by')
                    ->paginate(20);

        $company = Company::where('user_id', $enterprise->id)->first();

        return view('super-admin.enterprises.users', compact('enterprise', 'users', 'company'));
    }

    /**
     * Activer une entreprise
     */
    public function activateEnterprise(User $enterprise)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if ($enterprise->type !== 'company') {
            abort(404, 'Entreprise non trouvée');
        }

        $company = Company::where('user_id', $enterprise->id)->first();

        if (!$company) {
            return redirect()->back()->with('error', 'Informations entreprise non trouvées');
        }

        // Le bouton s'intitule « Activer l'abonnement » : il doit donc poser les
        // dates d'échéance, et pas seulement les drapeaux is_active. Sans ça
        // subscription_status restait à « trial » et plan_expire_date à NULL,
        // l'entreprise n'était jamais considérée comme abonnée et le bouton
        // continuait d'afficher « Activer » après le clic.
        //
        // Réactiver un abonnement suspendu ne doit pas le raccourcir : une
        // échéance encore à venir est conservée telle quelle. Sinon on ouvre
        // une période d'un mois à partir d'aujourd'hui, la durée retenue
        // partout ailleurs (renouvellement, validation de commande) : la colonne
        // plans.duration est du texte libre et incohérent (« Mois », « 1 »),
        // elle ne peut pas servir de base de calcul.
        $echeanceEnCours = $company->subscription_end_date
            && now()->lessThan($company->subscription_end_date);

        $dateDebut = $echeanceEnCours ? $company->subscription_start_date : now();
        $dateFin = $echeanceEnCours ? $company->subscription_end_date : now()->addMonth();

        DB::transaction(function () use ($enterprise, $company, $dateDebut, $dateFin) {
            $enterprise->update([
                'is_active' => true,
                'active_status' => 1,
                'plan_expire_date' => $dateFin,
            ]);

            $company->update([
                'is_active' => true,
                'subscription_status' => 'active',
                'subscription_start_date' => $dateDebut,
                'subscription_end_date' => $dateFin,
            ]);
        });

        return redirect()->back()->with('success', 'Abonnement activé avec succès (échéance : ' . $dateFin->format('d/m/Y') . ')');
    }

    public function suspendEnterprise(User $enterprise)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if ($enterprise->type !== 'company') {
            abort(404, 'Entreprise non trouvée');
        }

        $enterprise->update([
            'is_active' => false,
            'active_status' => 0,
        ]);

        $company = Company::where('user_id', $enterprise->id)->first();

        // Une entreprise sans ligne companies faisait ici une erreur fatale.
        if ($company) {
            $company->update([
                'is_active' => false,
                'subscription_status' => 'suspended',
            ]);
        }

        return redirect()->back()->with('success', 'Entreprise suspendue avec succès');
    }

    public function deleteEnterprise(User $enterprise)     
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }
  
        if ($enterprise->type !== 'company') {   
            abort(404, 'Entreprise non trouvée');
        }

        // Vérifier s'il y a des employés associés
        $employeesCount = User::where('id', $enterprise->id)->where('is_active', 1)->where('type', 'employee')->count();

        if ($employeesCount > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette entreprise car elle contient des employés actifs.');
        }

        $enterprise->delete();

        $company = Company::where('user_id', $enterprise->id)->first();
        $company->delete();

        return redirect()->route('super-admin.enterprises.index')->with('success', 'Entreprise supprimée avec succès');
    }

    /**
     * Supprimer une entreprise (AJAX)
     */
    public function deleteEnterpriseAjax(User $enterprise)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if ($enterprise->type !== 'company') {
            abort(404, 'Entreprise non trouvée');
        }

        // Vérifier s'il y a des employés associés
        $employeesCount = User::where('id', $enterprise->id)->where('is_active', 1)->where('type', 'employee')->count();

        if ($employeesCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer cette entreprise car elle contient des employés actifs.'
            ], 400);
        }

        $enterprise->delete();

        return redirect()->route('super-admin.enterprises.index')->with('success', 'Entreprise supprimée avec succès');
    }

    /**
     * Gérer l'abonnement d'une entreprise
     */
    public function subscriptionEntreprise(User $enterprise)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if ($enterprise->type !== 'company') {
            abort(404, 'Entreprise non trouvée');
        }

        // Récupérer les informations de l'entreprise et de l'abonnement
        $company = Company::where('user_id', $enterprise->id)->first();
        if (!$company) {
            abort(404, 'Informations entreprise non trouvées');
        }

        $pack = Plan::where('id', $company->plan_id)->first();

        // Récupérer le plan actuel
        $currentPlan = Plan::find($company->plan_id);
        $allPlans = Plan::where('is_active', true)->get();

        // Historique des commandes de cette entreprise
        $orders = Order::where('user_id', $enterprise->id)
                      ->with(['plan', 'coupon'])
                      ->orderBy('created_at', 'desc')
                      ->limit(10)
                      ->get();

        // Calculer les statistiques d'abonnement
        $subscriptionStats = [
            'days_remaining' => $company->subscription_end_date ? (int) now()->diffInDays($company->subscription_end_date, false) : null,
            'is_expired' => $company->subscription_end_date ? now()->isAfter($company->subscription_end_date) : false,
            'is_active' => $company->is_active && ($company->subscription_status === 'active' || $company->subscription_status === null),
            'current_period' => $this->getCurrentBillingPeriod($company),
            'next_renewal' => $this->getNextRenewalDate($company),
        ];

        return view('super-admin.enterprises.abonnement', compact(
            'enterprise',
            'company',
            'pack',
            'currentPlan',
            'allPlans',
            'orders',
            'subscriptionStats'
        ));
    }

    /**
     * Mettre à jour l'abonnement d'une entreprise
     */
    public function updateSubscription(Request $request, User $enterprise)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if ($enterprise->type !== 'company') {
            abort(404, 'Entreprise non trouvée');
        }

        $validated = $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
            'activation_date' => 'nullable|date',
            'billing_period' => 'required|in:monthly,yearly',
        ]);

        $company = Company::where('user_id', $enterprise->id)->first();
        if (!$company) {
            return back()->withErrors(['error' => 'Informations entreprise non trouvées']);
        }

        $plan = Plan::find($validated['plan_id']);
        if (!$plan) {
            return back()->withErrors(['error' => 'Plan non trouvé']);
        }

        try {
            $activationDate = ($validated['activation_date'] ?? null) ? Carbon::parse($validated['activation_date']) : now();
            $duration = $validated['billing_period'] === 'yearly' ? 12 : 1;
            $endDate = $activationDate->copy()->addMonths($duration);

            // Transaction : sans elle, l'échec de l'insertion de la commande
            // laissait users et companies déjà modifiés, donc un abonnement posé
            // sans trace de commande.
            DB::transaction(function () use ($enterprise, $company, $plan, $activationDate, $endDate) {
                // Mettre à jour l'utilisateur (table users)
                $enterprise->update([
                    'plan' => $plan->id,
                    'plan_expire_date' => $endDate,
                ]);

                // Mettre à jour l'entité Company
                $company->update([
                    'plan_id' => $plan->id,
                    'subscription_start_date' => $activationDate,
                    'subscription_end_date' => $endDate,
                    'subscription_status' => 'active',
                    'is_active' => true,
                ]);

                // Créer une commande pour ce changement de plan.
                // order_number est NOT NULL sans valeur par défaut : l'omettre
                // faisait échouer l'insertion en mode SQL strict.
                Order::create([
                    'user_id' => $enterprise->id,
                    'plan_id' => $plan->id,
                    'order_number' => Order::generateOrderNumber(),
                    'amount' => $plan->price,
                    'total_amount' => $plan->price,
                    'status' => 'paid',
                    'payment_method' => 'manual',
                    'paid_at' => now(),
                    'notes' => 'Changement de plan effectué par administrateur',
                ]);
            });

            return redirect()->route('super-admin.enterprises.subscription', $enterprise)
                           ->with('success', 'Abonnement mis à jour avec succès');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour de l\'abonnement', [
                'enterprise_id' => $enterprise->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    /**
     * Renouveler l'abonnement d'une entreprise
     */
    public function renewSubscription(Request $request, User $enterprise)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        if ($enterprise->type !== 'company') {
            abort(404, 'Entreprise non trouvée');
        }

        $company = Company::where('user_id', $enterprise->id)->first();
        if (!$company) {
            return back()->withErrors(['error' => 'Informations entreprise non trouvées']);
        }

        $currentPlan = Plan::find($company->plan_id);
        if (!$currentPlan) {
            return back()->withErrors(['error' => 'Plan actuel non trouvé']);
        }

        try {
            $renewalDate = now();
            $duration = 1; // Renouvellement mensuel par défaut
            $endDate = $renewalDate->copy()->addMonths($duration);

            // Si l'abonnement était expiré, on le prolonge depuis maintenant
            // Sinon, on le prolonge depuis la date d'expiration actuelle
            if ($company->subscription_end_date && now()->isAfter($company->subscription_end_date)) {
                $startDate = $renewalDate;
            } else {
                $startDate = $company->subscription_end_date;
            }

            DB::transaction(function () use ($enterprise, $company, $currentPlan, $startDate, $endDate) {
                // Mettre à jour l'utilisateur (table users)
                $enterprise->update([
                    'plan_expire_date' => $endDate,
                ]);

                // Mettre à jour l'entité Company
                $company->update([
                    'subscription_start_date' => $startDate,
                    'subscription_end_date' => $endDate,
                    'subscription_status' => 'active',
                    'is_active' => true,
                ]);

                // Créer une commande pour ce renouvellement.
                // order_number est NOT NULL sans valeur par défaut.
                Order::create([
                    'user_id' => $enterprise->id,
                    'plan_id' => $currentPlan->id,
                    'order_number' => Order::generateOrderNumber(),
                    'amount' => $currentPlan->price,
                    'total_amount' => $currentPlan->price,
                    'status' => 'paid',
                    'payment_method' => 'manual',
                    'paid_at' => now(),
                    'notes' => 'Renouvellement d\'abonnement effectué par administrateur',
                ]);
            });

            return redirect()->route('super-admin.enterprises.subscription', $enterprise)
                           ->with('success', 'Abonnement renouvelé avec succès');

        } catch (\Exception $e) {
            \Log::error('Erreur lors du renouvellement de l\'abonnement', [
                'enterprise_id' => $enterprise->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Erreur lors du renouvellement: ' . $e->getMessage()]);
        }
    }

    /**
     * Afficher les entreprises suspendues
     */
    public function enterprisesSuspend()
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $enterprises = User::companies()
                          ->where('status', 'suspended')
                          ->orderBy('name', 'asc')
                          ->paginate(15);

        return view('super-admin.enterprises.suspend', compact('enterprises'));
    }

    /**
     * Afficher les entreprises non suspendues
     */
    public function enterprisesUnsuspend()
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $enterprises = User::companies()
                          ->where('status', 'active')
                          ->orderBy('name', 'asc')
                          ->paginate(15);

        return view('super-admin.enterprises.unsuspend', compact('enterprises'));
    }

    public function enterprisesLastLogin()
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $enterprises = User::companies()
                          ->orderBy('name', 'asc')
                          ->paginate(15);

        return view('super-admin.enterprises.lastlogin', compact('enterprises'));
    }

    // ========================================
    // FONCTIONS GESTION DES PACKS
    // ========================================

    /**
     * Gestion des packs/abonnements
     */
    public function packs()
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $packs = Plan::active()->get();
        $pack = Plan::active()->first();

        // Récupérer la devise de l'utilisateur actuel
        $userCurrency = Auth::user()->getAttributes()['currency'] ?? 'EUR';

        // Récupérer les détails de la devise
        $currencyDetails = Auth::user()->currency;

        // Statistiques des packs
        $packStats = [];
        foreach ($packs as $pack) {
            $packStats[] = [
                'id' => $pack->id,
                'name' => $pack->name,
                'price' => $pack->price,
                'formatted_price' => $pack->formatted_price,
                'duration' => $pack->duration,
                'formatted_duration' => $pack->formatted_duration,
                'max_users' => $pack->max_users,
                'max_employees' => $pack->max_employees,
                'storage_limit' => $pack->storage_limit,
                'nbre_trait' => $pack->nbre_trait,
                'enable_chatgpt' => $pack->enable_chatgpt,
                'has_chatgpt' => $pack->hasChatGPT(),
                'description' => $pack->description,
                'features' => $pack->features_list,
                'company_count' => $pack->company_count,
                'active_company_count' => $pack->active_company_count,
                'usage_percentage' => $pack->usage_percentage,
                'price_monthly' => $pack->getPriceMonthly(),
                'price_yearly' => $pack->getPriceYearly(),
                'users_limit' => $pack->max_users,
                'is_popular' => $pack->is_popular ?? false,
                'is_enterprise' => $pack->is_enterprise ?? false,
                'is_active' => $pack->is_active ?? true,
            ];
        }

        // KPIs financiers (MRR, ARR, CA du mois, accrual)
        $kpis = $this->getFinancialKPIs();

        return view('super-admin.packs.index', compact('packStats', 'packs', 'pack', 'userCurrency', 'currencyDetails', 'kpis'));
    }

    /**
     * Afficher un pack spécifique
     */
    public function showPack(Plan $pack)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Récupérer la devise de l'utilisateur actuel
        $userCurrency = Auth::user()->getAttributes()['currency'] ?? 'EUR';
        $currencyDetails = Auth::user()->currency;

        return view('super-admin.packs.show', compact('pack', 'userCurrency', 'currencyDetails'));
    }

    /**
     * Afficher le formulaire de création d'un pack
     */
    public function createPack()
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Récupérer la devise de l'utilisateur actuel
        $userCurrency = Auth::user()->getAttributes()['currency'] ?? 'EUR';
        $currencyDetails = Auth::user()->currency;

        return view('super-admin.packs.create', compact('userCurrency', 'currencyDetails'));
    }

    /**
     * Créer un nouveau pack
     */
    public function storePack(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'price_yearly' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:1|max:12',
            'max_users' => 'nullable|integer|min:0',
            'max_employees' => 'nullable|integer|min:0',
            'storage_limit' => 'nullable|numeric|min:0',
            'nbre_trait' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'popular' => 'boolean',
            'enterprise' => 'boolean',
            'enable_chatgpt' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Créer le pack
        $pack = Plan::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'price_yearly' => $validated['price_yearly'] ?? null,
            'duration' => $validated['duration'] ?? 1,
            'max_users' => $validated['max_users'] ?? 0,
            'max_employees' => $validated['max_employees'] ?? 0,
            'storage_limit' => $validated['storage_limit'] ?? 5,
            'nbre_trait' => $validated['nbre_trait'] ?? 1000,
            'description' => $validated['description'] ?? null,
            'features' => $validated['features'] ? (is_array($validated['features']) ? $validated['features'] : explode("\n", $validated['features'])) : null,
            'popular' => $validated['popular'] ?? false,
            'enterprise' => $validated['enterprise'] ?? false,
            'enable_chatgpt' => $validated['enable_chatgpt'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ]);
 
        return redirect()->route('super-admin.packs.show', $pack)
                        ->with('success', 'Pack créé avec succès');
    }

    /**
     * Afficher le formulaire d'édition d'un pack
     */
    public function editPack(Plan $pack)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Récupérer la devise de l'utilisateur actuel
        $userCurrency = Auth::user()->getAttributes()['currency'] ?? 'EUR';
        $currencyDetails = Auth::user()->currency;

        return view('super-admin.packs.edit', compact('pack', 'userCurrency', 'currencyDetails'));
    }

    /**
     * Mettre à jour un pack
     */
    public function updatePack(Request $request, Plan $pack)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'price_yearly' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:1|max:12',
            'max_users' => 'nullable|integer|min:0',
            'max_employees' => 'nullable|integer|min:0',
            'storage_limit' => 'nullable|numeric|min:0',
            'nbre_trait' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'popular' => 'boolean',
            'enterprise' => 'boolean',
            'enable_chatgpt' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Mettre à jour le pack
        $pack->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'price_yearly' => $validated['price_yearly'] ?? null,
            'duration' => $validated['duration'] ?? 1,
            'max_users' => $validated['max_users'] ?? 0,
            'max_employees' => $validated['max_employees'] ?? 0,
            'storage_limit' => $validated['storage_limit'] ?? 5,
            'nbre_trait' => $validated['nbre_trait'] ?? 1000,
            'description' => $validated['description'] ?? null,
            'features' => $validated['features'] ? (is_array($validated['features']) ? $validated['features'] : explode("\n", $validated['features'])) : null,
            'popular' => $validated['popular'] ?? false,
            'enterprise' => $validated['enterprise'] ?? false,
            'enable_chatgpt' => $validated['enable_chatgpt'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('super-admin.packs.show', $pack)
                        ->with('success', 'Pack mis à jour avec succès');
    }

    /**
     * Supprimer un pack
     */
    public function deletePack(Plan $pack)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier s'il y a des entreprises utilisant ce pack
        if ($pack->company_count > 0) {
            return back()->with('error', 'Impossible de supprimer ce pack car il est utilisé par ' . $pack->company_count . ' entreprise(s).');
        }

        $pack->delete();

        $users = User::active()
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

        return view('super-admin.packs.index', compact('users'));
    }

    /**
     * KPIs financiers globaux: MRR normalisé, ARR, CA encaissé du mois, CA reconnu (accrual) du mois
     */
    private function getFinancialKPIs(): array
    {
        // Plans en cache clé par id
        $plans = Plan::active()->get(['id','price','price_yearly'])->keyBy('id');

        // Entreprises actives avec dates utiles
        $companies = Company::where('is_active', 1)
            ->where(function($q){
                $q->whereNull('subscription_status')->orWhere('subscription_status','active');
            })
            ->get(['plan_id','subscription_start_date','subscription_end_date']);

        $mrr = 0.0;
        $arr = 0.0;
        $accrualMonth = 0.0;

        $periodStart = now()->startOfMonth();
        $periodEnd = now()->endOfMonth();

        foreach ($companies as $c) {
            $plan = $plans->get($c->plan_id);
            if (!$plan) continue;

            // Inférence de périodicité si non stockée: basé sur la durée entre start/end
            $billing = 'monthly';
            if ($c->subscription_start_date && $c->subscription_end_date) {
                try {
                    $days = $c->subscription_end_date->diffInDays($c->subscription_start_date);
                    if ($days >= 335) { // ~11 mois+
                        $billing = 'yearly';
                    }
                } catch (\Throwable $e) {}
            }

            // Prix normalisé
            if ($billing === 'yearly') {
                $yearly = $plan->price_yearly ?? (($plan->price ?? 0) * 12);
                $mrr += ($yearly / 12.0);
            } else {
                $mrr += ($plan->price ?? 0);
            }

            // Accrual du mois courant (pro-rata journalier sur la période d'abonnement)
            if ($c->subscription_start_date && $c->subscription_end_date) {
                $contractValue = $billing === 'yearly'
                    ? ($plan->price_yearly ?? (($plan->price ?? 0) * 12))
                    : ($plan->price ?? 0);

                $serviceStart = $c->subscription_start_date->greaterThan($periodStart) ? $c->subscription_start_date : $periodStart;
                $serviceEnd = $c->subscription_end_date->lessThan($periodEnd) ? $c->subscription_end_date : $periodEnd;
                if ($serviceEnd >= $serviceStart) {
                    try {
                        $totalDays = max(1, $c->subscription_end_date->diffInDays($c->subscription_start_date));
                        $monthDays = $serviceEnd->diffInDays($serviceStart) + 1;
                        $accrualMonth += ($contractValue / $totalDays) * $monthDays;
                    } catch (\Throwable $e) {}
                }
            }
        }

        $arr = $mrr * 12.0;

        // CA encaissé (cash) du mois courant
        $cashMonth = Order::where('status','paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_amount');

        return [
            'mrr' => $mrr,
            'arr' => $arr,
            'cash_month' => $cashMonth,
            'accrual_month' => $accrualMonth,
        ];
    }

    /**
     * Activer/Désactiver un pack
     */
    public function togglePack(Plan $pack)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Basculer le statut du pack
        $pack->update([
            'is_active' => !$pack->is_active
        ]);

        $status = $pack->is_active ? 'activé' : 'désactivé';

        return response()->json([
            'success' => true,
            'message' => "Pack {$status} avec succès",
            'is_active' => $pack->is_active
        ]);
    }

    // ========================================
    // FONCTIONS UTILITAIRES
    // ========================================

    /**
     * Afficher les activités récentes détaillées
     */
    public function activities()
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Récupérer plus d'activités pour la vue détaillée (20 éléments)
        $activities = $this->getRecentActivity();
        $all_activities = [];

        // Activités de connexion (tous les admins)
        $admin_logins = User::where('type', 'super_admin')
            ->latest()
            ->limit(5)
            ->get();

        foreach ($admin_logins as $admin) {
            $all_activities[] = [
                'type' => 'login',
                'title' => 'Connexion Admin',
                'description' => $admin->name . ' s\'est connecté au système',
                'time' => $admin->last_login ? $admin->last_login->diffForHumans() : 'Inconnue',
                'icon' => 'ti-user-check',
                'color' => 'primary'
            ];
        }

        // Tous les employés récents
        $recent_employees = User::employees()
            ->latest()
            ->limit(8)
            ->get();

        foreach ($recent_employees as $employee) {
            $all_activities[] = [
                'type' => 'employee',
                'title' => 'Nouvel Employé',
                'description' => $employee->name . ' a été ajouté(e) au système',
                'time' => $employee->created_at->diffForHumans(),
                'icon' => 'ti-user-plus',
                'color' => 'success'
            ];
        }

        // Toutes les entreprises récentes
        $recent_companies = User::companies()
            ->latest()
            ->limit(8)
            ->get();

        foreach ($recent_companies as $company) {
            $all_activities[] = [
                'type' => 'company',
                'title' => 'Entreprise créée',
                'description' => 'Nouvelle entreprise "' . $company->name . '" ajoutée',
                'time' => $company->created_at->diffForHumans(),
                'icon' => 'ti-building',
                'color' => 'info'
            ];
        }

        // Plans modifiés récemment
        $recent_plans = Plan::latest()
            ->limit(5)
            ->get();

        foreach ($recent_plans as $plan) {
            $all_activities[] = [
                'type' => 'plan',
                'title' => 'Mise à jour Pack',
                'description' => 'Pack ' . $plan->name . ' modifié',
                'time' => $plan->updated_at->diffForHumans(),
                'icon' => 'ti-package',
                'color' => 'warning'
            ];
        }

        // Trier par date (plus récent en premier)
        usort($all_activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        // Limiter à 20 éléments
        $all_activities = array_slice($all_activities, 0, 20);

        return view('super-admin.activities.index', compact('all_activities'));
    }

    /**
     * Support et tickets
     */
    public function support()
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Ici vous pourriez intégrer un système de tickets
        // Pour l'instant, on affiche une vue simple

        return view('super-admin.support');
    }

    // ========================================
    // FONCTIONS GESTION DES RAPPORTS
    // ========================================

    /**
     * Afficher les rapports
     */
    public function reports(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }
        // Filtres
        $metricType = (string) $request->get('metricType', 'all');
        $period = (int) $request->get('period', 30);
        $startDate = $request->get('startDate') ? Carbon::parse($request->get('startDate'))->startOfDay() : Carbon::now()->subDays(max($period,1))->startOfDay();
        $endDate = $request->get('endDate') ? Carbon::parse($request->get('endDate'))->endOfDay() : Carbon::now()->endOfDay();

        // Données agrégées
        // Total revenu basé sur le prix des packs pour les entreprises ACTIVES
        $activeCompanies = Company::where('is_active', 1)->get(['plan_id']);
        $planIds = $activeCompanies->pluck('plan_id')->filter()->unique()->values();
        $plansMap = Plan::whereIn('id', $planIds)->get(['id','price'])->keyBy('id');
        $totalRevenue = (float) $activeCompanies->sum(function($c) use ($plansMap) {
            return (float) ($plansMap[$c->plan_id]->price ?? 0);
        });

        $totalCompanies = $activeCompanies->count();
        $totalUsers = User::count();
        $conversionRate = $totalUsers > 0 ? round(($totalCompanies / max($totalUsers,1)) * 100, 2) : 0;
        // Taux de conversion sur la période sélectionnée (nouvelles entreprises / nouveaux utilisateurs)
        $periodUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $periodCompanies = Company::whereBetween('created_at', [$startDate, $endDate])->count();
        $conversionRatePeriod = $periodUsers > 0 ? round(($periodCompanies / max($periodUsers,1)) * 100, 2) : 0;

        // Deltas (%) vs période précédente
        $periodDays = max($period, 1);
        $prevStart = (clone $startDate)->copy()->subDays($periodDays);
        $prevEnd = (clone $startDate)->copy()->subSecond();

        // Revenu delta basé sur commandes pour refléter du flux réel (si aucune commande, delta 0)
        $revCurrent = (float) Order::whereBetween('created_at', [$startDate, $endDate])->whereIn('status', ['paid','refunded'])->sum('amount');
        $revPrev = (float) Order::whereBetween('created_at', [$prevStart, $prevEnd])->whereIn('status', ['paid','refunded'])->sum('amount');
        $deltaRevenue = $revPrev > 0 ? round((($revCurrent - $revPrev) / $revPrev) * 100, 1) : 0.0;

        $companiesCurrent = Company::whereBetween('created_at', [$startDate, $endDate])->count();
        $companiesPrev = Company::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $deltaCompanies = $companiesPrev > 0 ? round((($companiesCurrent - $companiesPrev) / $companiesPrev) * 100, 1) : 0.0;

        $usersCurrent = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $usersPrev = User::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $deltaUsers = $usersPrev > 0 ? round((($usersCurrent - $usersPrev) / $usersPrev) * 100, 1) : 0.0;

        // Entreprises détaillées (démo)
        $companies = Company::orderBy('created_at','desc')->limit(25)->get();
        $companiesTable = $companies->map(function($c){
            $plan = Plan::find($c->plan_id);
            return [
                'name' => $c->name,
                'email' => $c->email,
                'plan_name' => $plan->name ?? 'N/A',
                'is_active' => (bool) $c->is_active,
                'employee_count' => 0,
                'storage_usage' => 0,
                'storage_used' => 0,
                'created_at' => $c->created_at,
                'revenue' => $plan->price,
                'price_yearly' => $plan->price_yearly,
                'id'=>$c->user_id
            ];
        })->toArray();

        // Top companies (placeholder based on creation recency)
        $topCompanies = collect($companiesTable)->take(5)->toArray();

        // Répartition par plan (pour graphe doughnut)
        $allCompanies = Company::get(['plan_id']);
        $totalForDist = max($allCompanies->count(), 1);
        $planCounts = $allCompanies->groupBy('plan_id')->map->count();
        $planNames = Plan::whereIn('id', $planCounts->keys())->pluck('name','id');
        $planDistribution = [];
        foreach ($planCounts as $pid => $count) {
            $planDistribution[] = [
                'plan' => $planNames[$pid] ?? 'Plan #'.$pid,
                'count' => $count,
                'percentage' => round(($count / $totalForDist) * 100, 1),
            ];
        }

        // Séries temporelles: Revenus par jour (période)
        $labelsRevenue = [];
        $dataRevenue = [];
        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            $labelsRevenue[] = $cursor->format('Y-m-d');
            $sumDay = (float) Order::whereDate('created_at', $cursor->format('Y-m-d'))
                ->whereIn('status', ['paid','refunded'])
                ->sum('amount');
            $dataRevenue[] = round($sumDay, 2);
            $cursor->addDay();
        }

        // Séries Croissance mensuelle: nouvelles entreprises par mois
        $labelsGrowth = [];
        $dataGrowth = [];
        $startMonth = $startDate->copy()->startOfMonth();
        $endMonth = $endDate->copy()->endOfMonth();
        $cursorM = $startMonth->copy();
        while ($cursorM->lte($endMonth)) {
            $labelsGrowth[] = $cursorM->locale('fr_FR')->isoFormat('MMM');
            $countMonth = Company::whereBetween('created_at', [$cursorM->copy()->startOfMonth(), $cursorM->copy()->endOfMonth()])->count();
            $dataGrowth[] = $countMonth;
            $cursorM->addMonth();
        }

        $reportData = [
            'total_revenue' => $totalRevenue,
            'total_companies' => $totalCompanies,
            'total_users' => $totalUsers,
            'conversion_rate' => $conversionRate,
            'conversion_rate_period' => $conversionRatePeriod,
            'delta_revenue' => $deltaRevenue,
            'delta_companies' => $deltaCompanies,
            'delta_users' => $deltaUsers,
            'companies' => $companiesTable,
            'top_companies' => $topCompanies,
            'plan_distribution' => $planDistribution,
            'revenue_series' => [ 'labels' => $labelsRevenue, 'data' => $dataRevenue ],
            'growth_series' => [ 'labels' => $labelsGrowth, 'data' => $dataGrowth ],
        ];

        return view('super-admin.rapports.reports', compact('reportData'));
    }

    // Exports globaux (reports page)
    public function exportReportsXlsx(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }
        $startDate = $request->get('startDate') ? Carbon::parse($request->get('startDate'))->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->get('endDate') ? Carbon::parse($request->get('endDate'))->endOfDay() : Carbon::now()->endOfDay();

        $companies = Company::orderBy('created_at','desc')->get(['name','email','plan_id','is_active','created_at']);
        $filename = 'rapport_entreprises_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $callback = function() use ($companies) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Nom','Email','Plan','Actif','Créé le']);
            foreach ($companies as $c) {
                fputcsv($handle, [
                    $c->name,
                    $c->email,
                    $c->plan_id,
                    $c->is_active ? 'Oui' : 'Non',
                    optional($c->created_at)->format('Y-m-d H:i'),
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportReportsPdf(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }
        $companies = Company::orderBy('created_at','desc')->get();
        $html = view('super-admin.rapports.reports-pdf', compact('companies'))->render();
        if (class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4','portrait');
            return $pdf->download('rapport_entreprises_' . now()->format('Ymd_His') . '.pdf');
        }
        return response($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rapport_entreprises_' . now()->format('Ymd_His') . '.pdf"'
        ]);
    }

    // ========================================
    // FONCTIONS GESTION DES COMMANDES
    // ========================================

    /**
     * Gestion des commandes
     */
    public function commandes(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Filtres GET
        $q = trim((string) $request->get('q'));
        $status = $request->get('status');

        $query = Order::with(['user', 'plan', 'coupon'])->orderBy('created_at', 'desc');
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('id', (int) $q)
                    ->orWhereHas('user', function($u) use ($q) {
                        $u->where('name', 'like', "%$q%")
                          ->orWhere('email', 'like', "%$q%");
                    })
                    ->orWhereHas('plan', function($p) use ($q) {
                        $p->where('name', 'like', "%$q%");
                    });
            });
        }
        if ($status) {
            $query->where('status', $status);
        }

        $commandes = $query->paginate(15)->appends($request->query());

        // Quick stats
        $stats = [
            'total' => Order::count(),
            'paid' => Order::where('status', 'validee')->count(),
            'pending' => Order::where('status', 'pending')->count(),
            'refunded' => Order::where('status', 'refunded')->count(),
            'amount_total' => (float) Order::sum('amount'),
            'today' => Order::whereDate('created_at', Carbon::today())->count(),
        ];

        return view('super-admin.commandes.index', compact('commandes', 'stats'));
    }

    /**
     * Commandes en attente
     */
    public function commandesPending(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $q = trim((string) $request->get('q'));
        $query = Order::with(['user', 'plan', 'coupon'])
                      ->where('status', 'pending')
                      ->orWhere('status', 'paid')
                      ->orderBy('created_at', 'asc');
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('id', (int) $q)
                    ->orWhereHas('user', function($u) use ($q) {
                        $u->where('name', 'like', "%$q%")
                          ->orWhere('email', 'like', "%$q%");
                    })
                    ->orWhereHas('plan', function($p) use ($q) {
                        $p->where('name', 'like', "%$q%");
                    });
            });
        }
        $commandes = $query->paginate(15)->appends($request->query());

        $stats = [
            'pending' => Order::where('status', 'pending')->count(),
            'pending_amount' => (float) Order::where('status', 'pending')->sum('amount'),
            'oldest_pending_at' => optional(Order::where('status','pending')->orderBy('created_at','asc')->first())->created_at,
        ];

        return view('super-admin.commandes.pending', compact('commandes', 'stats'));
    }

    /**
     * Afficher une commande spécifique
     */
    public function showCommande($commandeId)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $commande = Order::with(['user', 'plan', 'coupon'])->findOrFail($commandeId);

        return view('super-admin.commandes.show', compact('commande'));
    }

    /**
     * Mettre à jour une commande
     */
    public function updateCommande(Request $request, Order $commande)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Allow simple approve without payload (default to 'paid')
        $validated = $request->validate([
            'status' => 'nullable|in:pending,paid,cancelled,refunded,expired',
            'notes' => 'nullable|string',
            'payment_reference' => 'nullable|string|max:255',
        ]); 

        $oldStatus = $commande->status;
        $newStatus = $validated['status'] ?? 'paid';

        // Mettre à jour la commande
        $updateData = [
            'status' => $newStatus,
            'notes' => $validated['notes'] ?? $commande->notes,
        ];

        // Ajouter la référence de paiement si fournie
        if (!empty($validated['payment_reference'])) {
            $updateData['payment_reference'] = $validated['payment_reference'];
        }

        // Si le statut passe à "paid", ajouter la date de paiement
        if ($newStatus === 'paid' && $oldStatus !== 'paid') {
            $updateData['paid_at'] = now();
            
            // Activer le compte utilisateur
            if ($commande->user) {
                $entrepriseCommande = $commande->user->company;

                $donneesUtilisateur = [
                    'is_active' => true,
                    'plan' => $commande->plan_id,
                    'plan_expire_date' => now()->addMonth(), // Abonnement d'un mois
                ];

                // company_id n'est renseigné que si l'entreprise existe : sur une
                // commande dont l'utilisateur n'a pas de ligne companies, lire
                // ->company->id provoquait une erreur fatale et la validation de
                // la commande échouait entièrement.
                if ($entrepriseCommande) {
                    $donneesUtilisateur['company_id'] = $entrepriseCommande->id;
                }

                $commande->user->update($donneesUtilisateur);

                // Activer l'entreprise
                if ($entrepriseCommande) {
                    $entrepriseCommande->update([
                        'is_active' => true,
                        'plan_id' => $commande->plan_id,
                        'subscription_start_date' => now(),
                        'subscription_end_date' => now()->addMonth(),
                        'subscription_status' => 'active'
                    ]);
                }

                // Envoyer les notifications de changement de statut
                $this->sendOrderStatusNotification($commande->user, $commande, $oldStatus, $newStatus);  
            }
        }

        $commande->update($updateData);



        return back()->with('success', 'Commande mise à jour avec succès');
    }

    /**
     * Annuler une commande
     */
    public function cancelCommande(Order $commande)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $commande->cancel('Annulée par administrateur');

        return back()->with('success', 'Commande annulée avec succès');
    }

    /**
     * Supprimer une commande
     */
    public function deleteCommande(Order $commande)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $commande->delete();

        return back()->with('success', 'Commande supprimée avec succès');
    }

    // ========================================
    // FONCTIONS GESTION DES COUPONS
    // ========================================

    /**
     * Gestion des coupons
     */
    public function commandesCoupons(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $q = trim((string) $request->get('q'));
        $type = $request->get('type'); // percentage | fixed_amount
        $isActive = $request->get('is_active'); // '1' | '0'
        $expiring = $request->get('expiring'); // 'soon'

        $cquery = Coupon::with('creator')->orderBy('created_at', 'desc');
        if ($q !== '') {
            $cquery->where(function($sub) use ($q) {
                $sub->where('code', 'like', "%$q%")
                    ->orWhere('name', 'like', "%$q%");
            });
        }
        if ($type) { $cquery->where('type', $type); }
        if ($isActive !== null && $isActive !== '') { $cquery->where('is_active', (bool)$isActive); }
        if ($expiring === 'soon') {
            $now = Carbon::now();
            $cquery->whereNotNull('expires_at')->whereBetween('expires_at', [$now, $now->copy()->addDays(7)]);
        }

        $coupons = $cquery->paginate(15)->appends($request->query());
        $now = Carbon::now();
        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('is_active', true)->count(),
            'inactive' => Coupon::where('is_active', false)->count(),
            'expiring_soon' => Coupon::whereNotNull('expires_at')->whereBetween('expires_at', [$now, $now->copy()->addDays(7)])->count(),
        ];

        return view('super-admin.commandes.coupons.index', compact('coupons', 'stats'));
    }

    /**
     * Créer un nouveau coupon
     */
    public function storeCoupon(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'expires_at' => 'nullable|date|after:today',
            'is_active' => 'boolean',
            'applicable_plans' => 'nullable|array',
        ]);

        $coupon = Coupon::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'value' => $validated['value'],
            'min_amount' => $validated['min_amount'] ?? 0,
            'max_uses' => $validated['max_uses'] ?? 0,
            'used_count' => 0,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => isset($validated['is_active']) ? (bool)$validated['is_active'] : true,
            'created_by' => Auth::id(),
            'applicable_plans' => $validated['applicable_plans'] ?? null,
        ]);

        return redirect()->route('super-admin.commandes.coupons.index')->with('success', 'Coupon créé avec succès');
    }

    /**
     * Afficher un coupon spécifique
     */
    public function showCoupon(Coupon $coupon)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        return view('super-admin.commandes.coupons.show', compact('coupon'));
    }

    /**
     * Afficher le formulaire d'édition d'un coupon
     */
    public function editCoupon(Coupon $coupon)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $plans = Plan::active()->get();

        return view('super-admin.commandes.coupons.edit', compact('coupon', 'plans'));
    }

    /**
     * Mettre à jour un coupon
     */
    public function updateCoupon(Request $request, Coupon $coupon)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id . '|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'expires_at' => 'nullable|date|after:today',
            'is_active' => 'boolean',
            'applicable_plans' => 'nullable|array',
        ]);

        $coupon->update([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'value' => $validated['value'],
            'min_amount' => $validated['min_amount'] ?? 0,
            'max_uses' => $validated['max_uses'] ?? 0,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'applicable_plans' => $validated['applicable_plans'] ?? null,
        ]);

        return redirect()->route('super-admin.commandes.coupons.index')->with('success', 'Coupon mis à jour avec succès');
    }

    /**
     * Supprimer un coupon
     */
    public function deleteCoupon(Coupon $coupon)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $coupon->delete();

        return redirect()->route('super-admin.commandes.coupons.index')->with('success', 'Coupon supprimé avec succès');
    }

    /**
     * Activer/Désactiver un coupon
     */
    public function toggleCoupon(Coupon $coupon)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $coupon->update([
            'is_active' => !$coupon->is_active
        ]);

        return back()->with('success', 'Statut du coupon mis à jour');
    }

    // ========================================
    // FONCTIONS GESTION DES SECTEURS D'ACTIVITES
    // ========================================

    /**
     * Lister les secteurs d'activité
     */
    public function sectors()
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $sectors = Sector::ordered()->paginate(15);

        // Statistiques secteurs
        $stats = [
            'total' => Sector::count(),
            'active' => Sector::where('is_active', true)->count(),
            'inactive' => Sector::where('is_active', false)->count(),
        ];

        // Top secteurs avec le plus d'entreprises (si relation companies existe)
        $topSectors = Sector::withCount('companies')
            ->orderByDesc('companies_count')
            ->take(1)
            ->get();

        return view('super-admin.sectors.index', compact('sectors', 'stats', 'topSectors'));
    }

    public function createSector()
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        return view('super-admin.sectors.create');
    }

    /**
     * Créer un nouveau secteur
     */
    public function storeSector(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:sectors,name',
            'description' => 'nullable|string',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $sector = Sector::create([
            'name' => $validated['name'],
            'slug' => \Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->back()->with('success', 'Secteur créé avec succès');
    }

    /**
     * Basculer le statut d'un secteur
     */
    public function toggleSector(Request $request, Sector $sector)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $sector->update([
            'is_active' => !$sector->is_active
        ]);

        return redirect()->back()->with('success', 'Secteur ' . ($sector->is_active ? 'activé' : 'désactivé') . ' avec succès');
    }

    /**
     * Supprimer un secteur
     */
    public function deleteSector(Sector $sector)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier s'il y a des entreprises utilisant ce secteur
        if ($sector->companies_count > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer ce secteur car il est utilisé par ' . $sector->companies_count . ' entreprise(s).');
        }

        $sector->delete();

        return redirect()->back()->with('success', 'Secteur supprimé avec succès');
    }

    /**
     * Afficher le formulaire de modification d'un secteur
     */
    public function editSector(Sector $sector)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        return view('super-admin.sectors.edit', compact('sector'));
    }

    /**
     * Mettre à jour un secteur
     */
    public function updateSector(Request $request, Sector $sector)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:sectors,name,' . $sector->id,
            'description' => 'nullable|string',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $sector->update($validated);

        return redirect()->back()->with('success', 'Secteur mis à jour avec succès');
    }

    /**
     * Exporter les secteurs au format Excel (XLSX)
     */
    public function exportSectors(Request $request)
    {
        try {
            // Vérification de l'autorisation
            if (!Auth::user() || Auth::user()->type !== 'super_admin') {
                abort(403, 'Accès non autorisé');
            }

            // Récupération des filtres
            $search = $request->get('search');
            $status = $request->get('status');

            // Requête avec filtres
            $query = Sector::query();

            // Filtrage par recherche
            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            }

            // Filtrage par statut
            if ($status !== null) {
                $query->where('is_active', $status);
            }

            // Récupération des secteurs
            $sectors = $query->get();

            // Génération du fichier Excel
            $filename = 'secteurs_export_' . date('Y-m-d_H-i-s') . '.xls';
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $callback = function() use ($sectors) {
                // Ouvrir le flux de sortie
                $handle = fopen('php://output', 'w');

                // Écrire une marque BOM UTF-8 pour Excel
                fwrite($handle, "\xEF\xBB\xBF");

                // Informations d'en-tête du fichier
                fputcsv($handle, ['=== EXPORT DES SECTEURS D\'ACTIVITÉ ===']);
                fputcsv($handle, ['Généré le:', date('d/m/Y à H:i:s')]);
                fputcsv($handle, ['Par:', Auth::user()->name]);
                fputcsv($handle, ['Total des secteurs:', $sectors->count()]);
                fputcsv($handle, []); // Ligne vide

                // En-têtes du tableau
                fputcsv($handle, ['N°', 'Secteur d\'Activité', 'Description', 'Nombre d\'Entreprises', 'Statut', 'Créé le', 'Modifié le']);

                // Remplir le tableau avec les données des secteurs
                foreach ($sectors as $sector) {
                    fputcsv($handle, [
                        $sector->sort_order,
                        $sector->name,
                        $sector->description ?? '',
                        $sector->companies_count ?? 0,
                        $sector->is_active ? 'Actif' : 'Inactif',
                        $sector->created_at ? $sector->created_at->format('d/m/Y H:i:s') : '',
                        $sector->updated_at ? $sector->updated_at->format('d/m/Y H:i:s') : '',
                    ]);
                }

                // Résumé en fin de fichier
                fputcsv($handle, []); // Ligne vide
                fputcsv($handle, ['=== RÉSUMÉ ===']);
                fputcsv($handle, ['Secteurs actifs:', $sectors->where('is_active', true)->count()]);
                fputcsv($handle, ['Secteurs inactifs:', $sectors->where('is_active', false)->count()]);
                fputcsv($handle, ['Total entreprises dans tous secteurs:', $sectors->sum('companies_count')]);

                // Fermer le flux
                fclose($handle);
            };

            return Response::stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de l\'export: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    
    /**
     * Exporter les secteurs au format PDF
     */
    public function exportSectorsPdf(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Récupérer les paramètres de filtrage
        $search = $request->get('search');
        $status = $request->get('status');

        // Construire la requête
        $query = Sector::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $sectors = $query->orderBy('sort_order')->orderBy('name')->get();

        // Créer le contenu HTML pour le PDF
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Export des Secteurs</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; font-weight: bold; }
                .header { text-align: center; margin-bottom: 20px; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Liste des Secteurs d'Activité</h1>
                <p>Généré le " . date('d/m/Y à H:i:s') . "</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Ordre</th>
                        <th>Entreprises</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($sectors as $sector) {
            $html .= "
                    <tr>
                        <td>{$sector->id}</td>
                        <td>{$sector->name}</td>
                        <td>{$sector->description}</td>
                        <td>" . ($sector->is_active ? 'Actif' : 'Inactif') . "</td>
                        <td>{$sector->sort_order}</td>
                        <td>{$sector->companies_count}</td>
                    </tr>";
        }

        $html .= "
                </tbody>
            </table>

            <div class='footer'>
                <p>Total: {$sectors->count()} secteur(s) | RHFLOW - Système de Gestion</p>
            </div>
        </body>
        </html>";

        // Générer le PDF avec DomPDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'secteurs_export_' . date('Y-m-d_H-i-s') . '.pdf';

        return $dompdf->stream($filename, [
            'Attachment' => true
        ]);
    }

    /**
     * Afficher les détails d'un secteur
     */
    public function showSector(Sector $sector)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Charger les entreprises liées à ce secteur
        $sector->load(['companies' => function($query) {
            $query->select('id', 'name', 'email', 'phone', 'city', 'industry', 'created_at')
                  ->orderBy('name');
        }]);

        return view('super-admin.sectors.show', compact('sector'));
    }

    /**
     * Récupérer la période de facturation actuelle
     */
    private function getCurrentBillingPeriod($company)
    {
        if (!$company->subscription_start_date || !$company->subscription_end_date) {
            return 'Non définie';
        }

        $start = $company->subscription_start_date;
        $end = $company->subscription_end_date;

        $diffInDays = $start->diffInDays($end);

        if ($diffInDays >= 330 && $diffInDays <= 370) { // Environ 1 an
            return 'Annuel';
        } elseif ($diffInDays >= 28 && $diffInDays <= 32) { // Environ 1 mois
            return 'Mensuel';
        } else {
            return $diffInDays . ' jours';
        }
    }

    /**
     * Récupérer la date du prochain renouvellement
     */
    private function getNextRenewalDate($company)
    {
        if (!$company->subscription_end_date) {
            return null;
        }

        $endDate = $company->subscription_end_date;

        if (now()->isAfter($endDate)) {
            return 'Expiré depuis le ' . $endDate->format('d/m/Y');
        }

        return $endDate->format('d/m/Y à H:i');
    }


    // ========================================
    // FONCTIONS GESTION DES SETTINGS
    // ========================================

    /**
     * Afficher les paramètres système
     */
    public function settings()
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        // Initialiser les paramètres par défaut s'ils n'existent pas
        //Setting::initializeDefaults();

        // Récupérer tous les paramètres groupés
        $settings = [
            'general' => Setting::getGroup('general'),
            'email' => Setting::getGroup('email'),
            'security' => Setting::getGroup('security'),
            'backup' => Setting::getGroup('backup'),
        ];

        // Debug temporaire - voir les paramètres récupérés
        \Log::info('Settings retrieved for display', [
            'general' => $settings['general'],
            'email' => $settings['email'],
            'security' => $settings['security'],
            'backup' => $settings['backup'],
        ]);

        // Récupérer la liste des devises disponibles
        $currencies = [
            'XOF' => ['symbol' => 'CFA', 'name' => 'Franc CFA (XOF)'],
            'EUR' => ['symbol' => '€', 'name' => 'Euro'],
            'USD' => ['symbol' => '$', 'name' => 'Dollar américain'],
            'GBP' => ['symbol' => '£', 'name' => 'Livre sterling'],
            'CAD' => ['symbol' => 'C$', 'name' => 'Dollar canadien'],
            'CHF' => ['symbol' => 'CHF', 'name' => 'Franc suisse'],
            'JPY' => ['symbol' => '¥', 'name' => 'Yen japonais'],
            'CNY' => ['symbol' => '¥', 'name' => 'Yuan chinois'],
            'INR' => ['symbol' => '₹', 'name' => 'Roupie indienne'],
            'BRL' => ['symbol' => 'R$', 'name' => 'Réal brésilien'],
        ];

        return view('super-admin.settings.index', compact('settings', 'currencies'));
    }

    /*
     * Mettre à jour les paramètres système
    */
    public function updateSettings(Request $request)
    {
        try {
            // Valider les données du formulaire
            $validatedData = $request->validate([
                // Configuration générale
                'app_name' => 'required|string|max:255',
                'app_url' => 'required|url|max:255',
                'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'timezone' => 'required|string|max:50',
                'default_language' => 'required|string|max:10',
                'maintenance_mode' => 'boolean',
                'maintenance_message' => 'nullable|string|max:500',
                
                // Configuration email
                'mail_driver' => 'required|string|max:50',
                'mail_host' => 'required|string|max:255',
                'mail_port' => 'required|integer|min:1|max:65535',
                'mail_username' => 'nullable|string|max:255',
                'mail_password' => 'nullable|string|max:255',
                'mail_encryption' => 'nullable|string|max:10',
                'mail_from_address' => 'required|email|max:255',
                'mail_from_name' => 'required|string|max:255',
                
                // Sécurité
                'session_lifetime' => 'required|integer|min:5|max:1440',
                'password_expiry_days' => 'required|integer|min:0|max:365',
                'max_login_attempts' => 'required|integer|min:1|max:10',
                'lockout_duration' => 'required|integer|min:1|max:60',
                'require_email_verification' => 'boolean',
                
                // Devise
                'currency' => 'required|string|max:10',
                
                // Sauvegarde
                'backup_frequency' => 'required|string|in:daily,weekly,monthly',
                'retention_days' => 'required|integer|min:1|max:365',
            ]);
    
            // Traitement du logo
            if ($request->hasFile('app_logo')) {
                $logoFile = $request->file('app_logo');
                $logoName = 'logo-' . time() . '.' . $logoFile->getClientOriginalExtension();
                
                // Créer le dossier s'il n'existe pas
                $logoPath = public_path('storage/logos');
                if (!file_exists($logoPath)) {
                    mkdir($logoPath, 0755, true);
                }
                
                // Déplacer le fichier
                $logoFile->move($logoPath, $logoName);
                
                // Sauvegarder le chemin du logo
                Setting::set('app_logo', $logoName, 'string', 'general', 'Logo de l\'application', 'Logo affiché dans l\'application');
            }
    
            // Sauvegarder les paramètres généraux
            Setting::set('app_name', $validatedData['app_name'], 'string', 'general', 'Nom de l\'application', 'Nom affiché dans l\'application');
            Setting::set('app_url', $validatedData['app_url'], 'string', 'general', 'URL de l\'application', 'URL principale de l\'application');
            Setting::set('timezone', $validatedData['timezone'], 'string', 'general', 'Fuseau horaire', 'Fuseau horaire par défaut');
            Setting::set('default_language', $validatedData['default_language'], 'string', 'general', 'Langue par défaut', 'Langue par défaut de l\'application');
            Setting::set('maintenance_mode', $validatedData['maintenance_mode'] ?? false, 'boolean', 'general', 'Mode maintenance', 'Activer le mode maintenance pour bloquer l\'accès aux utilisateurs');
            Setting::set('maintenance_message', $validatedData['maintenance_message'] ?? '', 'string', 'general', 'Message de maintenance', 'Message affiché pendant la maintenance');
    
            // Sauvegarder les paramètres email
            Setting::set('mail_driver', $validatedData['mail_driver'], 'string', 'email', 'Driver email', 'Méthode d\'envoi des emails');
            Setting::set('mail_host', $validatedData['mail_host'], 'string', 'email', 'Serveur SMTP', 'Serveur SMTP pour l\'envoi des emails');
            Setting::set('mail_port', $validatedData['mail_port'], 'integer', 'email', 'Port SMTP', 'Port du serveur SMTP');
            Setting::set('mail_username', $validatedData['mail_username'] ?? '', 'string', 'email', 'Utilisateur SMTP', 'Nom d\'utilisateur SMTP');
            
            // Ne mettre à jour le mot de passe que s'il est fourni
            if (!empty($validatedData['mail_password'])) {
                Setting::set('mail_password', $validatedData['mail_password'], 'string', 'email', 'Mot de passe SMTP', 'Mot de passe SMTP');
            }
            
            Setting::set('mail_encryption', $validatedData['mail_encryption'] ?? '', 'string', 'email', 'Chiffrement SMTP', 'Type de chiffrement SMTP');
            Setting::set('mail_from_address', $validatedData['mail_from_address'], 'string', 'email', 'Adresse expéditeur', 'Adresse email d\'expédition');
            Setting::set('mail_from_name', $validatedData['mail_from_name'], 'string', 'email', 'Nom expéditeur', 'Nom affiché comme expéditeur');
    
            // Sauvegarder les paramètres de sécurité
            Setting::set('session_lifetime', $validatedData['session_lifetime'], 'integer', 'security', 'Durée de session', 'Durée de vie des sessions en minutes');
            Setting::set('password_expiry_days', $validatedData['password_expiry_days'], 'integer', 'security', 'Expiration mot de passe', 'Nombre de jours avant expiration des mots de passe');
            Setting::set('max_login_attempts', $validatedData['max_login_attempts'], 'integer', 'security', 'Tentatives de connexion', 'Nombre maximum de tentatives de connexion');
            Setting::set('lockout_duration', $validatedData['lockout_duration'], 'integer', 'security', 'Durée de blocage', 'Durée de blocage en minutes après tentatives échouées');
            Setting::set('require_email_verification', $validatedData['require_email_verification'] ?? false, 'boolean', 'security', 'Vérification email obligatoire', 'Exiger la vérification des emails');
    
            // Sauvegarder les paramètres de sauvegarde
            Setting::set('backup_frequency', $validatedData['backup_frequency'], 'string', 'backup', 'Fréquence de sauvegarde', 'Fréquence des sauvegardes automatiques');
            Setting::set('retention_days', $validatedData['retention_days'], 'integer', 'backup', 'Rétention des sauvegardes', 'Nombre de jours de rétention des sauvegardes');
    
            // Mettre à jour la devise de l'utilisateur connecté si nécessaire
            if (isset($validatedData['currency'])) {
                auth()->user()->update(['currency' => $validatedData['currency']]);
            }
    
            // Mettre à jour la configuration Laravel en temps réel
            $this->updateRuntimeConfig();
    
            return redirect()->route('super-admin.settings.index')->with('success', 'Paramètres mis à jour avec succès!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // #settingsForm est un formulaire HTML classique : repondre en JSON
            // affichait le JSON brut dans le navigateur au lieu du formulaire.
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->validator);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour des paramètres: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour des paramètres: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour des paramètres : ' . $e->getMessage());
        }
    }
    
    /**
     * Mettre à jour la configuration Laravel en temps réel
     */
    private function updateRuntimeConfig()
    {
        try {
            config([
                'app.name' => Setting::get('app_name', 'RH Flow'),
                'app.url' => Setting::get('app_url', config('app.url')),
                'app.timezone' => Setting::get('timezone', 'Europe/Paris'),
                'app.locale' => Setting::get('default_language', 'fr'),
            ]);

            // Mettre à jour la configuration email
            config([
                'mail.default' => Setting::get('mail_driver', 'smtp'),
                'mail.mailers.smtp.host' => Setting::get('mail_host', 'smtp.gmail.com'),
                'mail.mailers.smtp.port' => Setting::get('mail_port', 587),
                'mail.mailers.smtp.username' => Setting::get('mail_username', ''),
                'mail.mailers.smtp.password' => Setting::get('mail_password', ''),
                'mail.mailers.smtp.encryption' => Setting::get('mail_encryption', 'tls'),
                'mail.from.address' => Setting::get('mail_from_address', 'rhflow@dc-knowing.com'),
                'mail.from.name' => Setting::get('mail_from_name', 'RH Flow'),
            ]);

            // Mettre à jour la configuration de session
            config([
                'session.lifetime' => Setting::get('session_lifetime', 120),
            ]);
        } 
        catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour de la configuration runtime: ' . $e->getMessage());
        }
    }
    
    /**
     * Fonction pour tester la configuration email
     */
    public function testEmailConfiguration(Request $request)
    {
        try {
            // Récupérer les paramètres email actuels
            $mailConfig = [
                'driver' => Setting::get('mail_driver', 'smtp'),
                'host' => Setting::get('mail_host', 'smtp.gmail.com'),
                'port' => Setting::get('mail_port', 587),
                'username' => Setting::get('mail_username', ''),
                'password' => Setting::get('mail_password', ''),
                'encryption' => Setting::get('mail_encryption', 'tls'),
                'from' => [
                    'address' => Setting::get('mail_from_address', 'rhflow@dc-knowing.com'),
                    'name' => Setting::get('mail_from_name', 'RH Flow'),
                ],
            ];
    
            // Configurer temporairement le mail
            config([
                'mail.default' => $mailConfig['driver'],
                'mail.mailers.smtp.host' => $mailConfig['host'],
                'mail.mailers.smtp.port' => $mailConfig['port'],
                'mail.mailers.smtp.username' => $mailConfig['username'],
                'mail.mailers.smtp.password' => $mailConfig['password'],
                'mail.mailers.smtp.encryption' => $mailConfig['encryption'],
                'mail.from.address' => $mailConfig['from']['address'],
                'mail.from.name' => $mailConfig['from']['name'],
            ]);
    
            // Tester l'envoi d'email
            \Mail::raw('Test de configuration email - RH Flow', function ($message) {
                $message->to(auth()->user()->email)
                        ->subject('Test de configuration email');
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Email de test envoyé avec succès! Vérifiez votre boîte de réception.'
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Réinitialiser les paramètres aux valeurs par défaut
     */
    public function resetSettings(Request $request)
    {
        try {
            // Réinitialiser les paramètres par défaut
            Setting::initializeDefaults();
    
            return response()->json([
                'success' => true,
                'message' => 'Paramètres réinitialisés avec succès!'
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la réinitialisation des paramètres: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réinitialisation des paramètres: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher le profil du Super Admin
     */
    public function profile(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();

        if ($request->isMethod('post')) {
            // Traitement de la mise à jour du profil
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'username' => 'required|string|unique:users,username,' . $user->id,
                'current_password' => 'nullable|required_with:password',
                'password' => 'nullable|min:8|confirmed',
                'avatar' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:2048',
            ]);

            try {
                // Vérifier le mot de passe actuel si changement de mot de passe
                if ($request->filled('password')) {
                    if (!Hash::check($validated['current_password'], $user->password)) {
                        return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
                    }
                }

                // Gestion de l'avatar
                if ($request->hasFile('avatar')) {
                    $avatar = $request->file('avatar');
                    if ($avatar->isValid()) {
                        // Supprimer l'ancien fichier : $user->avatar est le chemin
                        // stocke, alors que $user->avatar_url est un accesseur qui
                        // renvoie une URL complete et ne correspond a aucun fichier.
                        $ancien = $user->getRawOriginal('avatar');
                        if ($ancien && file_exists(public_path($ancien))) {
                            unlink(public_path($ancien));
                        }

                        // Nouveau nom d'avatar
                        $avatarName = 'super-admin-' . time() . '.' . $avatar->getClientOriginalExtension();
                        $avatar->move(public_path('storage/avatars'), $avatarName);
                        $validated['avatar'] = 'storage/avatars/' . $avatarName;
                    }
                }

                // current_password ne doit pas partir en base, et un mot de passe
                // vide effacerait celui du compte (le cast 'hashed' laisse passer
                // null) : on ne garde la cle que si un nouveau mot de passe est saisi.
                unset($validated['current_password']);

                if (empty($validated['password'])) {
                    unset($validated['password']);
                }

                // Mise à jour du profil
                $user->update($validated);

                return back()->with('success', 'Profil mis à jour avec succès!');

            } catch (\Exception $e) {
                \Log::error('Erreur lors de la mise à jour du profil: ' . $e->getMessage());
                return back()->withErrors(['error' => 'Erreur lors de la mise à jour du profil: ' . $e->getMessage()]);
            }
        }

        // Affichage du formulaire de profil
        return view('super-admin.profile.index', compact('user'));
    }

    /**
     * Système de recherche global pour le Super Admin
     */
    public function search(Request $request)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $query = trim((string) $request->get('q', ''));
        $type = (string) $request->get('type', 'all');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Le terme de recherche doit contenir au moins 2 caractères'
            ]);
        }

        $results = [];

        // Recherche dans les utilisateurs
        if ($type === 'all' || $type === 'users') {
            $users = User::where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('email', 'like', "%$query%")
                  ->orWhere('username', 'like', "%$query%");
            })
            ->whereIn('type', ['company', 'hr', 'paie', 'payroll', 'employee'])
            ->limit(10)
            ->get();

            foreach ($users as $user) {
                $results[] = [
                    'type' => 'user',
                    'id' => $user->id,
                    'title' => $user->name,
                    'subtitle' => $user->email . ' (' . ucfirst($user->type) . ')',
                    'url' => route('super-admin.users.show', $user),
                    'icon' => 'ti-user',
                    'color' => 'primary'
                ];
            }
        }

        // Recherche dans les entreprises
        if ($type === 'all' || $type === 'enterprises') {
            $enterprises = User::where('type', 'company')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%$query%")
                      ->orWhere('email', 'like', "%$query%");
                })
                ->limit(10)
                ->get();

            foreach ($enterprises as $enterprise) {
                $results[] = [
                    'type' => 'enterprise',
                    'id' => $enterprise->id,
                    'title' => $enterprise->name,
                    'subtitle' => $enterprise->email . ' (Entreprise)',
                    'url' => route('super-admin.enterprises.show', $enterprise),
                    'icon' => 'ti-building',
                    'color' => 'info'
                ];
            }
        }

        // Recherche dans les packs
        if ($type === 'all' || $type === 'packs') {
            $packs = Plan::where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('description', 'like', "%$query%");
            })
            ->limit(10)
            ->get();

            foreach ($packs as $pack) {
                $results[] = [
                    'type' => 'pack',
                    'id' => $pack->id,
                    'title' => $pack->name,
                    'subtitle' => $pack->description ?? 'Pack d\'abonnement',
                    'url' => route('super-admin.packs.show', $pack),
                    'icon' => 'ti-package',
                    'color' => 'success'
                ];
            }
        }

        // Recherche dans les secteurs
        if ($type === 'all' || $type === 'sectors') {
            $sectors = Sector::where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('description', 'like', "%$query%");
            })
            ->limit(10)
            ->get();

            foreach ($sectors as $sector) {
                $results[] = [
                    'type' => 'sector',
                    'id' => $sector->id,
                    'title' => $sector->name,
                    'subtitle' => $sector->description ?? 'Secteur d\'activité',
                    'url' => route('super-admin.sectors.show', $sector),
                    'icon' => 'ti-building',
                    'color' => 'warning'
                ];
            }
        }

        // Recherche dans les commandes
        if ($type === 'all' || $type === 'orders') {
            $orders = Order::where(function ($q) use ($query) {
                $q->where('id', 'like', "%$query%")
                  ->orWhere('payment_reference', 'like', "%$query%");
            })
            ->limit(10)
            ->get();

            foreach ($orders as $order) {
                $results[] = [
                    'type' => 'order',
                    'id' => $order->id,
                    'title' => 'Commande #' . $order->id,
                    'subtitle' => $order->reference ?? 'Référence: ' . $order->id,
                    'url' => route('super-admin.commandes.show', $order),
                    'icon' => 'ti-shopping-cart',
                    'color' => 'danger'
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => count($results)
        ]);
    }

    // ========================================
    // FONCTIONS GESTION DES MODULES
    // ========================================

    /**
     * Afficher la gestion des modules pour un pack
     */
    public function packModules(Plan $pack)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $allModules = Module::all();
        $packModules = $pack->modules()->withPivot('is_active')->get();

        return view('super-admin.packs.modules', compact('pack', 'allModules', 'packModules'));
    }

    /**
     * Attacher/détacher des modules à un pack
     */
    public function updatePackModules(Request $request, Plan $pack)
    {
        if (!Auth::user() || Auth::user()->type !== 'super_admin') {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'modules' => 'array',
            'modules.*' => 'exists:modules,id',
        ]);

        // Détacher tous les modules existants
        $pack->modules()->detach();

        // Attacher les nouveaux modules avec is_active = true
        if (isset($validated['modules'])) {
            foreach ($validated['modules'] as $moduleId) {
                $pack->modules()->attach($moduleId, ['is_active' => true]);
            }
        }

        return redirect()->route('super-admin.packs.modules', $pack)->with('success', 'Modules mis à jour avec succès');
    }

    /**
     * Envoyer les notifications de changement de statut de commande
     */
    private function sendOrderStatusNotification(User $user, Order $order, string $oldStatus, string $newStatus)
    {
        try {
            // Ne pas envoyer de notification si le statut n'a pas changé
            if ($oldStatus === $newStatus) {
                return;
            }

            $user = $order->user;
            if (!$user) {
                return;
            }

            $emailBody = $this->buildStatusChangeEmail($user, $order, $oldStatus, $newStatus);
            
            Mail::html($emailBody, function ($message) use ($user, $order) {
                $message->to($user->email)
                    ->subject('Mise à jour de votre commande RHFlow - #' . $order->order_number)
                    ->from('rhflow@dc-knowing.com', 'RHFlow');
            });

            Log::info("Email de statut envoyé pour commande #{$order->id}: {$oldStatus} → {$newStatus}");

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de l'email de statut pour commande #{$order->id}: " . $e->getMessage());
        }
    }

    /**
     * Construire l'email de changement de statut
     */
    private function buildStatusChangeEmail(User $user, Order $order, string $oldStatus, string $newStatus): string
    {
        $statusColors = [
            'pending' => '#ffc107',
            'paid' => '#28a745',
            'cancelled' => '#dc3545',
            'refunded' => '#17a2b8',
            'expired' => '#6c757d',
            'validee' => '#03711dff',
        ];

        $statusLabels = [
            'pending' => 'En attente de paiement',
            'paid' => 'Payée et activée',
            'cancelled' => 'Annulée',
            'refunded' => 'Remboursée',
            'expired' => 'Expirée',
            'validee' => 'Validée'
        ];

        $color = $statusColors[$newStatus] ?? '#6c757d';
        $label = $statusLabels[$newStatus] ?? $newStatus;

        $html = '<html><body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">';
        
        // Header
        $headerBg = $newStatus === 'paid' ? 'linear-gradient(135deg, #28a745 0%, #20c997 100%)' : 
                   ($newStatus === 'cancelled' ? 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)' : 
                   'linear-gradient(135deg, #007bff 0%, #0056b3 100%)');
        
        $html .= '<div style="background: ' . $headerBg . '; padding: 30px; text-align: center; color: white;">';
        $html .= '<h1 style="margin: 0; font-size: 28px;">' . ($newStatus === 'paid'|| $newStatus === 'validee' ? '🎉' : ($newStatus === 'cancelled' ? '❌' : '📢')) . ' Mise à jour de votre commande</h1>';
        $html .= '<p style="margin: 10px 0 0 0; opacity: 0.9;">Commande #' . $order->order_number . '</p>';
        $html .= '</div>';
        
        $html .= '<div style="padding: 30px; background: #f8f9fa;">';
        $html .= '<h2 style="color: #333; margin-bottom: 20px;">📋 Détails de la mise à jour</h2>';
        
        // Changement de statut
        $html .= '<div style="background: white; border-left: 4px solid ' . $color . '; padding: 15px; margin-bottom: 20px; border-radius: 0 8px 8px 0;">';
        $html .= '<p style="margin: 0 0 10px 0; font-size: 14px; color: #666;">Changement de statut:</p>';
        $html .= '<div style="display: flex; align-items: center; gap: 10px;">';
        $html .= '<span style="background: #e9ecef; padding: 4px 8px; border-radius: 4px; font-size: 12px;">' . ($statusLabels[$oldStatus] ?? $oldStatus) . '</span>';
        $html .= '<i class="ri-arrow-right-line"></i>';
        $html .= '<span style="background: ' . $color . '; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">' . $label . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Détails de la commande
        $html .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">';
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Plan:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($order->plan->name) . '</td></tr>';
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Montant:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">' . number_format($order->total_amount, 0, ',', ' ') . ' FCFA</td></tr>';
        if ($order->payment_reference) {
            $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Référence:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($order->payment_reference) . '</td></tr>';
        }
        if ($order->paid_at) {
            $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Date de paiement:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">' . $order->paid_at->format('d/m/Y H:i') . '</td></tr>';
        }
        $html .= '</table>';
        
        // Message spécifique selon le statut
        if ($newStatus === 'paid') {
            $html .= '<div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">';
            $html .= '<h3 style="color: #155724; margin-top: 0;">✅ Votre compte est maintenant actif!</h3>';
            $html .= '<p style="margin-bottom: 15px;">Félicitations! Votre paiement a été validé et votre compte RHFlow est maintenant activé.</p>';
            $html .= '<p style="margin-bottom: 15px;">Vous pouvez maintenant:</p>';
            $html .= '<ul style="margin: 0; padding-left: 20px;">';
            $html .= '<li>Vous connecter à votre espace RHFlow</li>';
            $html .= '<li>Configurer votre entreprise</li>';
            $html .= '<li>Ajouter vos employés</li>';
            $html .= '<li>Générer vos bulletins de paie</li>';
            $html .= '</ul>';
            $html .= '</div>';
            
            $html .= '<div style="text-align: center; margin-top: 30px;">';
            $html .= '<a href="' . route('login') . '" style="background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Me connecter</a>';
            $html .= '</div>';
            
        } elseif ($newStatus === 'cancelled') {
            $html .= '<div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">';
            $html .= '<h3 style="color: #721c24; margin-top: 0;">❌ Commande annulée</h3>';
            $html .= '<p>Votre commande a été annulée. Si vous pensez qu\'il s\'agit d\'une erreur, veuillez contacter notre support.</p>';
            $html .= '</div>';
            
        } else {
            $html .= '<div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">';
            $html .= '<h3 style="color: #0c5460; margin-top: 0;">📢 Mise à jour de votre commande</h3>';
            $html .= '<p>Le statut de votre commande a été mis à jour. Vous pouvez consulter les détails dans votre espace client.</p>';
            $html .= '</div>';
        }
        
        if ($order->notes) {
            $html .= '<div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin-bottom: 20px;">';
            $html .= '<h4 style="color: #856404; margin-top: 0;">📝 Notes:</h4>';
            $html .= '<p style="margin: 0;">' . nl2br(htmlspecialchars($order->notes)) . '</p>';
            $html .= '</div>';
        }

        // Paramètres de connexion
        if ($newStatus === 'validee') {
            $html .= '<div style="background: #e8f5e8; border: 1px solid #28a745; border-radius: 8px; padding: 20px; margin-bottom: 20px;">';
            $html .= '<h3 style="color: #155724; margin-top: 0;">🔐 Vos paramètres de connexion</h3>';
            $html .= '<div style="background: white; padding: 15px; border-radius: 5px; margin: 15px 0;">';
            $html .= '<p style="margin: 5px 0;"><strong>Email:</strong> ' . htmlspecialchars($user->email) . '</p>';
            $html .= '<p style="margin: 5px 0;"><strong>Nom d\'utilisateur:</strong> ' . htmlspecialchars($user->username) . '</p>';
            $html .= '<p style="margin: 5px 0;"><strong>Mot de passe:</strong> Celui que vous avez défini lors de l\'inscription</p>';
            $html .= '</div>';
            $html .= '<div style="text-align: center; margin-top: 20px;">';
            $html .= '<a href="' . route('login') . '" style="background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">🚀 Me connecter maintenant</a>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        $html .= '<div style="background: #333; color: white; padding: 20px; text-align: center; font-size: 12px;">';
        $html .= '<p style="margin: 0;">© 2024 RHFlow - Solution de gestion de paie</p>';
        $html .= '<p style="margin: 5px 0 0 0;">Cet email a été envoyé automatiquement. Merci de ne pas répondre.</p>';
        $html .= '<p style="margin: 5px 0 0 0;">Besoin d\'aide ? Contactez-nous: rhflow@dc-knowing.com | +225 07 67 13 19 93</p>';
        $html .= '</div>';
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * Envoyer une notification de changement de statut de commande
     */
    public function sendOrderStatusNotificationAction(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Récupérer l'ancien statut depuis la base de données
        $oldStatus = $order->getOriginal('status');
        $newStatus = $order->status;
        
        // Envoyer la notification
        $this->sendOrderStatusNotification($order, $oldStatus, $newStatus);
        
        return redirect()->back()->with('success', 'Notification envoyée avec succès');
    }
}