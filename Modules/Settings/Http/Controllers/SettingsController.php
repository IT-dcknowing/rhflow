<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Leaves\Models\LeaveType;
use Modules\Settings\Models\WorkLocation;
use Modules\Settings\Models\CompanyDocument;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Company;
use App\Models\Designation;
use App\Models\Country;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\EmployeeDay;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Settings\Exports\UsersExport;
use Modules\Settings\Imports\UsersImport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;


class SettingsController extends Controller
{
    /**
     * Afficher la page d'accueil des paramètres
     */
    public function index()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Statistiques rapides
        if ($company) {
            $stats = [
                'total_branches' => $company->branches()->count(),
                'total_departments' => $company->departments()->count(),
                'total_designations' => $company->designations()->count(),
                'total_leave_types' => LeaveType::forCompany($company->id)->count(),
                'total_work_locations' => WorkLocation::forCompany($company->id)->count(),
            ];
        }else{
            $stats = [
                'total_branches' => 0,
                'total_departments' => 0,
                'total_designations' => 0,
                'total_leave_types' => 0,
                'total_work_locations' => 0,
            ];
        }

        return view('settings::index', compact('user', 'company', 'stats'));
    }

    public function configEnd()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = Company::find($user->company_id);
        
        $user->config_company = 1;
        $user->save();
        
        if($company->industry == null){
            return redirect()->back()->with('error', 'Veuillez configurer le secteur d\'activité de votre entreprise.');
        }else{
            return redirect()->route('company.dashboard');
        }
    }

    /**
     * Afficher le formulaire de configuration de l'entreprise
     */
    public function companySettings()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Récupérer les secteurs d'activité disponibles
        $sectors = \App\Models\Sector::orderBy('name')->get();

        return view('settings::company-settings', compact('user', 'company', 'sectors'));
    }

    /**
     * Mettre à jour les paramètres de l'entreprise
     */
    public function updateCompanySettings(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'employee_prefix' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:100',
            'accident_taux' => 'nullable|string|max:100',
            'size' => 'nullable|string|in:startup,small,medium,large,enterprise',
            'description' => 'nullable|string',
            'tax_id' => 'nullable|string|max:20',
            'registration_number' => 'nullable|string|max:50',
            'working_hours_per_week' => 'nullable|numeric|min:1|max:168',
            'working_days_per_week' => 'nullable|numeric|min:1|max:7',
            'timezone' => 'nullable|string',
            'state' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_data' => 'nullable|string',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'header_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        // Mettre à jour les informations de base
        $company->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone, 
            'employee_prefix' => $request->employee_prefix,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'website' => $request->website,
            'industry' => $request->industry,
            'accident_taux' => $request->accident_taux,
            'size' => $request->size,
            'description' => $request->description,
            'tax_id' => $request->tax_id,
            'registration_number' => $request->registration_number,
            'working_hours_per_week' => $request->working_hours_per_week,
            'working_days_per_week' => $request->working_days_per_week,
        ]);

        // Mettre à jour les paramètres géographiques dans settings JSON
        $settings = $company->settings ?? [];
        $geoFieldsUpdated = false;

        if ($request->filled('timezone')) {
            $settings['timezone'] = $request->timezone;
            $geoFieldsUpdated = true;
        }

        if ($request->filled('state')) {
            $settings['state'] = $request->state;
            $geoFieldsUpdated = true;
        }

        if ($request->filled('latitude')) {
            $settings['latitude'] = $request->latitude;
            $geoFieldsUpdated = true;
        }

        if ($request->filled('longitude')) {
            $settings['longitude'] = $request->longitude;
            $geoFieldsUpdated = true;
        }

        if ($request->filled('location_data')) {
            $settings['location_data'] = $request->location_data;
            $geoFieldsUpdated = true;
        }

        if ($geoFieldsUpdated) {
            $company->update(['settings' => $settings]);
        }

        // Mettre à jour les couleurs du thème
        $colorsUpdated = false;

        if ($request->filled('primary_color')) {
            $settings['primary_color'] = $request->primary_color;
            $colorsUpdated = true;
        }

        if ($request->filled('secondary_color')) {
            $settings['secondary_color'] = $request->secondary_color;
            $colorsUpdated = true;
        }

        if ($request->filled('header_bg_color')) {
            $settings['header_bg_color'] = $request->header_bg_color;
            $colorsUpdated = true;
        }

        if ($colorsUpdated) {
            $company->update(['settings' => $settings]);
        }

        $user = User::find($company->user_id);
        $user->company_id = $company->id;   
        $user->save();

        return redirect()->back()->with('success', 'Paramètres de l\'entreprise mis à jour avec succès.');
    }

    /**
     * Mettre à jour le logo de l'entreprise
     */
    public function updateLogo(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();
        $company = $user->company;

        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo si il existe
            if ($company->logo && Storage::exists('public/logos/' . $company->logo)) {
                Storage::delete('public/logos/' . $company->logo);
            }

            // Uploader le nouveau logo
            $logoName = time() . '_logo.' . $request->logo->extension();
            $logo = $request->file('logo');
            $logo->move(public_path('storage/logos'), $logoName);

            $company->updateLogo($logoName);
        }

        return redirect()->back()->with('success', 'Logo mis à jour avec succès.');
    }

    /**
     * Mettre à jour la signature électronique
     */
    public function updateSignature(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'signature' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $company = $user->company;

        if ($request->hasFile('signature')) {
            // Supprimer l'ancienne signature si elle existe
            if ($company->electronic_signature && Storage::exists('public/signatures/' . $company->electronic_signature)) {
                Storage::delete('public/signatures/' . $company->electronic_signature);
            }

            // Uploader la nouvelle signature
            $signatureName = time() . '_signature.' . $request->signature->extension();
            $signature = $request->file('signature');
            $signature->move(public_path('storage/signatures'), $signatureName);

            $company->updateSignature($signatureName);
        }

        return redirect()->back()->with('success', 'Signature électronique mise à jour avec succès.');
    }

    /**
     * Mettre à jour le cachet électronique
     */
    public function updateStamp(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'stamp' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $company = $user->company;

        if ($request->hasFile('stamp')) {
            // Supprimer l'ancien cachet si il existe
            if ($company->electronic_stamp && Storage::exists('public/stamps/' . $company->electronic_stamp)) {
                Storage::delete('public/stamps/' . $company->electronic_stamp);
            }

            // Uploader le nouveau cachet
            $stampName = time() . '_cachet.' . $request->stamp->extension();
            $stamp = $request->file('stamp');
            $stamp->move(public_path('storage/stamps'), $stampName);

            $company->updateStamp($stampName);
        }

        return redirect()->back()->with('success', 'Cachet électronique mis à jour avec succès.');
    }

    /**
     * Afficher les types de congés
     */
    public function leaveTypes()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;
        $leaveTypes = LeaveType::forCompany($company->id)->orWhere('types','default')->orderBy('title')->get();

        return view('settings::leave-types', compact('user', 'company', 'leaveTypes'));
    }

    /**
     * Créer un type de congé
     */
    public function storeLeaveType(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'days' => 'required|integer|min:1|max:365',
        ]);

        $user = Auth::user();
        $company = $user->company;

        LeaveType::create([
            'title' => $request->title,
            'days' => $request->days,
            'created_by' => $user->id,
            'company_id' => $company->id,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Type de congé créé avec succès.');
    }

    /**
     * Mettre à jour un type de congé
     */
    public function updateLeaveType(Request $request, LeaveType $leaveType)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }
        try{
            $request->validate([
                'title' => 'required|string|max:255',
                'days' => 'required|integer|min:1|max:365',
                'is_active' => 'nullable|boolean',
            ]);

            $leaveType->update([
                'title' => $request->title,
                'days' => $request->days,
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()->back()->with('success', 'Type de congé mis à jour avec succès.');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour du type de congé.');
        }
    }

    /**
     * Supprimer un type de congé
     */
    public function destroyLeaveType(LeaveType $leaveType)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        try {
            $leaveType->delete();

            return redirect()->back()->with('success', 'Type de congé supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression du type de congé.');
        }
    }

    /**
     * Retourner les données d'un type de congé en JSON pour l'édition AJAX
     */
    public function editLeaveType(LeaveType $leaveType)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que le type de congé appartient à l'entreprise
        if ($leaveType->company_id !== $company->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'leaveType' => [
                'id' => $leaveType->id,
                'title' => $leaveType->title,
                'days' => $leaveType->days,
                'is_active' => $leaveType->is_active
            ]
        ]);
    }

    /**
     * Activer ou desactiver un type de congé
     */
    public function toggleLeaveType(LeaveType $leaveType)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que le type de congé appartient à l'entreprise
        if ($leaveType->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        $leaveType->is_active = !$leaveType->is_active;
        $leaveType->save();

        return redirect()->back()->with('success', 'Type de congé ' . ($leaveType->is_active ? 'activé' : 'desactivé') . ' avec succès.');
    }

    /**
     * Afficher les emplacements de travail
     */
    public function workLocations()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        // Récupérer tous les utilisateurs de l'entreprise pour le modal de création
        $users = User::where('id', $user->id)
            ->orWhere('created_by', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $company = $user->company;
        $workLocations = WorkLocation::forCompany($company->id)->orderBy('name')->get();
        // Seules les succursales actives peuvent recevoir un nouvel emplacement
        $branches = $company->branches()->where('is_active', 1)->orderBy('name')->get();
        $country = Country::All();

        return view('settings::work-locations', compact('user', 'company', 'workLocations', 'branches','users','country'));
    }

    /**
     * Créer un emplacement de travail
     */
    public function storeWorkLocation(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            \Log::warning('Unauthorized access attempt', [
                'user_id' => Auth::id(),
                'user_type' => Auth::user()->type,
            ]);
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Validation des données
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:office,remote,client_site,warehouse,factory,store',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:10',
                'branch_id' => 'nullable|exists:branches,id',
                'manager_id' => 'nullable|exists:users,id',
                'has_time_clock' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'user_id' => Auth::id(),
                'errors' => $e->validator->errors()->toArray(),
            ]);
            return redirect()->back()->withErrors($e->validator)->withInput();
        }

        // Préparer les données pour la création
        $workLocationData = [
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'has_time_clock' => $request->has_time_clock ?? false,
            'branch_id' => $request->branch_id,
            'company_id' => $company->id,
            'manager_id' => $request->manager_id,
            'is_active' => $request->is_active ?? true,
        ];

        // Ajouter les coordonnées GPS seulement si elles sont fournies
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $workLocationData['latitude'] = $request->latitude;
            $workLocationData['longitude'] = $request->longitude;
        }

        try {
            // Créer le nouvel emplacement de travail
            $workLocation = WorkLocation::create($workLocationData);

            \Log::info('Work location created successfully', [
                'user_id' => Auth::id(),
                'work_location_id' => $workLocation->id,
                'created_data' => $workLocationData,
            ]);
            
            return redirect()->back()->with('success', 'Emplacement de travail créé avec succès.');
        } catch (\Exception $e) {
            \Log::error('Error creating work location', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la création de l\'emplacement de travail.');
        }
    }

    /**
     * Mettre à jour un emplacement de travail
     */
    public function updateWorkLocation(Request $request, WorkLocation $workLocation)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            \Log::warning('Unauthorized access attempt', [
                'user_id' => Auth::id(),
                'user_type' => Auth::user()->type,
            ]);
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Log des données de la requête
        \Log::info('Updating work location', [
            'user_id' => Auth::id(),
            'work_location_id' => $workLocation->id,
            'request_data' => $request->all(),
        ]);

        // Validation des données
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:office,remote,client_site,warehouse,factory,store',
                'address_display' => 'required|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:10',
                'branch_id' => 'nullable|exists:branches,id',
                'manager_id' => 'nullable|exists:users,id',
                'has_time_clock' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'user_id' => Auth::id(),
                'errors' => $e->validator->errors()->toArray(),
            ]);
            return redirect()->back()->withErrors($e->validator)->withInput();
        }

        // Préparer les données pour la mise à jour
        $updateData = [
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'address' => $request->address_display,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'has_time_clock' => $request->has_time_clock ?? false,
            'branch_id' => $request->branch_id,
            'manager_id' => $request->manager_id,
            'is_active' => $request->boolean('is_active'),
        ];

        // Ajouter les coordonnées GPS seulement si elles sont fournies
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $updateData['latitude'] = $request->latitude;
            $updateData['longitude'] = $request->longitude;
        }

        try {
            $workLocation->update($updateData);
            \Log::info('Work location updated successfully', [
                'user_id' => Auth::id(),
                'work_location_id' => $workLocation->id,
                'updated_data' => $updateData,
            ]);
            
            return redirect()->back()->with('success', 'Emplacement de travail mis à jour avec succès.');
        } catch (\Exception $e) {
            \Log::error('Error updating work location', [
                'user_id' => Auth::id(),
                'work_location_id' => $workLocation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un emplacement de travail
     */
    public function destroyWorkLocation(WorkLocation $workLocation)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $workLocation->delete();

        return redirect()->back()->with('success', 'Emplacement de travail supprimé avec succès.');
    }

    /**
     * Basculer le statut d'un emplacement de travail (actif/inactif)
     */
    public function toggleWorkLocation(WorkLocation $workLocation)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que l'emplacement appartient à l'entreprise
        if ($workLocation->company_id !== $company->id) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
        }

        $workLocation->is_active = !$workLocation->is_active;
        $workLocation->save();

        return redirect()->back()->with('success', 'Statut de l\'emplacement mis à jour avec succès.');
    }

    /**
     * Afficher la page des couleurs du thème
     */
    public function themeColors()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        return view('settings::theme-colors', compact('user', 'company'));
    }

    /**
     * Mettre à jour les couleurs du thème
     */
    public function updateThemeColors(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'header_bg_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $user = Auth::user();
        $company = $user->company;

        // Mettre à jour les couleurs du thème
        $settings = $company->settings ?? [];
        $settings['primary_color'] = $request->primary_color;
        $settings['secondary_color'] = $request->secondary_color;
        $settings['header_bg_color'] = $request->header_bg_color;
        $company->update(['settings' => $settings]);

        return redirect()->back()->with('success', 'Couleurs du thème mises à jour avec succès.');
    }

    /**
     * Afficher la page des documents société
     */
    public function companyDocuments()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Récupérer les documents de l'entreprise
        $documents = [
            'logo' => $company->logo ? ['url' => $company->logo_url, 'name' => 'Logo'] : null,
            'signature' => $company->electronic_signature ? ['url' => $company->electronic_signature_url, 'name' => 'Signature'] : null,
            'stamp' => $company->electronic_stamp ? ['url' => $company->electronic_stamp_url, 'name' => 'Cachet'] : null,
        ];

        // Récupérer les documents légaux de l'entreprise
        $companyDocuments = CompanyDocument::forCompany($company->id)
            ->active()
            ->orderBy('document_type')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistiques des documents
        $documentStats = [
            'total_documents' => $companyDocuments->count(),
            'verified_documents' => $companyDocuments->where('is_verified', true)->count(),
            'expired_documents' => $companyDocuments->where('expiry_date', '<', now())->count(),
            'expiring_soon_documents' => $companyDocuments->where('expiry_date', '<=', now()->addDays(30))
                                                         ->where('expiry_date', '>', now())
                                                         ->count(),
            'missing_required' => CompanyDocument::getMissingRequiredDocuments($company->id),
        ];

        return view('settings::company-documents', compact('user', 'company', 'documents', 'companyDocuments', 'documentStats'));
    }

    /**
     * Mettre à jour les documents société
     */
    public function updateCompanyDocuments(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Validation des fichiers uploadés
        $validations = [];
        if ($request->hasFile('logo')) {
            $validations['logo'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }
        if ($request->hasFile('signature')) {
            $validations['signature'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        }
        if ($request->hasFile('stamp')) {
            $validations['stamp'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        if (!empty($validations)) {
            $request->validate($validations);
        }

        // Upload du logo
        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::exists('public/logos/' . $company->logo)) {
                Storage::delete('public/logos/' . $company->logo);
            }
            $logoName = time() . '.' . $request->logo->extension();
            $request->logo->storeAs('public/logos', $logoName);
            $company->updateLogo($logoName);
        }

        // Upload de la signature
        if ($request->hasFile('signature')) {
            if ($company->electronic_signature && Storage::exists('public/signatures/' . $company->electronic_signature)) {
                Storage::delete('public/signatures/' . $company->electronic_signature);
            }
            $signatureName = time() . '.' . $request->signature->extension();
            $request->signature->storeAs('public/signatures', $signatureName);
            $company->updateSignature($signatureName);
        }

        // Upload du cachet
        if ($request->hasFile('stamp')) {
            if ($company->electronic_stamp && Storage::exists('public/stamps/' . $company->electronic_stamp)) {
                Storage::delete('public/stamps/' . $company->electronic_stamp);
            }
            $stampName = time() . '.' . $request->stamp->extension();
            $request->stamp->storeAs('public/stamps', $stampName);
            $company->updateStamp($stampName);
        }

        return redirect()->back()->with('success', 'Documents société mis à jour avec succès.');
    }

    /**
     * Afficher la liste des utilisateurs
     */
    public function users(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;
        $employees = $company->employees;
        $branches = $company->branches;
        $departments = $company->departments;

        // Base query pour tous les utilisateurs de l'entreprise
        $baseUsersQuery = User::where(function ($q) use ($company) {
            $q->where('company_id', $company->id)
                ->orWhere('id', $company->user_id)
                ->orWhere('created_by', $company->user_id);
        });

        // Statistiques globales des utilisateurs (non altérées par les filtres)
        $stats = [
            'total' => (clone $baseUsersQuery)->count(),
            'active' => (clone $baseUsersQuery)->where('is_active', true)->count(),
            'inactive' => (clone $baseUsersQuery)->where('is_active', false)->count(),
            'hr' => (clone $baseUsersQuery)->where('type', 'hr')->count(),
            'payroll' => (clone $baseUsersQuery)->where('type', 'payroll')->count(),
            'company' => (clone $baseUsersQuery)->where('type', 'company')->count(),
            'employee' => (clone $baseUsersQuery)->where('type', 'employee')->count(),
        ];

        // Query pour la liste avec relations
        $query = (clone $baseUsersQuery)->with([
            'userEmployee.branch',
            'userEmployee.department',
            'userEmployee.designation',
        ]);

        // Filtre de recherche par mot-clé
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('userEmployee', function ($empQ) use ($search) {
                        $empQ->where('employee_id', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Filtre par profil / type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par statut (actif / inactif)
        if ($request->filled('status')) {
            $query->where('is_active', (int)$request->status === 1);
        }

        // Filtre par succursale / branche
        if ($request->filled('branch_id')) {
            $query->whereHas('userEmployee', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        // Filtre par département
        if ($request->filled('department_id')) {
            $query->whereHas('userEmployee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Pagination paramétrable (10 par défaut)
        $perPage = (int) $request->get('per_page', 10);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        return view('settings::users', compact('user', 'company', 'users', 'stats', 'employees', 'branches', 'departments'));
    }

    /**
     * Afficher le formulaire de création d'utilisateur
     */
    public function createUser()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Récupérer les branches, départements et postes pour les sélecteurs
        $branches = $company->branches;
        $departments = $company->departments;
        $designations = $company->designations;

        return view('settings::users-create', compact('user', 'company', 'branches', 'departments', 'designations'));
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function storeUser(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'type' => 'required|string|in:company,hr,payroll,employee',
            'phone' => 'nullable|string|max:20',
            'branch_id' => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'is_active' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $company = $user->company;

        // Créer l'utilisateur
        // company_id est indispensable : toute l'application filtre ses données
        // sur auth()->user()->company_id. Sans lui, le compte se connecte mais
        // toutes les pages restent vides.
        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'type' => $request->type,
            'phone' => $request->phone,
            'is_active' => $request->is_active ?? true,
            'created_by' => $user->id,
            'company_id' => $company->id,
        ]);

        // Créer l'employé
        $newEmployee = Employee::create([
            'user_id' => $newUser->id,
            'company_id' => $company->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'branch_id' => $request->branch_id,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'is_active' => $request->is_active ?? true,
            'company_doj' => now()->toDateString(),
            'start_date' => now()->toDateString(),
        ]);

        // Le matricule est indispensable : il identifie l'employé sur les
        // bulletins et les documents. Il est généré après l'insertion pour
        // reprendre l'identifiant réel de la ligne, comme les fiches créées
        // depuis le module Employés (ex. #EL-M-EMP0383 pour l'id 383).
        $newEmployee->update([
            'employee_id' => $this->genererMatricule($company, $newEmployee->id),
        ]);

        return redirect()->route('company.settings.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function userDetails(User $user)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $currentUser = Auth::user();
        $company = $currentUser->company;

        // Ne pas exposer un utilisateur d'une autre entreprise
        if ($user->company_id !== $company->id && $user->id !== $company->user_id && $user->created_by !== $company->user_id) {
            return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
        }

        $user->loadMissing(['creator']);
        $employee = Employee::with(['branch', 'department', 'designation'])->where('user_id', $user->id)->first();

        // Le JS attend { success, html } : on rend le partiel cote serveur.
        return response()->json([
            'success' => true,
            'html' => view('settings::partials.user-details', compact('user', 'company', 'employee'))->render(),
        ]);
    }

    /**
     * Afficher le formulaire d'édition d'utilisateur
     */
    public function editUser(User $user)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $currentUser = Auth::user();
        $company = $currentUser->company;
        
        // Récupérer les branches, départements et postes pour les sélecteurs
        $branches = $company->branches;
        $departments = $company->departments;
        $designations = $company->designations;
        $employee = Employee::where('user_id', $user->id)->first();

        return view('settings::users-edit', compact('user', 'company', 'branches', 'departments', 'designations', 'employee'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(Request $request, User $user)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $currentUser = Auth::user();
        $company = $currentUser->company;

        // L'identifiant de connexion attribué à la création ne change plus (ex. KOUA15) :
        // ni un changement de nom ni une valeur envoyée par le formulaire ne le remplacent.
        $usernameFige = !empty($user->username);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => [$usernameFige ? 'nullable' : 'required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => 'required|email|unique:users,email,' . $user->id . '|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'type' => 'required|string|in:company,hr,payroll,employee',
            'phone' => 'nullable|string|max:20',
            'branch_id' => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'is_active' => 'nullable|boolean',
        ]);

        // Mettre à jour l'utilisateur
        $updateData = [
            'name' => $request->name,
            'username' => $usernameFige ? $user->username : $request->username,
            'email' => $request->email,
            'type' => $request->type,
            'phone' => $request->phone,
            'is_active' => $request->is_active ?? true,
        ];

        // Mettre à jour le mot de passe seulement s'il est fourni
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Rattacher les comptes créés avant le correctif, qui n'ont pas de company_id.
        // On ne touche jamais à un rattachement déjà défini.
        if (empty($user->company_id)) {
            $updateData['company_id'] = $company->id;
        }

        $user->update($updateData);

        // Mettre à jour l'employé s'il existe
        $employee = Employee::where('user_id', $user->id)->first();
        if ($employee) {
            $donneesEmploye = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'branch_id' => $request->branch_id,
                'department_id' => $request->department_id,
                'designation_id' => $request->designation_id,
                'is_active' => $request->is_active ?? true,
            ];

            // Rattraper les fiches créées avant le correctif, sans matricule
            if (empty($employee->employee_id)) {
                $donneesEmploye['employee_id'] = $this->genererMatricule($company, $employee->id);
            }
            if (empty($employee->company_doj)) {
                $donneesEmploye['company_doj'] = now()->toDateString();
            }

            $employee->update($donneesEmploye);
        }else{
            // Créer l'employé
            $newEmployee = Employee::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'branch_id' => $request->branch_id,
                'department_id' => $request->department_id,
                'designation_id' => $request->designation_id,
                'is_active' => $request->is_active ?? true,
                'company_doj' => now()->toDateString(),
                'start_date' => now()->toDateString(),
            ]);

            $newEmployee->update([
                'employee_id' => $this->genererMatricule($company, $newEmployee->id),
            ]);
        }

        return redirect()->route('company.settings.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Matricule employé : préfixe de l'entreprise + identifiant sur 4 chiffres.
     * Même format que celui produit par le module Employés.
     */
    private function genererMatricule($company, int $employeeId): string
    {
        return $company->employee_prefix . str_pad((string) $employeeId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroyUser(User $user)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $currentUser = Auth::user();
        $company = $currentUser->company;

        // Empêcher la suppression de son propre compte
        if ($user->id === $currentUser->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('company.settings.users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Basculer le statut d'un utilisateur (actif/inactif)
     */
    public function toggleUserStatus(User $user)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $currentUser = Auth::user();
        $company = $currentUser->company;

        // Empêcher la désactivation de son propre compte
        if ($user->id === $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        // Basculer la colonne brute : $user->is_active est un accesseur qui combine
        // is_active et active_status (desactivation cote super-admin).
        $user->update(['is_active' => !(bool) $user->getRawOriginal('is_active')]);

        return response()->json([
            'success' => true,
            'message' => 'Statut de l\'utilisateur mis à jour avec succès.',
            'is_active' => $user->is_active
        ]);
    }

    /**
     * Exporter les utilisateurs au format Excel
     */
    public function exportUsers(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Récupérer tous les utilisateurs de l'entreprise
        $query = User::where(function ($q) use ($company) {
            $q->where('company_id', $company->id)
                ->orWhere('id', $company->user_id)
                ->orWhere('created_by', $company->user_id);
        })->with([
            'userEmployee.branch',
            'userEmployee.department',
            'userEmployee.designation',
            'creator'
        ]);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('userEmployee', function ($empQ) use ($search) {
                        $empQ->where('employee_id', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('is_active', (int)$request->status === 1);
        }
        if ($request->filled('branch_id')) {
            $query->whereHas('userEmployee', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }
        if ($request->filled('department_id')) {
            $query->whereHas('userEmployee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        // Créer le fichier Excel
        return Excel::download(new UsersExport($users), 'utilisateurs_' . \Illuminate\Support\Str::slug($company->name) . '_' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Importer les utilisateurs depuis un fichier Excel
     */
    public function importUsers(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
            'skip_duplicates' => 'boolean',
        ]);

        $user = Auth::user();
        $company = $user->company;

        try {
            // Importer le fichier : les lignes invalides sont écartées et listées
            $import = new UsersImport($company, $user, $request->boolean('skip_duplicates'));
            Excel::import($import, $request->file('file'));

            $message = $import->importes . ' utilisateur(s) importé(s).';
            if ($import->ignores) {
                $message .= ' ' . $import->ignores . ' doublon(s) ignoré(s).';
            }

            $refus = collect($import->failures())
                ->map(fn ($echec) => 'ligne ' . $echec->row() . ' : ' . implode(' ', $echec->errors()))
                ->unique()
                ->values();
            if ($refus->isNotEmpty()) {
                $message .= ' ' . $refus->count() . ' ligne(s) refusée(s) — ' . $refus->take(5)->implode(' · ') . ($refus->count() > 5 ? ' …' : '');

                return redirect()->back()->with('error', $message);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Afficher la page de gestion des branches (sites/succursales)
     */
    public function branches()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;
        $branches = $company->branches()->orderBy('name')->get();

        // Récupérer tous les utilisateurs de l'entreprise pour le modal de création
        $companyUsers = User::where('id', $company->user_id)
            ->orWhere('created_by', $company->user_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Statistiques
        $stats = [
            'total_branches' => $branches->count(),
            'active_branches' => $branches->where('is_active', true)->count(),
            'inactive_branches' => $branches->where('is_active', false)->count(),
        ];

        return view('settings::branches', compact('user', 'company', 'branches', 'companyUsers', 'stats'));
    }

    /**
     * Retourner les données d'une branche en JSON pour l'édition AJAX
     */
    public function editBranch(Branch $branch)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que la branche appartient à l'entreprise
        if ($branch->company_id !== $company->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        // Charger la relation manager si elle existe
        $branch->load('manager');

        return response()->json([
            'success' => true,
            'branch' => [
                'id' => $branch->id,
                'name' => $branch->name,
                'code' => $branch->code,
                'type' => $branch->type,
                'address' => $branch->address,
                'phone' => $branch->phone,
                'email' => $branch->email,
                'manager_id' => $branch->manager_id,
                'is_active' => $branch->is_active,
                'manager' => $branch->manager ? [
                    'id' => $branch->manager->id,
                    'name' => $branch->manager->name,
                    'email' => $branch->manager->email
                ] : null
            ]
        ]);
    }

    /**
     * Créer une nouvelle branche
     */
    public function storeBranch(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Debug: Log des données reçues
        \Log::info('StoreBranch Debug', [
            'user_id' => $user->id,
            'company_id' => $company->id,
            'request_data' => $request->all()
        ]);

        // Validation des données
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:branches,code',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'type' => 'required|in:siege,succursale',
                'manager_id' => 'required|exists:users,id',
                'is_active' => 'nullable|boolean'
            ], [
                'type.required' => 'Le type de site est obligatoire.',
                'type.in' => 'Le type de site doit être « siège » ou « succursale ».',
                'manager_id.required' => 'Le manager du site est obligatoire.',
                'manager_id.exists' => "Le manager sélectionné n'existe pas.",
            ]);
        } catch (\Exception $validationException) {
            \Log::error('Validation error', [
                'errors' => $validationException->validator->errors()->all(),
            ]);
            return redirect()->back()->with('error', 'Erreur de validation : ' . implode(', ', $validationException->validator->errors()->all()));
        }

        // Tentative de création de la succursale
        try {
            \Log::info('Attempting to create branch', [
                'company_id' => $company->id,
                'branch_data' => [
                    'name' => $request->name,
                    'code' => $request->code,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'manager_id' => $request->manager_id,
                    'is_active' => $request->is_active
                ]
            ]);

            $branch = Branch::create([
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'manager_id' => $request->manager_id,
                'company_id' => $company->id,
                // has() est toujours vrai (champ hidden is_active=0 dans le formulaire) :
                // il faut lire la valeur envoyee, pas la presence du champ.
                'is_active' => $request->boolean('is_active')
            ]);

            \Log::info('Branch created successfully', ['branch_id' => $branch->id]);

            return redirect()->back()->with('success', 'Succursale créée avec succès.');
        } catch (\Exception $e) {
            \Log::error('Error creating branch', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Créer un manager depuis la modale d'un site, et le renvoyer en JSON.
     *
     * La modale ne demande que le strict nécessaire : l'identifiant de connexion est
     * généré comme ailleurs dans l'application, et le type vaut « hr » par défaut.
     * Comme storeUser(), on crée aussi la fiche employé et son matricule, sans quoi
     * le compte n'apparaîtrait pas dans le module Employés.
     */
    public function storeBranchManager(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
        }

        // Les erreurs partent en JSON : la requête est envoyée avec Accept: application/json.
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'phone' => 'nullable|string|max:20',
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée.',
        ]);

        $user = Auth::user();
        $company = $user->company;

        // Le mot de passe n'est plus saisi dans la modale : il est généré ici et renvoyé
        // en clair une seule fois, pour être transmis au manager. Sans cela, le compte
        // serait créé mais inutilisable tant qu'il ne passe pas par « mot de passe oublié ».
        $motDePasse = \Illuminate\Support\Str::password(12);

        try {
            // company_id est indispensable : toute l'application filtre ses données dessus.
            $manager = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => User::genererUsername($request->name),
                'password' => Hash::make($motDePasse),
                'type' => 'hr',
                'phone' => $request->phone,
                'is_active' => true,
                'created_by' => $user->id,
                'company_id' => $company->id,
            ]);

            $employee = Employee::create([
                'user_id' => $manager->id,
                'company_id' => $company->id,
                'name' => $manager->name,
                'email' => $manager->email,
                'phone' => $manager->phone,
                'is_active' => true,
                'company_doj' => now()->toDateString(),
                'start_date' => now()->toDateString(),
            ]);

            $employee->update([
                'employee_id' => $this->genererMatricule($company, $employee->id),
            ]);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $manager->id,
                    'name' => $manager->name,
                    'email' => $manager->email,
                    // Libellé prêt à poser dans la liste, au même format que les options rendues.
                    'libelle' => $manager->email ? $manager->name . ' (' . $manager->email . ')' : $manager->name,
                    'identifiant' => $manager->username,
                ],
                // Affiché une seule fois côté navigateur : il n'est stocké qu'en haché.
                'motDePasse' => $motDePasse,
            ]);
        } catch (\Exception $e) {
            \Log::error('Création du manager impossible', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Création impossible : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mettre à jour une branche
     */
    public function updateBranch(Request $request, Branch $branch)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que la branche appartient à l'entreprise
        if ($branch->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code,' . $branch->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'type' => 'required|in:siege,succursale',
            'manager_id' => 'required|exists:users,id',
            'is_active' => 'nullable|boolean'
        ], [
            'type.required' => 'Le type de site est obligatoire.',
            'type.in' => 'Le type de site doit être « siège » ou « succursale ».',
            'manager_id.required' => 'Le manager du site est obligatoire.',
            'manager_id.exists' => "Le manager sélectionné n'existe pas.",
        ]);

        $branch->update([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'manager_id' => $request->manager_id,
            'is_active' => $request->boolean('is_active')
        ]);

        return redirect()->back()->with('success', 'Succursale mise à jour avec succès.');
    }

    /**
     * Activer ou desactiver une branche
     */
    public function toggleBranch(Branch $branch)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que la branche appartient à l'entreprise
        if ($branch->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        $branch->is_active = !$branch->is_active;
        $branch->save();

        return redirect()->back()->with('success', 'Succursale ' . ($branch->is_active ? 'activée' : 'desactivée') . ' avec succès.');
    }

    /**
     * Supprimer une branche
     */
    public function destroyBranch(Branch $branch)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que la branche appartient à l'entreprise
        if ($branch->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        $branch->delete();

        return redirect()->back()->with('success', 'Succursale supprimée avec succès.');
    }

    /**
     * Afficher la page de gestion des departments (services)
     */
    public function departments()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;
        $departments = $company->departments()->with('branch')->orderBy('name')->get();
        $branches = $company->branches()->active()->get();

        // Récupérer tous les utilisateurs de l'entreprise pour le modal de création
        $companyUsers = User::where('id', $company->user_id)
            ->orWhere('created_by', $company->user_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Statistiques
        $stats = [
            'total_departments' => $departments->count(),
            'active_departments' => $departments->where('is_active', true)->count(),
            'inactive_departments' => $departments->where('is_active', false)->count(),
        ];

        return view('settings::departments', compact('user', 'company', 'departments', 'branches', 'companyUsers', 'stats'));
    }

    /**
     * Activer ou desactiver un department
     */
    public function toggleDepartment(Department $department)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que le department appartient à l'entreprise
        if ($department->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        $department->is_active = !$department->is_active;
        $department->save();

        return redirect()->back()->with('success', 'Service ' . ($department->is_active ? 'activé' : 'desactivé') . ' avec succès.');
    }

    /**
     * Retourner les données d'un department en JSON pour l'édition AJAX
     */
    public function editDepartment(Department $department)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que le department appartient à l'entreprise
        if ($department->company_id !== $company->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'department' => [
                'id' => $department->id,
                'name' => $department->name,
                'code' => $department->code,
                'description' => $department->description,
                'branch_id' => $department->branch_id,
                'manager_id' => $department->manager_id,
                'is_active' => $department->is_active
            ]
        ]);
    }

    /**
     * Créer un nouveau department
     */
    public function storeDepartment(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        $request->validate([
            // Le nom doit être unique dans l'entreprise : le code ne suffit pas à l'empêcher,
            // puisqu'il est généré avec une part d'aléa et diffère donc à chaque saisie.
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('departments', 'name')
                    ->where(fn($query) => $query->where('company_id', $company->id)),
            ],
            'code' => 'required|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
            'manager_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
            'is_active' => 'nullable|boolean'
        ], [
            'name.unique' => 'Un service portant ce nom existe déjà dans votre entreprise.',
            'manager_id.unique' => 'Ce gestionnaire est déjà assigné à un autre service.',
            'manager_id.required' => 'Le manager du service est obligatoire.',
            'manager_id.exists' => "Le manager sélectionné n'existe pas.",
            'branch_id.required' => 'La succursale est obligatoire.',
            'branch_id.exists' => "La succursale sélectionnée n'existe pas.",
        ]);

        $department = Department::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'manager_id' => $request->manager_id,
            'branch_id' => $request->branch_id,
            'company_id' => $company->id,
            'is_active' => $request->boolean('is_active')
        ]);

        return redirect()->back()->with('success', 'Service créé avec succès.');
    }

    /**
     * Mettre à jour un department
     */
    public function updateDepartment(Request $request, Department $department)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que le department appartient à l'entreprise
        if ($department->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            // Unicité du nom dans l'entreprise, en ignorant le service en cours de modification.
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('departments', 'name')
                    ->ignore($department->id)
                    ->where(fn($query) => $query->where('company_id', $company->id)),
            ],
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'manager_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
            'is_active' => 'nullable|boolean'
        ], [
            'name.unique' => 'Un service portant ce nom existe déjà dans votre entreprise.',
            'manager_id.unique' => 'Ce gestionnaire est déjà assigné à un autre service.',
            'manager_id.required' => 'Le manager du service est obligatoire.',
            'manager_id.exists' => "Le manager sélectionné n'existe pas.",
            'branch_id.required' => 'La succursale est obligatoire.',
            'branch_id.exists' => "La succursale sélectionnée n'existe pas.",
        ]);

        $department->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'manager_id' => $request->manager_id,
            'branch_id' => $request->branch_id,
            'is_active' => $request->boolean('is_active')
        ]);

        return redirect()->back()->with('success', 'Service mis à jour avec succès.');
    }

    /**
     * Supprimer un department
     */
    public function destroyDepartment(Department $department)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que le department appartient à l'entreprise
        if ($department->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        $department->delete();

        return redirect()->back()->with('success', 'Service supprimé avec succès.');
    }

    /**
     * Afficher la page de gestion des designations (postes)
     */
    public function designations()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;
        $designations = $company->designations()->with('department')->orderBy('name')->get();
        $departments = $company->departments()->active()->get();

        // Statistiques
        $stats = [
            'total_designations' => $designations->count(),
            'active_designations' => $designations->where('is_active', true)->count(),
            'inactive_designations' => $designations->where('is_active', false)->count(),
        ];

        return view('settings::designations', compact('user', 'company', 'designations', 'departments', 'stats'));
    }

    /**
     * Activer ou desactiver une designation
     */
    public function toggleDesignation(Designation $designation)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que la designation appartient à l'entreprise
        if ($designation->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        $designation->is_active = !$designation->is_active;
        $designation->save();

        return redirect()->back()->with('success', 'Poste ' . ($designation->is_active ? 'activé' : 'desactivé') . ' avec succès.');
    }

    /**
     * Retourner les données d'une designation en JSON pour l'édition AJAX
     */
    public function editDesignation(Designation $designation)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que la designation appartient à l'entreprise
        if ($designation->company_id !== $company->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'designation' => [
                'id' => $designation->id,
                'name' => $designation->name,
                'code' => $designation->code,
                'description' => $designation->description,
                'department_id' => $designation->department_id,
                'is_active' => $designation->is_active
            ]
        ]);
    }

    /**
     * Créer une nouvelle designation
     */
    public function storeDesignation(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // La validation est volontairement hors du try : une ValidationException étend
        // \Exception et serait capturée plus bas, ce qui remplacerait les erreurs de champ
        // par un message générique.
        $request->validate([
            // Le nom doit être unique dans l'entreprise : le code ne l'empêche pas,
            // puisqu'il est généré avec une part d'aléa et diffère à chaque saisie.
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('designations', 'name')
                    ->where(fn($query) => $query->where('company_id', $company->id)),
            ],
            'code' => 'required|string|max:50|unique:designations,code',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'is_active' => 'nullable|boolean'
        ], [
            'name.unique' => 'Un poste portant ce nom existe déjà dans votre entreprise.',
        ]);

        try {
            $designation = Designation::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'company_id' => $company->id,
                'is_active' => $request->boolean('is_active')
            ]);

            return redirect()->back()->with('success', 'Poste créé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la création du poste : ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour une designation
     */
    public function updateDesignation(Request $request, Designation $designation)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que la designation appartient à l'entreprise
        if ($designation->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            // Unicité du nom dans l'entreprise, en ignorant le poste en cours de modification.
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('designations', 'name')
                    ->ignore($designation->id)
                    ->where(fn($query) => $query->where('company_id', $company->id)),
            ],
            'code' => 'required|string|max:50|unique:designations,code,' . $designation->id,
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'is_active' => 'nullable|boolean'
        ], [
            'name.unique' => 'Un poste portant ce nom existe déjà dans votre entreprise.',
        ]);

        // Validation des données
        try {
            $designation->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'is_active' => $request->boolean('is_active')
            ]);

            return redirect()->back()->with('success', 'Poste mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour du poste : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une designation
     */
    public function destroyDesignation(Designation $designation)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que la designation appartient à l'entreprise
        if ($designation->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        // Validation des données
        try {
            $designation->delete();

            return redirect()->back()->with('success', 'Poste supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression du poste : ' . $e->getMessage());
        }
    }

    /**
     * Afficher la page de configuration du système de présence
     */
    public function attendanceSystem()
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;
        $total_emp = Employee::where('company_id', $company->id)->where('is_active', 1)->count();

        // Récupérer les statistiques des types de présence
        $attendanceStats = [
            'total_employees' => $total_emp,
            'manual_attendance' => $company->employees()->where('is_active', 1)->count(),
            'qr_code_attendance' => $company->employees()->where('is_active', 1)->count(),
            'not_configured' => $company->users()->whereNull('attendance_type')->count(),
        ];

        $locations = WorkLocation::where('company_id', $company->id)->get();

        return view('settings::attendance-system', compact('user', 'company', 'attendanceStats', 'locations'));
    }

    /**
     * Mettre à jour la configuration du système de présence
     */
    public function updateAttendanceSystem(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
        }
        
        try {
            $user = Auth::user();
            $company = $user->company;

            $request->validate([
                'default_attendance_type' => 'required|in:manual,qr_code',
                'allow_multiple_types' => 'nullable|boolean',
                'qr_code_settings' => 'nullable|array'
            ]);

            $company->update([
                'default_attendance_type' => $request->default_attendance_type,
                'allow_multiple_attendance_types' => $request->has('allow_multiple_types'),
                'attendance_settings' => [
                    'qr_code' => $request->qr_code_settings,
                ]
            ]);

            if (!$request->has('allow_multiple_types')) {
                $company->users()->update([
                    'attendance_type' => $request->default_attendance_type
                ]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Système de présence mis à jour avec succès.',
                'redirect' => route('company.settings.attendance-system.index')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error updating attendance system', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false, 
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère un QR code pour un emplacement spécifique
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateQRCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:work_locations,id',
            'qr_size' => 'nullable|integer|min:100|max:800',
            'api_url' => 'nullable|url',
            'qr_type' => 'nullable|string|in:mobile_app,web_portal',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $company = $user->company;

        // Récupérer l'emplacement sélectionné par l'utilisateur
        $location = WorkLocation::where('id', $request->location_id)
            ->where('company_id', $company->id)
            ->where('is_active', 1)
            ->first();

        if (!$location) {
            return redirect()->back()->withErrors(['location_id' => 'Emplacement non trouvé ou inactif.'])->withInput();
        }

        $size = $request->qr_size ?? 500;
        $qrType = $request->qr_type ?? 'mobile_app';
        $timestamp = now()->timestamp;

        // Déterminer la chaîne encodée selon le type
        if ($qrType === 'web_portal') {
            // Le code QR contiendra le lien vers notre nouveau Portail Mobile
            $qrString = route('company.times.qrcode-pointage.portal', ['location_id' => $location->id]);
        } else {
            $api_url = config('app.url') . '/api/pointage/scanner';
            $qrString = "{$api_url}?location_id={$location->id}&timestamp={$timestamp}";
        }

        // Vérifier que $qrString n'est pas vide
        if (empty($qrString)) {
            return redirect()->back()->withErrors(['api_url' => 'L\'URL est requise pour générer le QR code.'])->withInput();
        }

        // Générer le QR code en format SVG (ne nécessite pas d'extension image)
        $qrCode = QrCode::format('svg')
                        ->size($size)
                        ->errorCorrection('H')
                        ->encoding('UTF-8')
                        ->generate($qrString);

        $qrCodeBase64 = base64_encode($qrCode);

        return view('settings::partials.qr-display', [
            'company' => $company,
            'qrCode' => $qrCodeBase64,
            'location' => $location,
            'qrString' => $qrString,
            'qrType' => $qrType, // Passer le type à la vue
            'timestamp' => $timestamp,
            'size' => $size
        ]);
    }

    /**
     * Télécharger le QR code en format PNG
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadQRCode(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $validator = Validator::make($request->all(), [
            'qr_string' => 'required|string',
            'location_name' => 'required|string',
            'size' => 'nullable|integer|min:100|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $size = $request->size ?? 500;
        $locationName = $request->location_name;
        
        // Générer le QR code en format SVG
        $qrCodeSvg = QrCode::format('svg')
                        ->size($size)
                        ->errorCorrection('H')
                        ->encoding('UTF-8')
                        ->generate($request->qr_string);

        // Créer un nom de fichier sécurisé
        $fileName = 'QRCode_' . Str::slug($locationName) . '_' . date('Y-m-d_His') . '.svg';

        // Retourner le fichier en téléchargement
        return response($qrCodeSvg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Créer un document de société
     */
    public function storeCompanyDocument(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        $request->validate([
            'document_type' => 'required|string|in:' . implode(',', array_keys(CompanyDocument::DOCUMENT_TYPES)),
            'document_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB max
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'is_required' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
            'verification_notes' => 'nullable|string',
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $request->document_type . '.' . $file->extension();
            $filePath = $file->storeAs('company_documents', $fileName, 'public');

            CompanyDocument::create([
                'document_type' => $request->document_type,
                'document_name' => $request->document_name,
                'description' => $request->description,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'issue_date' => $request->issue_date,
                'expiry_date' => $request->expiry_date,
                'is_required' => $request->is_required ?? false,
                'is_verified' => $request->is_verified ?? false,
                'verification_notes' => $request->verification_notes,
                'company_id' => $company->id,
                'is_active' => true,
            ]);

            \Log::info('Company document created successfully', [
                'user_id' => Auth::id(),
                'company_id' => $company->id,
                'document_type' => $request->document_type,
            ]);

            return redirect()->back()->with('success', 'Document ajouté avec succès.');
        } catch (\Exception $e) {
            \Log::error('Error creating company document', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Erreur lors de l\'ajout du document: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour un document de société
     */
    public function updateCompanyDocument(Request $request, CompanyDocument $companyDocument)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que le document appartient à l'entreprise
        if ($companyDocument->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'document_type' => 'required|string|in:' . implode(',', array_keys(CompanyDocument::DOCUMENT_TYPES)),
            'document_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'is_required' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
            'verification_notes' => 'nullable|string',
        ]);

        try {
            $updateData = [
                'document_type' => $request->document_type,
                'document_name' => $request->document_name,
                'description' => $request->description,
                'issue_date' => $request->issue_date,
                'expiry_date' => $request->expiry_date,
                'is_required' => $request->is_required ?? false,
                'is_verified' => $request->is_verified ?? false,
                'verification_notes' => $request->verification_notes,
            ];

            // Si un nouveau fichier est uploadé
            if ($request->hasFile('file')) {
                // Supprimer l'ancien fichier
                if ($companyDocument->file_path && Storage::exists('public/' . $companyDocument->file_path)) {
                    Storage::delete('public/' . $companyDocument->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $request->document_type . '.' . $file->extension();
                $filePath = $file->storeAs('company_documents', $fileName, 'public');

                $updateData = array_merge($updateData, [
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }

            $companyDocument->update($updateData);

            \Log::info('Company document updated successfully', [
                'user_id' => Auth::id(),
                'document_id' => $companyDocument->id,
            ]);

            return redirect()->back()->with('success', 'Document mis à jour avec succès.');
        } catch (\Exception $e) {
            \Log::error('Error updating company document', [
                'user_id' => Auth::id(),
                'document_id' => $companyDocument->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Erreur lors de la mise à jour du document: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un document de société
     */
    public function destroyCompanyDocument(CompanyDocument $companyDocument)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que le document appartient à l'entreprise
        if ($companyDocument->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        try {
            // Supprimer le fichier physique
            if ($companyDocument->file_path && Storage::exists('public/' . $companyDocument->file_path)) {
                Storage::delete('public/' . $companyDocument->file_path);
            }

            $companyDocument->delete();

            \Log::info('Company document deleted successfully', [
                'user_id' => Auth::id(),
                'document_id' => $companyDocument->id,
            ]);

            return redirect()->back()->with('success', 'Document supprimé avec succès.');
        } catch (\Exception $e) {
            \Log::error('Error deleting company document', [
                'user_id' => Auth::id(),
                'document_id' => $companyDocument->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Erreur lors de la suppression du document: ' . $e->getMessage());
        }
    }

    /**
     * Basculer le statut de vérification d'un document
     */
    public function toggleDocumentVerification(CompanyDocument $companyDocument)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que le document appartient à l'entreprise
        if ($companyDocument->company_id !== $company->id) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
        }

        $companyDocument->update([
            'is_verified' => !$companyDocument->is_verified,
            'verification_date' => $companyDocument->is_verified ? null : now(),
            'verified_by' => $companyDocument->is_verified ? null : $user->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Statut de vérification mis à jour avec succès.',
            'is_verified' => $companyDocument->is_verified,
            'status_badge' => $companyDocument->status_badge,
        ]);
    }

    /**
     * Télécharger un document de société
     */
    public function downloadCompanyDocument(CompanyDocument $companyDocument)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $company = $user->company;

        // Vérifier que le document appartient à l'entreprise
        if ($companyDocument->company_id !== $company->id) {
            abort(403, 'Accès non autorisé');
        }

        if (!$companyDocument->file_path || !Storage::exists('public/' . $companyDocument->file_path)) {
            abort(404, 'Fichier non trouvé');
        }

        \Log::info('Company document downloaded', [
            'user_id' => Auth::id(),
            'document_id' => $companyDocument->id,
        ]);

        return Storage::download('public/' . $companyDocument->file_path, $companyDocument->file_name);
    }

    /**
     * API pour récupérer les suggestions de localisation
     */
    public function getLocationSuggestions(Request $request)
    {
        if (!Auth::check() || Auth::user()->type !== 'company') {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $query = $request->get('q', '');
        $limit = $request->get('limit', 10);

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = [];

        // 1. Rechercher dans les emplacements existants de l'entreprise
        $user = Auth::user();
        $company = $user->company;

        $existingLocations = WorkLocation::forCompany($company->id)
            ->where(function($q) use ($query) {
                $q->where('address', 'like', '%' . $query . '%')
                  ->orWhere('city', 'like', '%' . $query . '%')
                  ->orWhere('state', 'like', '%' . $query . '%')
                  ->orWhere('country', 'like', '%' . $query . '%');
            })
            ->limit(5)
            ->get()
            ->map(function($location) {
                return [
                    'id' => 'existing_' . $location->id,
                    'text' => $location->full_address ?: $location->name,
                    'address' => $location->address,
                    'city' => $location->city,
                    'state' => $location->state,
                    'country' => $location->country,
                    'postal_code' => $location->postal_code,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'type' => 'existing',
                    'source' => 'database'
                ];
            });

        $suggestions = array_merge($suggestions, $existingLocations->toArray());

        // 2. Utiliser l'API Nominatim (OpenStreetMap) pour des suggestions externes
        try {
            $nominatimUrl = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
                'q' => $query,
                'format' => 'json',
                'limit' => $limit - count($suggestions),
                'addressdetails' => 1,
                'countrycodes' => 'CI,FR,BE,CH,CA,US,GB,DE,ES,IT,NL,DZ,TN,MA,SN,ML,NE,BF,TG,BJ,CD,CG,GA,CM,TD,CF,GN,BI,RW,UG,KE,TZ,AO,MZ,MG,MU,SC,DJ,ER,SO,ET,SS,SD,LY,EG,MR,GM,GW,SL,LR,GH,NG,ZW,BW,ZA,NA', // Pays francophones, européens et africains principaux
                'accept-language' => 'fr,en'
            ]);

            $context = stream_context_create([
                'http' => [
                    'timeout' => 3,
                    'user_agent' => 'RH-Flow/1.0 (contact@rhflow.com)'
                ]
            ]);

            $response = file_get_contents($nominatimUrl, false, $context);

            if ($response !== false) {
                $data = json_decode($response, true);

                foreach ($data as $item) {
                    $suggestions[] = [
                        'id' => 'nominatim_' . $item['place_id'],
                        'text' => $item['display_name'],
                        'address' => $item['address']['house_number'] ?? '' . ' ' . ($item['address']['road'] ?? ''),
                        'city' => $item['address']['city'] ?? $item['address']['town'] ?? $item['address']['village'] ?? '',
                        'state' => $item['address']['state'] ?? $item['address']['region'] ?? '',
                        'country' => $item['address']['country'] ?? '',
                        'postal_code' => $item['address']['postcode'] ?? '',
                        'latitude' => $item['lat'],
                        'longitude' => $item['lon'],
                        'type' => 'new',
                        'source' => 'nominatim'
                    ];
                }
            }
        } catch (\Exception $e) {
            // En cas d'erreur avec l'API externe, on continue avec les données locales
        }

        // 3. Ajouter des suggestions basées sur les données de pays et régions
        if (count($suggestions) < $limit) {
            $countries = Country::where('name', 'like', '%' . $query . '%')->limit(3)->get();
            foreach ($countries as $country) {
                $suggestions[] = [
                    'id' => 'country_' . $country->id,
                    'text' => $country->name,
                    'country' => $country->name,
                    'type' => 'country',
                    'source' => 'database'
                ];
            }
        }

        return response()->json(array_slice($suggestions, 0, $limit));
    }
}