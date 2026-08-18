<?php

namespace Modules\Evenements\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Evenements\Models\Promotion;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\EmployeeDay;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Designation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    /**
     * Affiche la liste des promotions
     */
    public function index(Request $request)
    {
        // Récupération des paramètres de filtrage
        $search = $request->input('search');
        $employee_id = $request->input('employee_id');
        $department_id = $request->input('department_id');
        $status = $request->input('status');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        // Construction de la requête
        $query = Promotion::with(['employee', 'oldDesignation', 'newDesignation', 'createdBy'])
            ->where('company_id', auth()->user()->company_id);

        // Filtrage par recherche
        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Filtrage par employé
        if ($employee_id) {
            $query->where('employee_id', $employee_id);
        }

        // Filtrage par département
        if ($department_id) {
            $query->whereHas('employee', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        // Filtrage par statut
        if ($status) {
            $query->where('status', $status);
        }

        // Filtrage par date
        if ($start_date) {
            $query->whereDate('promotion_date', '>=', $start_date);
        }
        
        if ($end_date) {
            $query->whereDate('promotion_date', '<=', $end_date);
        }

        // Tri par date de promotion (du plus récent au plus ancien)
        $query->orderBy('promotion_date', 'desc');

        // Pagination
        $promotions = $query->paginate(15)->withQueryString();

        // Données pour les filtres
        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();
            
        $departments = Department::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();

        // Calcul des statistiques
        $totalPromotions = Promotion::where('company_id', auth()->user()->company_id)->count();
        $pendingPromotions = Promotion::where('company_id', auth()->user()->company_id)
            ->where('status', 'pending')
            ->count();
        $approvedPromotions = Promotion::where('company_id', auth()->user()->company_id)
            ->where('status', 'approved')
            ->count();
        $rejectedPromotions = Promotion::where('company_id', auth()->user()->company_id)
            ->where('status', 'rejected')
            ->count();

        return view('evenements::promotions.index', [
            'promotions' => $promotions,
            'employees' => $employees,
            'departments' => $departments,
            'totalPromotions' => $totalPromotions,
            'pendingPromotions' => $pendingPromotions,
            'approvedPromotions' => $approvedPromotions,
            'rejectedPromotions' => $rejectedPromotions,
        ]);
    }

    /**
     * Affiche le formulaire de création d'une promotion
     */
    public function create()
    {
        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();
            
        $designations = Designation::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();
            
        return view('evenements::promotions.create', compact('employees', 'designations'));
    }

    /**
     * Enregistre une nouvelle promotion
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'old_designation_id' => 'required|exists:designations,id',
            'new_designation_id' => 'required|exists:designations,id',
            'promotion_date' => 'required|date',
            'effective_date' => 'required|date|after_or_equal:promotion_date',
            'salary' => 'required|numeric|min:0',
            'reason' => 'required|string|max:1000',
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Vérifier que l'employé appartient bien à l'entreprise
        $employee = Employee::where('id', $validated['employee_id'])
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();
            
        // Vérifier que les désignations appartiennent bien à l'entreprise
        $designations = Designation::whereIn('id', [$validated['old_designation_id'], $validated['new_designation_id']])
            ->where('company_id', auth()->user()->company_id)
            ->count();
            
        if ($designations !== 2) {
            return redirect()->back()
                ->with('error', 'Une ou plusieurs désignations sélectionnées sont invalides.')
                ->withInput();
        }

        // Création de la promotion
        $promotion = Promotion::create([
            'employee_id' => $validated['employee_id'],
            'old_designation_id' => $validated['old_designation_id'],
            'new_designation_id' => $validated['new_designation_id'],
            'promotion_date' => $validated['promotion_date'],
            'effective_date' => $validated['effective_date'],
            'previous_salary' => $employee->salary,
            'new_salary' => $validated['salary'],
            'reason' => $validated['reason'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'company_id' => auth()->user()->company_id,
            'created_by' => auth()->id(),
        ]);

        // Si la promotion est approuvée, mettre à jour les informations de l'employé
        if ($validated['status'] === 'approved' && $validated['effective_date'] <= now()->toDateString()) {
            $this->applyPromotion($promotion);
        }

        return redirect()->route('company.evenements.promotions.index')
            ->with('success', 'Promotion créée avec succès.');
    }

    /**
     * Affiche les détails d'une promotion
     */
    public function show(Promotion $promotion)
    {
        $this->authorize('view', $promotion);
        
        $promotion->load(['employee', 'oldDesignation', 'newDesignation', 'createdBy']);
        
        return view('evenements::promotions.show', compact('promotion'));
    }

    /**
     * Affiche le formulaire de modification d'une promotion
     */
    public function edit(Promotion $promotion)
    {
        $this->authorize('update', $promotion);
        
        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();
            
        $designations = Designation::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();
            
        return view('evenements::promotions.edit', compact('promotion', 'employees', 'designations'));
    }

    /**
     * Met à jour une promotion existante
     */
    public function update(Request $request, Promotion $promotion)
    {
        $this->authorize('update', $promotion);
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'old_designation_id' => 'required|exists:designations,id',
            'new_designation_id' => 'required|exists:designations,id',
            'promotion_date' => 'required|date',
            'effective_date' => 'required|date|after_or_equal:promotion_date',
            'new_salary' => 'required|numeric|min:0',
            'reason' => 'required|string|max:1000',
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Vérifier que l'employé appartient bien à l'entreprise
        $employee = Employee::where('id', $validated['employee_id'])
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();
            
        // Vérifier que les désignations appartiennent bien à l'entreprise
        $designations = Designation::whereIn('id', [$validated['old_designation_id'], $validated['new_designation_id']])
            ->where('company_id', auth()->user()->company_id)
            ->count();
            
        if ($designations !== 2) {
            return redirect()->back()
                ->with('error', 'Une ou plusieurs désignations sélectionnées sont invalides.')
                ->withInput();
        }

        // Sauvegarder l'ancien statut pour vérifier les changements
        $oldStatus = $promotion->status;
        
        // Mise à jour de la promotion
        $promotion->update([
            'employee_id' => $validated['employee_id'],
            'old_designation_id' => $validated['old_designation_id'],
            'new_designation_id' => $validated['new_designation_id'],
            'promotion_date' => $validated['promotion_date'],
            'effective_date' => $validated['effective_date'],
            'new_salary' => $validated['new_salary'],
            'reason' => $validated['reason'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Si le statut est passé à "approuvé" et que la date d'effet est passée, appliquer la promotion
        if ($validated['status'] === 'approved' && 
            ($oldStatus !== 'approved' || $promotion->wasChanged('effective_date')) && 
            $validated['effective_date'] <= now()->toDateString()) {
            $this->applyPromotion($promotion);
        }
        // Si le statut est passé de "approuvé" à autre chose, annuler la promotion
        elseif ($oldStatus === 'approved' && $validated['status'] !== 'approved') {
            $this->revertPromotion($promotion);
        }

        return redirect()->route('company.evenements.promotions.show', $promotion)
            ->with('success', 'Promotion mise à jour avec succès.');
    }

    /**
     * Supprime une promotion
     */
    public function destroy(Promotion $promotion)
    {
        $this->authorize('delete', $promotion);
        
        // Si la promotion était approuvée, annuler son application
        if ($promotion->status === 'approved') {
            $this->revertPromotion($promotion);
        }
        
        $promotion->delete();
        
        return redirect()->route('company.evenements.promotions.index')
            ->with('success', 'Promotion supprimée avec succès.');
    }
    
    /**
     * Applique une promotion à l'employé concerné
     */
    protected function applyPromotion(Promotion $promotion)
    {
        $employee = $promotion->employee;
        
        // Mettre à jour la désignation et le salaire de l'employé
        $employee->update([
            'designation_id' => $promotion->new_designation_id,
            'salary' => $promotion->new_salary,
        ]);
        
        // Mettre à jour la date de dernière promotion
        $promotion->update([
            'applied_at' => now(),
        ]);
    }
    
    /**
     * Annule l'application d'une promotion
     */
    protected function revertPromotion(Promotion $promotion)
    {
        $employee = $promotion->employee;
        
        // Vérifier si l'employé a toujours l'ancienne désignation
        if ($employee->designation_id === $promotion->new_designation_id) {
            // Revenir à l'ancienne désignation et à l'ancien salaire
            $employee->update([
                'designation_id' => $promotion->old_designation_id,
                'salary' => $promotion->previous_salary,
            ]);
        }
        
        // Mettre à jour la date d'annulation
        $promotion->update([
            'reverted_at' => now(),
        ]);
    }
    
    /**
     * Exporte les promotions au format Excel
     */
    public function export(Request $request, Promotion $promotion = null)
    {
        // Si un ID de promotion spécifique est fourni, exporter uniquement cette promotion
        if ($promotion) {
            $this->authorize('view', $promotion);
            $promotions = collect([$promotion]);
        } else {
            // Sinon, exporter toutes les promotions selon les filtres
            $query = $this->buildPromotionQuery($request);
            $promotions = $query->get();
        }
        
        // Générer le contenu CSV
        $headers = [
            'ID', 'Employé', 'Ancien poste', 'Nouveau poste', 
            'Date de promotion', 'Date d\'effet', 'Ancien salaire', 
            'Nouveau salaire', 'Statut', 'Raison', 'Notes', 'Créé le', 'Créé par'
        ];
        
        $rows = [];
        foreach ($promotions as $promo) {
            $rows[] = [
                $promo->id,
                $promo->employee->full_name,
                $promo->oldDesignation->name,
                $promo->newDesignation->name,
                $promo->promotion_date->format('d/m/Y'),
                $promo->effective_date->format('d/m/Y'),
                number_format($promo->previous_salary, 2, ',', ' '),
                number_format($promo->new_salary, 2, ',', ' '),
                $this->getStatusBadge($promo->status),
                $promo->reason,
                $promo->notes ?? '',
                $promo->created_at->format('d/m/Y H:i'),
                $promo->createdBy->name,
            ];
        }
        
        // Générer le fichier CSV
        $filename = $promotion 
            ? 'promotion_' . $promotion->id . '_' . now()->format('Y-m-d') . '.csv'
            : 'promotions_' . now()->format('Y-m-d') . '.csv';
            
        return $this->generateCsv($filename, $headers, $rows);
    }
    
    /**
     * Construit une requête de base pour les promotions avec les filtres
     */
    protected function buildPromotionQuery(Request $request)
    {
        $query = Promotion::with(['employee', 'oldDesignation', 'newDesignation', 'createdBy'])
            ->where('company_id', auth()->user()->company_id);
            
        // Appliquer les filtres
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        
        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('promotion_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('promotion_date', '<=', $request->end_date);
        }
        
        // Tri par défaut
        $query->orderBy('promotion_date', 'desc');
        
        return $query;
    }
    
    /**
     * Retourne le badge HTML pour le statut
     */
    protected function getStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">En attente</span>',
            'approved' => '<span class="badge bg-success">Approuvée</span>',
            'rejected' => '<span class="badge bg-danger">Rejetée</span>',
        ];
        
        return $badges[$status] ?? $status;
    }
    
    /**
     * Génère un fichier CSV à partir des données fournies
     */
    protected function generateCsv($filename, $headers, $rows)
    {
        $handle = fopen('php://temp', 'w+');
        
        // Ajouter l'en-tête UTF-8 pour Excel
        fputs($handle, "\xEF\xBB\xBF");
        
        // Écrire l'en-tête
        fputcsv($handle, $headers, ';');
        
        // Écrire les lignes
        foreach ($rows as $row) {
            // Nettoyer les balises HTML des statuts
            $row = array_map(function($value) {
                return strip_tags($value);
            }, $row);
            
            fputcsv($handle, $row, ';');
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        // Retourner la réponse avec le fichier CSV
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
