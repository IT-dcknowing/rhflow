<?php

namespace App\Http\Controllers;

use App\Models\AllowanceOption;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllowanceOptionController extends Controller
{
    /**
     * Afficher la liste des options d'allocation
     */
    public function index()
    {
        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        $allowanceOptions = AllowanceOption::byCompany($companyId)
            ->active()
            ->orderBy('name')
            ->get();

        return response()->json($allowanceOptions);
    }

    /**
     * Créer une nouvelle option d'allocation
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'param_fiscal' => 'nullable|string|max:255',
            'param_social' => 'nullable|string|max:255',
            'type' => 'required|in:default,created',
        ]);

        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        $allowanceOption = AllowanceOption::create([
            'name' => $request->name,
            'param_fiscal' => $request->param_fiscal,
            'param_social' => $request->param_social,
            'type' => $request->type,
            'company_id' => $companyId,
        ]);

        return response()->json($allowanceOption, 201);
    }

    /**
     * Afficher une option d'allocation spécifique
     */
    public function show($id)
    {
        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        $allowanceOption = AllowanceOption::byCompany($companyId)
            ->active()
            ->findOrFail($id);

        return response()->json($allowanceOption);
    }

    /**
     * Mettre à jour une option d'allocation
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'param_fiscal' => 'nullable|string|max:255',
            'param_social' => 'nullable|string|max:255',
            'type' => 'required|in:default,created',
        ]);

        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        $allowanceOption = AllowanceOption::byCompany($companyId)->findOrFail($id);

        $allowanceOption->update($request->all());

        return response()->json($allowanceOption);
    }

    /**
     * Supprimer une option d'allocation
     */
    public function destroy($id)
    {
        $companyId = Auth::user()->company_id ?? null;

        if (!$companyId) {
            return response()->json(['error' => 'Aucune entreprise trouvée'], 404);
        }

        $allowanceOption = AllowanceOption::byCompany($companyId)->findOrFail($id);

        // Soft delete
        $allowanceOption->delete();

        return response()->json(['message' => 'Option d\'allocation supprimée avec succès']);
    }
}
