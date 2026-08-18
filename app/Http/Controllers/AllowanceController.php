<?php

namespace App\Http\Controllers;

use App\Models\Allowance;
use App\Models\AllowanceOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllowanceController extends Controller
{
    /**
     * Afficher la liste des allocations
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        $query = Allowance::byCompany($companyId)->active();

        // Filtres optionnels
        if ($request->has('employee_id')) {
            $query->byEmployee($request->employee_id);
        }

        if ($request->has('type')) {
            $query->byType($request->type);
        }

        if ($request->has('allowance_option_id')) {
            $query->where('allowance_option_id', $request->allowance_option_id);
        }

        $allowances = $query->with(['employee', 'allowanceOption'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($allowances);
    }

    /**
     * Créer une nouvelle allocation
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee_months,id',
            'allowance_option_id' => 'required|exists:allowance_options,id',
            'title' => 'required|string|max:255',
            'trait_fisc' => 'nullable|string|max:255',
            'trait_cnps' => 'nullable|string|max:255',
            'base_heures' => 'boolean',
            'amount' => 'required|numeric|min:0',
            'amount_imp' => 'numeric|min:0',
            'montant' => 'numeric|min:0',
            'jours_work' => 'integer|min:0',
            'jours_leave' => 'integer|min:0',
            'type' => 'required|in:fixed,variable',
            'type_amount' => 'boolean',
            'details' => 'nullable|string',
        ]);

        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        // Vérifier que l'option d'allocation appartient à la même entreprise
        $allowanceOption = AllowanceOption::byCompany($companyId)->findOrFail($request->allowance_option_id);

        $allowance = Allowance::create($request->all() + ['company_id' => $companyId]);

        return response()->json($allowance->load(['employee', 'allowanceOption']), 201);
    }

    /**
     * Afficher une allocation spécifique
     */
    public function show($id)
    {
        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        $allowance = Allowance::byCompany($companyId)
            ->active()
            ->with(['employee', 'allowanceOption'])
            ->findOrFail($id);

        return response()->json($allowance);
    }

    /**
     * Mettre à jour une allocation
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee_months,id',
            'allowance_option_id' => 'required|exists:allowance_options,id',
            'title' => 'required|string|max:255',
            'trait_fisc' => 'nullable|string|max:255',
            'trait_cnps' => 'nullable|string|max:255',
            'base_heures' => 'boolean',
            'amount' => 'required|numeric|min:0',
            'amount_imp' => 'numeric|min:0',
            'montant' => 'numeric|min:0',
            'jours_work' => 'integer|min:0',
            'jours_leave' => 'integer|min:0',
            'type' => 'required|in:fixed,variable',
            'type_amount' => 'boolean',
            'details' => 'nullable|string',
        ]);

        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        $allowance = Allowance::byCompany($companyId)->findOrFail($id);

        // Vérifier que l'option d'allocation appartient à la même entreprise
        AllowanceOption::byCompany($companyId)->findOrFail($request->allowance_option_id);

        $allowance->update($request->all());

        return response()->json($allowance->load(['employee', 'allowanceOption']));
    }

    /**
     * Supprimer une allocation
     */
    public function destroy($id)
    {
        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        $allowance = Allowance::byCompany($companyId)->findOrFail($id);

        // Soft delete
        $allowance->delete();

        return response()->json(['message' => 'Allocation supprimée avec succès']);
    }

    /**
     * Obtenir les allocations par employé
     */
    public function getByEmployee($employeeId)
    {
        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        $allowances = Allowance::byCompany($companyId)
            ->byEmployee($employeeId)
            ->active()
            ->with(['allowanceOption'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($allowances);
    }

    /**
     * Obtenir les statistiques des allocations
     */
    public function getStats()
    {
        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        $stats = [
            'total' => Allowance::byCompany($companyId)->active()->count(),
            'fixed' => Allowance::byCompany($companyId)->active()->fixed()->count(),
            'variable' => Allowance::byCompany($companyId)->active()->variable()->count(),
            'total_amount' => Allowance::byCompany($companyId)->active()->sum('amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Imprimer une allocation (sera implémentée plus tard)
     */
    public function print($id)
    {
        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        $allowance = Allowance::byCompany($companyId)
            ->active()
            ->with(['employee', 'allowanceOption'])
            ->findOrFail($id);

        // Logique d'impression (sera implémentée plus tard)
        return response()->json(['message' => 'Fonction d\'impression en cours de développement', 'data' => $allowance]);
    }
}
