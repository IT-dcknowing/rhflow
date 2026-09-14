<?php

namespace Modules\NatureAvantage\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\NatureAvantage\Models\Avantage;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Allowance;
use App\Models\PaiePeriode;
use App\Models\PaieExercice;
use Modules\Employees\Models\Employee;

class NatureAvantageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
       
        $periode = null;
        if ($request->has("periode_id")) {
            // Une période supprimée ne doit pas produire un 404 : on retombe sur
            // l'écran de sélection de période avec un message.
            $periode = PaiePeriode::with("exercice")
                ->where("company_id", Auth::user()->company_id)
                ->find($request->periode_id);

            if (!$periode) {
                session()->flash("error", "Cette période de paie n'existe plus ou n'appartient pas à votre entreprise.");
            }
        } else {
            // Pick the latest 'en_cours' period by default
            $periode = PaiePeriode::with("exercice")
                ->where('company_id', $company_id)
                ->where('statut', 'en_cours')
                ->orderBy('date_debut', 'desc')
                ->first();
            
            // If no active period, pick the absolute latest period
            if (!$periode) {
                $periode = PaiePeriode::with("exercice")
                    ->where('company_id', $company_id)
                    ->orderBy('date_debut', 'desc')
                    ->first();
            }
        }
        
        $exercices = PaieExercice::with("periodes")
            ->where("company_id", $company_id)
            ->orderBy("date_debut", "desc")
            ->get();

        $company = Auth::user()->company;
        
        // Filter advantages by period if one exists
        $query = Avantage::where('company_id', $company->id);
        if ($periode) {
            $query->where('periode_id', $periode->id);
        }
        $avantages = $query->get();
        
        return view('natureavantage::index', compact('avantages', 'periode', 'exercices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $periode = null;
        if ($request->has("periode_id")) {
            // Une période supprimée ne doit pas produire un 404 : on retombe sur
            // l'écran de sélection de période avec un message.
            $periode = PaiePeriode::with("exercice")
                ->where("company_id", Auth::user()->company_id)
                ->find($request->periode_id);

            if (!$periode) {
                session()->flash("error", "Cette période de paie n'existe plus ou n'appartient pas à votre entreprise.");
            }
        }
        $employees = Employee::where('company_id', Auth::user()->company_id)->where('is_active', 1)->get();
        $company = Auth::user()->company;
        $branches = Branch::where('company_id', $company->id)->get();
        
        return view('natureavantage::create', compact('branches', 'periode', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'type_avantage' => 'required',
            'traitement' => 'required',
            'amount_real' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $company = Auth::user()->company;

        $amount_nature = $request->montant_logement + $request->montant_mobilier + $request->electric + $request->montant_eau + $request->amount_garde + $request->amount_maison + $request->amount_cuisto;
        $amount_nature2 = $request->amount_garde + $request->amount_maison + $request->amount_cuisto;

        $avantages = new Avantage();
        $avantages->employee_Id = $request->employee_id;
        $avantages->periode_id = $request->periode_id;
        $avantages->type_avantage = $request->type_avantage;
        if ($request->type_avantage == 'avantage_en_nature') {
            if ($request->input('amount_garde') || $request->input('amount_maison') || $request->input('amount_cuisto')) {
                $avantages->libelle = $request->description . ', Domesticité, ' . $request->autre_description . '.';
            } else {
                $avantages->libelle = $request->description . ', ' . $request->autre_description . '.';
            }
        } else {
            $avantages->libelle = $request->description;
        }
        $avantages->amount_reel = $request->amount_real;
        $avantages->amount = $amount_nature;
        $avantages->taxe_its = $amount_nature2;
        $avantages->taxe_cnps = $request->montant_avantage;
        $avantages->traitement = $request->traitement;
        $avantages->status = 'pending';
        $avantages->is_active = 1;
        $avantages->company_id = $company->id;
        $avantages->save();

        $this->_recalculateTaxesForEmployee($request->employee_id, $request->periode_id);

        return redirect()->route('company.avantages.show', $avantages->id)
            ->with('success', 'Nature d\'avantage créée avec succès.');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $company = Auth::user()->company;
        $avantage = Avantage::where('company_id', $company->id)->findOrFail($id);
        
        return view('natureavantage::show', compact('avantage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $company = Auth::user()->company;
        $avantage = Avantage::where('company_id', $company->id)->findOrFail($id);
        $employees = Employee::where('company_id', Auth::user()->company_id)->where('is_active', 1)->get();
        $branches = Branch::where('company_id', $company->id)->get();
        
        return view('natureavantage::edit', compact('avantage', 'branches', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'type_avantage' => 'required',
            'traitement' => 'required',
            'amount_real' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $company = Auth::user()->company;
        $avantage = Avantage::where('company_id', $company->id)->findOrFail($id);
        
        $amount_nature = $request->montant_logement + $request->montant_mobilier + $request->electric + $request->montant_eau;
        $amount_nature2 = $request->amount_garde + $request->amount_maison + $request->amount_cuisto;

        $avantage->employee_id = $request->employee_id;
        $avantage->type_avantage = $request->type_avantage;
        if ($request->type_avantage == 'avantage_en_nature') {
            if ($request->input('amount_garde') || $request->input('amount_maison') || $request->input('amount_cuisto')) {
                $avantage->libelle = $request->description . ', Domesticité, ' . $request->autre_description . '.';
            } else {
                $avantage->libelle = $request->description . ', ' . $request->autre_description . '.';
            }
        } else {
            $avantage->libelle = $request->description;
        }
        $avantage->amount_reel = $request->amount_real;
        $avantage->amount = $amount_nature;
        $avantage->taxe_its = $amount_nature2;
        $avantage->taxe_cnps = $request->montant_avantage;
        $avantage->traitement = $request->traitement;
        $avantage->save();

        $this->_recalculateTaxesForEmployee($avantage->employee_id, $avantage->periode_id);

        return redirect()->route('company.avantages.show', $avantage->id)
            ->with('success', 'Nature d\'avantage mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $company = Auth::user()->company;
        $avantage = Avantage::where('company_id', $company->id)->findOrFail($id);
        
        $employee_id = $avantage->employee_id ?: $avantage->employee_Id; // Fallback for case
        $periode_id = $avantage->periode_id;
        
        $avantage->delete();

        $this->_recalculateTaxesForEmployee($employee_id, $periode_id);

        return redirect()->back()
            ->with('success', 'Nature d\'avantage supprimée avec succès.');
    }
    
    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(Request $request, $id)
    {
        $company = Auth::user()->company;
        $avantage = Avantage::where('company_id', $company->id)->findOrFail($id);
        
        $avantage->is_active = !$avantage->is_active;
        if($request->status == 'suspend'){
            $avantage->status = 'reject';
        }else{
            $avantage->status = 'approved';
        }
        $avantage->save();
        
        $this->_recalculateTaxesForEmployee($avantage->employee_id, $avantage->periode_id);
        
        return redirect()->back()
            ->with('success', 'Statut de la nature d\'avantage modifié avec succès.');
    }
    
    /**
     * Recalculate default taxes for an employee after an advantage change.
     */
    private function _recalculateTaxesForEmployee($employee_id, $periode_id)
    {
        if (!$periode_id || !$employee_id) return;
        
        $employee = \Modules\Employees\Models\Employee::find($employee_id);
        if (!$employee) return;
        
        $periode = \App\Models\PaiePeriode::find($periode_id);
        if (!$periode || $periode->statut === 'cloture') return;

        app(\App\Services\SalaryService::class)->appliquerRetenuesLegales($employee, $periode);
    }
}
