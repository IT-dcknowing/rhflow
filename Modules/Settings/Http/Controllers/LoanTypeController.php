<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Settings\Models\LoanType;
use App\Models\Company;

class LoanTypeController extends Controller
{
    /**
     * Afficher la liste des types de prêts
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $search = $request->get('search', '');
        
        $loanTypes = LoanType::forCompany($companyId)
            ->when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        return view('settings::loan-types.index', compact('loanTypes', 'search'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $company = Company::findOrFail(auth()->user()->company_id);
        return view('settings::loan-types.create', compact('company'));
    }

    /**
     * Enregistrer un nouveau type de prêt
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'repayment_period_min' => 'required|integer|min:1',
            'repayment_period_max' => 'required|integer|min:1|gte:repayment_period_min',
            'requires_guarantor' => 'boolean',
            'guarantor_conditions' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Le nom du type de prêt est obligatoire',
            'max_amount.required' => 'Le montant maximum est obligatoire',
            'interest_rate.required' => 'Le taux d\'intérêt est obligatoire',
            'repayment_period_min.required' => 'La période minimale est obligatoire',
            'repayment_period_max.required' => 'La période maximale est obligatoire',
            'repayment_period_max.gte' => 'La période maximale doit être supérieure ou égale à la période minimale'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Veuillez corriger les erreurs dans le formulaire');
        }

        try {
            $loanType = LoanType::create([
                'company_id' => auth()->user()->company_id,
                'name' => $request->name,
                'description' => $request->description,
                'max_amount' => $request->max_amount,
                'interest_rate' => $request->interest_rate,
                'repayment_period_min' => $request->repayment_period_min,
                'repayment_period_max' => $request->repayment_period_max,
                'requires_guarantor' => $request->boolean('requires_guarantor', false),
                'guarantor_conditions' => $request->guarantor_conditions,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            return redirect()
                ->route('company.settings.loan-types.index')
                ->with('success', 'Type de prêt créé avec succès');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du type de prêt: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du type de prêt');
        }
    }

    /**
     * Afficher les détails d'un type de prêt
     */
    public function show($id)
    {
        $loanType = LoanType::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);
            
        return view('settings::loan-types.show', compact('loanType'));
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit($id)
    {
        $loanType = LoanType::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);
            
        $company = Company::findOrFail(auth()->user()->company_id);
        
        return view('settings::loan-types.edit', compact('loanType', 'company'));
    }

    /**
     * Mettre à jour un type de prêt
     */
    public function update(Request $request, $id)
    {
        $loanType = LoanType::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'repayment_period_min' => 'required|integer|min:1',
            'repayment_period_max' => 'required|integer|min:1|gte:repayment_period_min',
            'requires_guarantor' => 'boolean',
            'guarantor_conditions' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Le nom du type de prêt est obligatoire',
            'max_amount.required' => 'Le montant maximum est obligatoire',
            'interest_rate.required' => 'Le taux d\'intérêt est obligatoire',
            'repayment_period_min.required' => 'La période minimale est obligatoire',
            'repayment_period_max.required' => 'La période maximale est obligatoire',
            'repayment_period_max.gte' => 'La période maximale doit être supérieure ou égale à la période minimale'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Veuillez corriger les erreurs dans le formulaire');
        }

        try {
            $loanType->update([
                'name' => $request->name,
                'description' => $request->description,
                'max_amount' => $request->max_amount,
                'interest_rate' => $request->interest_rate,
                'repayment_period_min' => $request->repayment_period_min,
                'repayment_period_max' => $request->repayment_period_max,
                'requires_guarantor' => $request->boolean('requires_guarantor', false),
                'guarantor_conditions' => $request->guarantor_conditions,
                'is_active' => $request->boolean('is_active', true),
                'updated_by' => auth()->id()
            ]);

            return redirect()
                ->route('company.settings.loan-types.index')
                ->with('success', 'Type de prêt mis à jour avec succès');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour du type de prêt: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du type de prêt');
        }
    }

    /**
     * Supprimer un type de prêt
     */
    public function destroy($id)
    {
        $loanType = LoanType::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        try {
            // Vérifier si des prêts sont associés à ce type
            if ($loanType->loans()->exists()) {
                return redirect()
                    ->back()
                    ->with('error', 'Impossible de supprimer ce type de prêt car des prêts y sont associés');
            }

            $loanType->delete();

            return redirect()
                ->route('company.settings.loan-types.index')
                ->with('success', 'Type de prêt supprimé avec succès');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du type de prêt: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la suppression du type de prêt');
        }
    }

    /**
     * Activer/Désactiver un type de prêt
     */
    public function toggleStatus($id)
    {
        $loanType = LoanType::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        try {
            $loanType->update([
                'is_active' => !$loanType->is_active,
                'updated_by' => auth()->id()
            ]);

            $status = $loanType->is_active ? 'activé' : 'désactivé';

            return redirect()
                ->back()
                ->with('success', "Type de prêt {$status} avec succès");

        } catch (\Exception $e) {
            \Log::error('Erreur lors du changement de statut: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors du changement de statut');
        }
    }
}
