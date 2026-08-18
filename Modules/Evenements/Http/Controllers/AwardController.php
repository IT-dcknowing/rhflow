<?php

namespace Modules\Evenements\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Evenements\Models\Award;
use Modules\Evenements\Models\AwardType;
use Modules\Employees\Models\Employee;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AwardController extends Controller
{
    /**
     * Affiche la liste des récompenses avec filtrage et pagination
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('award_type_id');
        $employee = $request->input('employee_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status', 'all');

        $query = Award::with(['employee', 'awardType'])
            ->where('company_id', auth()->user()->company_id);

        // Filtrage par recherche
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('gift', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('employee', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtrage par type de récompense
        if ($type) {
            $query->where('award_type_id', $type);
        }

        // Filtrage par employé
        if ($employee) {
            $query->where('employee_id', $employee);
        }

        // Filtrage par date
        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        // Filtrage par statut
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $awards = $query->latest('date')->paginate(15)->withQueryString();

        // Données pour les filtres
        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();
            
        $awardTypes = AwardType::where('company_id', auth()->user()->company_id)
            ->orWhereNull('company_id')
            ->orderBy('name')
            ->get();

        return view('evenements::awards.index', compact(
            'awards',
            'employees',
            'awardTypes',
            'search',
            'type',
            'employee',
            'startDate',
            'endDate',
            'status'
        ));
    }

    /**
     * Affiche le formulaire de création d'une récompense
     */
    public function create()
    {
        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();
            
        $awardTypes = AwardType::where('company_id', auth()->user()->company_id)
            ->orWhereNull('company_id')
            ->orderBy('name')
            ->get();
            
        return view('evenements::awards.create', compact('employees', 'awardTypes'));
    }

    /**
     * Enregistre une nouvelle récompense
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employee_months,id',
            'award_type_id' => 'required|exists:award_types,id',
            'date' => 'required|date',
            'gift' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        // Vérifier que l'employé appartient bien à l'entreprise
        $employee = Employee::where('id', $validated['employee_id'])
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        // Créer la récompense
        $award = new Award();
        $award->employee_id = $validated['employee_id'];
        $award->award_type_id = $validated['award_type_id'];
        $award->date = $validated['date'];
        $award->gift = $validated['gift'];
        $award->description = $validated['description'] ?? null;
        $award->status = $validated['status'];
        $award->company_id = auth()->user()->company_id;
        $award->save();

        return redirect()
            ->route('company.evenements.awards.index')
            ->with('success', 'Récompense ajoutée avec succès.');
    }

    /**
     * Affiche les détails d'une récompense
     */
    public function show(Award $award)
    {
        $this->authorize('view', $award);
        
        return view('evenements::awards.show', compact('award'));
    }

    /**
     * Affiche le formulaire de modification d'une récompense
     */
    public function edit(Award $award)
    {
        $this->authorize('update', $award);
        
        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();
            
        $awardTypes = AwardType::where('company_id', auth()->user()->company_id)
            ->orWhereNull('company_id')
            ->orderBy('name')
            ->get();
            
        return view('evenements::awards.edit', compact('award', 'employees', 'awardTypes'));
    }

    /**
     * Met à jour une récompense existante
     */
    public function update(Request $request, Award $award)
    {
        $this->authorize('update', $award);
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employee_months,id',
            'award_type_id' => 'required|exists:award_types,id',
            'date' => 'required|date',
            'gift' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        // Vérifier que le nouvel employé appartient bien à l'entreprise
        $employee = Employee::where('id', $validated['employee_id'])
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        // Mettre à jour la récompense
        $award->update([
            'employee_id' => $validated['employee_id'],
            'award_type_id' => $validated['award_type_id'],
            'date' => $validated['date'],
            'gift' => $validated['gift'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status']
        ]);

        return redirect()
            ->route('company.evenements.awards.index')
            ->with('success', 'Récompense mise à jour avec succès.');
    }

    /**
     * Supprime une récompense
     */
    public function destroy(Award $award)
    {
        $this->authorize('delete', $award);
        
        $award->delete();
        
        return redirect()
            ->route('company.evenements.awards.index')
            ->with('success', 'Récompense supprimée avec succès.');
    }

    /**
     * Exporte les récompenses au format CSV
     */
    public function export(Request $request)
    {
        $query = Award::with(['employee', 'awardType'])
            ->where('company_id', auth()->user()->company_id);

        // Appliquer les mêmes filtres que dans l'index
        $this->applyFilters($query, $request);

        $awards = $query->get();

        $fileName = 'recompenses-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'ID', 'Employé', 'Type de récompense', 'Date', 'Cadeau', 
            'Description', 'Statut', 'Date de création'
        ];

        $callback = function() use($awards, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ";");

            foreach ($awards as $award) {
                $row = [
                    $award->id,
                    $award->employee->name,
                    $award->awardType->name,
                    $award->date->format('d/m/Y'),
                    $award->gift,
                    $award->description,
                    $this->getStatusBadge($award->status),
                    $award->created_at->format('d/m/Y H:i')
                ];

                fputcsv($file, $row, ";");
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Applique les filtres à la requête
     */
    protected function applyFilters($query, $request)
    {
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('gift', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('employee', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($type = $request->award_type_id) {
            $query->where('award_type_id', $type);
        }

        if ($employee = $request->employee_id) {
            $query->where('employee_id', $employee);
        }

        if ($startDate = $request->start_date) {
            $query->whereDate('date', '>=', $startDate);
        }

        if ($endDate = $request->end_date) {
            $query->whereDate('date', '<=', $endDate);
        }

        if ($status = $request->status && $request->status !== 'all') {
            $query->where('status', $status);
        }

        return $query;
    }

    /**
     * Retourne le badge HTML pour le statut
     */
    protected function getStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">En attente</span>',
            'approved' => '<span class="badge bg-success">Approuvé</span>',
            'rejected' => '<span class="badge bg-danger">Rejeté</span>'
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }
}
