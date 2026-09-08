<?php

namespace Modules\Ruptures\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Modules\Ruptures\Models\Rupture;
use Modules\Ruptures\Models\RuptureType;
use Modules\Employees\Models\Employee;
use Modules\Loans\Models\Loan;
use Modules\Loans\Models\LoanPayment;
use Modules\Contracts\Models\Contract;
use App\Models\Company;
use App\Models\Allowance;
use App\Models\PaieExercice;
use App\Models\PaiePeriode;
use App\Models\JobCategorie;
use App\Models\Designation;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RupturesController extends Controller
{
    /**
     * Affiche la liste des ruptures
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
        }
        
        $exercices = PaieExercice::with("periodes")
            ->where("company_id", Auth::user()->company_id)
            ->orderBy("date_debut", "desc")
            ->get();

        $ruptures = Rupture::with(['employee', 'ruptureType'])
                    ->where("company_id", Auth::user()->company_id)
                    ->where("periode_id", $request->periode_id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        $employees = Employee::where('company_id', Auth::user()->company_id)->where('is_active', 1)->get();
        $types = RuptureType::all();
        
        return view('ruptures::index', compact('ruptures', 'employees', 'types', 'periode', 'exercices'));
    }

    /**
     * Affiche les ruptures d'un employé spécifique
     */
    public function ruptureEmployee($employee_id)
    {
        $employee = Employee::findOrFail($employee_id);
        $ruptures = Rupture::with(['ruptureType'])
            ->where('employee_id', $employee_id)
            ->where('company_id', Auth::user()->company_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $types = RuptureType::all();
        
        return view('ruptures::index', compact('ruptures', 'employee', 'types'));
    }

    /**
     * Affiche le formulaire de création d'une rupture
     */
    public function create(Request $request)
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
        } 
        
        $employees = Employee::where('company_id', Auth::user()->company_id)->where('is_active', 1)->get();
        $types = RuptureType::all();
        
        return view('ruptures::create', compact('employees', 'types', 'periode'));
    }

    /**
     * Enregistre une nouvelle rupture
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'employee_id' => 'required|exists:employees,id',
            'rupture_type_id' => 'required|exists:rupture_types,id',
            'request_date' => 'required|date',
            'effective_date' => 'required|date',
            'description' => 'nullable|string',
        ]);


        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $total = ($request->gratification_amount_input+$request->conge_2021_amount_input+ $request->preavis_amount_input+$request->aggravation_preavis_amount_input+$request->licenciement_amount_input+$request->dommages_interets_amount_input)-($request->amount_cnps+$request->amount_its+$request->amount_loan);

        $rupture                   = new Rupture();
        $rupture->employee_id      = $request->employee_id;
        $rupture->rupture_type_id  = $request->rupture_type_id;
        $rupture->contract_id      = $request->contract_id;
        $rupture->periode_id       = $request->periode_id;
        $rupture->notice_date      = Carbon::parse($request->request_date)->format('Y-m-d');
        $rupture->termination_date = Carbon::parse($request->effective_date)->format('Y-m-d');
        $rupture->indem_comp       = $request->gratification_amount_input;
        $rupture->indem_comp_cong  = $request->conge_2021_amount_input;
        $rupture->imdem_prea       = $request->preavis_amount_input;
        $rupture->indem_licence    = $request->aggravation_preavis_amount_input;
        $rupture->aggravation      = $request->licenciement_amount_input;
        $rupture->dom_inter		   = $request->dommages_interets_amount_input;
        $rupture->amount_cnps      = $request->amount_cnps;
        $rupture->amount_its       = $request->amount_its;
        $rupture->amount_loan      = $request->amount_loan;
        $rupture->solde            = $total;
        $rupture->description      = $request->description;
        $rupture->status           = 'pending';
        $rupture->company_id       = Auth::user()->company_id;
        $rupture->save();

        $employee = Employee::find($rupture->employee_id);

        if ($request->rupture_type_id == '6') {
            $employee->is_active = 3;
            $employee->statut_emp = 'Mise à pied sans salaire';
            $employee->save();
        }

        if ($request->rupture_type_id == '7') {
            $employee->is_active = 3;
            $employee->statut_emp = 'Avertissement';
            $employee->save();
        }

        if ($request->rupture_type_id != '7' || $request->rupture_type_id != '6'){
            $contrat   = Contract::find($request->contract_id);
            if($contrat){
                $contrat->end_date    = Carbon::parse($request->effective_date)->format('Y-m-d');
                $contrat->status      = 'terminated';
                $contrat->save();

                $employee->is_active = 2;
                $employee->statut_emp = 'Fin de contrat';
                $employee->save();
            }
        }

        return redirect()->route('company.ruptures.show', $rupture->id)
            ->with('success', 'Rupture créée avec succès.');
    }

    /**
     * Affiche les détails d'une rupture
     */
    public function show($id)
    {
        $rupture = Rupture::with(['employee', 'ruptureType'])->findOrFail($id);
        
        return view('ruptures::show', compact('rupture'));
    }

    /**
     * Affiche le formulaire de modification d'une rupture
     */
    public function edit($id)
    {
        $rupture = Rupture::with(['employee', 'ruptureType'])->findOrFail($id);
        $employees = Employee::where('company_id', Auth::user()->company_id)->where('is_active', 1)->get();
        $ruptureTypes = RuptureType::all();
        $contracts = Contract::where('company_id', Auth::user()->company_id)
                    ->where('id', $rupture->contract_id)->first();
        return view('ruptures::edit', compact('rupture', 'employees', 'ruptureTypes', 'contracts'));
    }

    /**
     * Met à jour une rupture existante
     */
    public function update(Request $request, $id)
    {
        $rupture = Rupture::findOrFail($id);
          
        $validator = Validator::make($request->all(), [ 
            'employee_id' => 'required|exists:employees,id',
            'rupture_type_id' => 'required|exists:rupture_types,id',
            'request_date' => 'required|date',
            'effective_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $total = ($request->gratification_amount_input+$request->conge_2021_amount_input+ $request->preavis_amount_input+$request->aggravation_preavis_amount_input+$request->licenciement_amount_input+$request->dommages_interets_amount_input)-($request->amount_cnps+$request->amount_its+$request->amount_loan);

        $rupture->employee_id      = $request->employee_id;
        $rupture->rupture_type_id  = $request->rupture_type_id;
        $rupture->contract_id      = $request->contract_id;
        $rupture->notice_date      = Carbon::parse($request->request_date)->format('Y-m-d');
        $rupture->termination_date = Carbon::parse($request->effective_date)->format('Y-m-d');
        $rupture->indem_comp       = $request->gratification_amount_input;
        $rupture->indem_comp_cong  = $request->conge_2021_amount_input;
        $rupture->imdem_prea       = $request->preavis_amount_input;
        $rupture->aggravation      = $request->aggravation_preavis_amount_input;
        $rupture->indem_licence    = $request->licenciement_amount_input;
        $rupture->dom_inter		   = $request->dommages_interets_amount_input;
        $rupture->amount_cnps      = $request->amount_cnps;
        $rupture->amount_its       = $request->amount_its;
        $rupture->amount_loan      = $request->amount_loan;
        $rupture->solde            = $total;
        $rupture->description      = $request->description;
        
        if ($request->rupture_type_id != '7' || $request->rupture_type_id != '6'){
            $employee = Employee::find($rupture->employee_id);

            $employee->is_active = false;
            $employee->statut_emp = 'Fin de contrat';
            $employee->save();

            $contrat   = Contract::find($request->contract_id);
            if($contrat){
                $contrat->end_date    = Carbon::parse($request->effective_date)->format('Y-m-d');
                $contrat->status      = 'terminated';
                $contrat->save();
            }
        }
        
        if ($request->has('status')) {
            $rupture->status = $request->status;
        }
        
        $rupture->save();

        return redirect()->route('company.ruptures.show', $rupture->id)
            ->with('success', 'Rupture mise à jour avec succès.');
    }

    /**
     * Supprime une rupture
     */
    public function destroy($id)
    {
        $rupture = Rupture::findOrFail($id);
        
        // Supprimer les pièces jointes
        foreach ($rupture->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->path);
            $attachment->delete();
        }
        
        $rupture->delete();
        
        return redirect()->route('company.ruptures.index')
            ->with('success', 'Rupture supprimée avec succès.');
    }

    /**
     * Change le statut d'une rupture
     */
    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'description' => 'nullable|string',
        ]);

        $rupture = Rupture::findOrFail($id);
        $rupture->status = $request->status;
        
        if ($request->has('description')) {
            $rupture->description = $rupture->description ? $rupture->description . "\n" . $request->description : $request->description;
        }
        
        $rupture->save();

        if($request->status == 'completed'){
            $employee = Employee::find($rupture->employee_id);

            if($rupture->indem_comp > 0){
                $allowance = new Allowance();
                $allowance->code = '138';
                $allowance->code_compta = '6613';
                $allowance->employee_id = $rupture->employee_id;
                $allowance->allowance_option_id = 38; // Utiliser $value qui contient item_brut
                $allowance->periode_id = $rupture->periode_id;
                $allowance->title = 'Droits des Indemnités';
                $allowance->trait_fisc = 'exo 0%';
                $allowance->trait_cnps = 'Soumis';
                $allowance->base_heures = 0;
                $allowance->jours_leave = 0;
                $allowance->amount_imp = round($rupture->indem_comp);
                $allowance->amount = round($rupture->indem_comp);
                $allowance->montant = round($rupture->indem_comp);
                $allowance->details = $rupture->description;
                $allowance->jours_work = $employee->tax_payer_id;
                $allowance->type_amount = 0;
                $allowance->type = "fixed";
                $allowance->company_id = Auth::user()->company_id;
                $allowance->created_by = Auth::user()->id;
                $allowance->save();
            }
            /*
            if($rupture->indem_comp_cong > 0){
                $allowance = new Allowance();
                $allowance->code = '139';
                $allowance->code_compta = '6613';
                $allowance->employee_id = $rupture->employee_id;
                $allowance->allowance_option_id = 38; // Utiliser $value qui contient item_brut
                $allowance->periode_id = $rupture->periode_id;
                $allowance->title = 'Indemnité compensatrice de congé';
                $allowance->trait_fisc = 'exo 0%';
                $allowance->trait_cnps = 'Soumis';
                $allowance->base_heures = 0;
                $allowance->jours_leave = 0;
                $allowance->amount_imp = round($rupture->indem_comp_cong);
                $allowance->amount = round($rupture->indem_comp_cong);
                $allowance->montant = round($rupture->indem_comp_cong);
                $allowance->details = $rupture->description;
                $allowance->jours_work = $employee->tax_payer_id;
                $allowance->type_amount = 0;
                $allowance->type = "fixed";
                $allowance->company_id = Auth::user()->company_id;
                $allowance->created_by = Auth::user()->id;
                $allowance->save();
            }

            if($rupture->imdem_prea > 0){
                $allowance = new Allowance();
                $allowance->code = '140';
                $allowance->code_compta = '6614';
                $allowance->employee_id = $rupture->employee_id;
                $allowance->allowance_option_id = 38; // Utiliser $value qui contient item_brut
                $allowance->periode_id = $rupture->periode_id;
                $allowance->title = 'Indemnité de préavis';
                $allowance->trait_fisc = 'exo 0%';
                $allowance->trait_cnps = 'Soumis';
                $allowance->base_heures = 0;
                $allowance->jours_leave = 0;
                $allowance->amount_imp = round($rupture->imdem_prea);
                $allowance->amount = round($rupture->imdem_prea);
                $allowance->montant = round($rupture->imdem_prea);
                $allowance->details = $rupture->description;
                $allowance->jours_work = $employee->tax_payer_id;
                $allowance->type_amount = 0;
                $allowance->type = "fixed";
                $allowance->company_id = Auth::user()->company_id;
                $allowance->created_by = Auth::user()->id;
                $allowance->save();
            }

            if($rupture->indem_licence > 0){
                $allowance = new Allowance();
                $allowance->code = '141';
                $allowance->code_compta = '6614';
                $allowance->employee_id = $rupture->employee_id;
                $allowance->allowance_option_id = 38; // Utiliser $value qui contient item_brut
                $allowance->periode_id = $rupture->periode_id;
                $allowance->title = 'Indemnité de licenciement';
                $allowance->trait_fisc = 'exo 0%';
                $allowance->trait_cnps = 'Soumis';
                $allowance->base_heures = 0;
                $allowance->jours_leave = 0;
                $allowance->amount_imp = round($rupture->indem_licence);
                $allowance->amount = round($rupture->indem_licence);
                $allowance->montant = round($rupture->indem_licence);
                $allowance->details = $rupture->description;
                $allowance->jours_work = $employee->tax_payer_id;
                $allowance->type_amount = 0;
                $allowance->type = "fixed";
                $allowance->company_id = Auth::user()->company_id;
                $allowance->created_by = Auth::user()->id;
                $allowance->save();
            }

            if($rupture->aggravation > 0){
                $allowance = new Allowance();
                $allowance->code = '142';
                $allowance->code_compta = '6614';
                $allowance->employee_id = $rupture->employee_id;
                $allowance->allowance_option_id = 38; // Utiliser $value qui contient item_brut
                $allowance->periode_id = $rupture->periode_id;
                $allowance->title = 'Aggravation de l\'indemnité compensatrice de préavis';
                $allowance->trait_fisc = 'exo 0%';
                $allowance->trait_cnps = 'Soumis';
                $allowance->base_heures = 0;
                $allowance->jours_leave = 0;
                $allowance->amount_imp = round($rupture->aggravation);
                $allowance->amount = round($rupture->aggravation);
                $allowance->montant = round($rupture->aggravation);
                $allowance->details = $rupture->description;
                $allowance->jours_work = $employee->tax_payer_id;
                $allowance->type_amount = 0;
                $allowance->type = "fixed";
                $allowance->company_id = Auth::user()->company_id;
                $allowance->created_by = Auth::user()->id;
                $allowance->save();
            }

            if($rupture->dom_inter > 0){
                $allowance = new Allowance();
                $allowance->code = '143';
                $allowance->code_compta = '6614';
                $allowance->employee_id = $rupture->employee_id;
                $allowance->allowance_option_id = 38; // Utiliser $value qui contient item_brut
                $allowance->periode_id = $rupture->periode_id;
                $allowance->title = 'Domages-intérêts';
                $allowance->trait_fisc = 'exo 0%';
                $allowance->trait_cnps = 'Soumis';
                $allowance->base_heures = 0;
                $allowance->jours_leave = 0;
                $allowance->amount_imp = round($rupture->dom_inter);
                $allowance->amount = round($rupture->dom_inter);
                $allowance->montant = round($rupture->dom_inter);
                $allowance->details = $rupture->description;
                $allowance->jours_work = $employee->tax_payer_id;
                $allowance->type_amount = 0;
                $allowance->type = "fixed";
                $allowance->company_id = Auth::user()->company_id;
                $allowance->created_by = Auth::user()->id;
                $allowance->save();
            }

            if($rupture->solde > 0){
                $allowance = new Allowance();
                $allowance->code = '144';
                $allowance->code_compta = '6614';
                $allowance->employee_id = $rupture->employee_id;
                $allowance->allowance_option_id = 38; // Utiliser $value qui contient item_brut
                $allowance->periode_id = $rupture->periode_id;
                $allowance->title = 'Droit de rupture';
                $allowance->trait_fisc = 'exo 0%';
                $allowance->trait_cnps = 'Soumis';
                $allowance->base_heures = 0;
                $allowance->jours_leave = 0;
                $allowance->amount_imp = round($rupture->solde);
                $allowance->amount = round($rupture->solde);
                $allowance->montant = round($rupture->solde);
                $allowance->details = $rupture->description;
                $allowance->jours_work = $employee->tax_payer_id;
                $allowance->type_amount = 0;
                $allowance->type = "fixed";
                $allowance->company_id = Auth::user()->company_id;
                $allowance->created_by = Auth::user()->id;
                $allowance->save();
            }*/
        }
        
        return redirect()->back()->with('success', 'Statut de la rupture mis à jour avec succès.');
    }

    /**
     * Affiche la liste des types de rupture
     */
    public function types()
    {
        $types = RuptureType::withCount('ruptures')->paginate(10);
        
        return view('ruptures::types-rupture', compact('types'));
    }

    /**
     * Enregistre un nouveau type de rupture
     */
    public function storeType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rupture_types,name',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        RuptureType::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'company_id' => Auth::user()->company_id,
        ]);

        return redirect()->route('company.ruptures.types')
            ->with('success', 'Type de rupture créé avec succès.');
    }

    /**
     * Met à jour un type de rupture existant
     */
    public function updateType(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rupture_types,name,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $type = RuptureType::findOrFail($id);
        $type->name = $request->name;
        $type->description = $request->description;
        $type->is_active = $request->has('is_active') ? 1 : 0;
        $type->company_id = Auth::user()->company_id;
        $type->save();

        return redirect()->route('company.ruptures.types')
            ->with('success', 'Type de rupture mis à jour avec succès.');
    }

    /**
     * Supprime un type de rupture
     */
    public function destroyType($id)
    {
        $type = RuptureType::findOrFail($id);
        
        // Vérifier si le type est utilisé par des ruptures
        if ($type->ruptures()->count() > 0) {
            return redirect()->route('company.ruptures.types')
                ->with('error', 'Ce type de rupture ne peut pas être supprimé car il est utilisé par des ruptures existantes.');
        }
        
        $type->delete();
        
        return redirect()->route('company.ruptures.types')
            ->with('success', 'Type de rupture supprimé avec succès.');
    }

    // ================================================================
    // CALCULS LÉGAUX AUTOMATIQUES — Rupture de contrat
    // Référence : Décret n°96-200 (Préavis) / n°96-201 (Licenciement)
    //             CCI Art. 34 / Note n°01224 DGI 2022 (Fiscalité)
    // ================================================================

    /**
     * Endpoint API JSON — Pré-remplit le formulaire de rupture
     * avec les montants légaux calculés automatiquement.
     * Route : GET /company/ruptures/calcul-droits/{employee_id}
     */
    public function calculerDroitsRupture(Request $request, $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);
        $date_rupture = $request->input('date_rupture', now()->toDateString());

        // Ancienneté
        $date_entree = $employee->company_doj ?? $employee->start_date;
        $debut = \Carbon\Carbon::parse($date_entree);
        $fin   = \Carbon\Carbon::parse($date_rupture);
        $mois_anciennete  = (int) $debut->diffInMonths($fin);
        $annees_completes = (int) $debut->diffInYears($fin);

        // Calculs
        $preavis   = $this->calculerPreavis($employee, $annees_completes, $mois_anciennete);
        $licence   = $this->calculerIndemniteLicenciement($employee, $annees_completes, $fin);

        return response()->json([
            'employee_name'       => $employee->name,
            'date_entree'         => $date_entree,
            'anciennete_annees'   => $annees_completes,
            'anciennete_mois'     => $mois_anciennete,
            'preavis'             => $preavis,
            'licenciement'        => $licence,
        ]);
    }

    /**
     * Calcule la durée et l'indemnité de préavis légal.
     * Référence : CCI Art. 34 / Décret n°96-200 du 7 mars 1996
     *
     * Grille :
     *  - Journalier/Ouvrier (cat ≤ 5) : < 6 mois → 8j | 6m-1an → 15j | > 1an → 1 mois
     *  - Employé mensuel (cat ≤ 5)    : ≤ 6 ans → 1 mois | 6-11 ans → 2 mois
     *  - Cadre/Maîtrise (cat ≥ 6)     : toute ancienneté → 3 mois
     */
    private function calculerPreavis(Employee $employee, int $annees, int $mois_total): array
    {
        $smm = $this->getSalaireMoyenMensuel($employee);
        $categorie = intval($employee->categorie ?? 0);
        $contrat   = strtolower($employee->contrat ?? '');

        $duree_jours  = 0;
        $duree_libelle = '';

        $est_journalier = str_contains($contrat, 'journal') || str_contains($contrat, 'journa');
        $est_cadre      = ($categorie >= 6);

        if ($est_cadre) {
            // Cadres et maîtrise : 3 mois quelle que soit l'ancienneté
            $duree_jours   = 90;
            $duree_libelle = '3 mois';
        } elseif ($est_journalier || $categorie <= 5) {
            if ($mois_total < 6) {
                $duree_jours   = 8;
                $duree_libelle = '8 jours';
            } elseif ($mois_total < 12) {
                $duree_jours   = 15;
                $duree_libelle = '15 jours';
            } elseif ($annees < 6) {
                $duree_jours   = 30;
                $duree_libelle = '1 mois';
            } else {
                $duree_jours   = 60;
                $duree_libelle = '2 mois';
            }
        } else {
            // Défaut mensuel non-cadre
            if ($annees < 6) {
                $duree_jours   = 30;
                $duree_libelle = '1 mois';
            } elseif ($annees < 11) {
                $duree_jours   = 60;
                $duree_libelle = '2 mois';
            } else {
                $duree_jours   = 90;
                $duree_libelle = '3 mois';
            }
        }

        $montant_preavis = round(($smm / 30) * $duree_jours);

        return [
            'duree_jours'   => $duree_jours,
            'duree_libelle' => $duree_libelle,
            'smm'           => $smm,
            'montant'       => $montant_preavis,
        ];
    }

    /**
     * Calcule l'indemnité de licenciement.
     * Référence : Décret n°96-201 du 7 mars 1996, Art. 3
     *
     * Barème :
     *  - De 1 à 5 ans :  SMM × 30% × n1
     *  - De 6 à 10 ans : SMM × 35% × n2
     *  - > 10 ans :      SMM × 40% × n3
     *
     * Régime fiscal (Note n°01224/MBPE/DGI du 12 avril 2022) :
     *  - Indemnité ≤ 75 000 FCFA → exonérée totalement
     *  - Indemnité > 75 000 FCFA → 50% imposable à l'IRS
     */
    private function calculerIndemniteLicenciement(Employee $employee, int $annees, \Carbon\Carbon $date_rupture): array
    {
        if ($annees < 1) {
            return [
                'montant'         => 0,
                'detail'          => 'Moins d\'1 an d\'ancienneté — pas d\'indemnité',
                'imposable'       => 0,
                'exonere'         => 0,
                'taux_imposition' => '0%',
            ];
        }

        $smm = $this->getSalaireMoyenMensuel($employee);

        // Calcul par tranches cumulées
        $n1 = min($annees, 5);
        $n2 = max(0, min($annees - 5, 5));  // années entre 6 et 10
        $n3 = max(0, $annees - 10);           // années > 10

        $tranche1 = round($smm * 0.30 * $n1);
        $tranche2 = round($smm * 0.35 * $n2);
        $tranche3 = round($smm * 0.40 * $n3);
        $total    = $tranche1 + $tranche2 + $tranche3;

        // Régime fiscal
        if ($total <= 75000) {
            $imposable = 0;
            $exonere   = $total;
            $taux_impo = '0% (≤ 75 000 FCFA)';
        } else {
            $imposable = round($total * 0.50);
            $exonere   = $total - $imposable;
            $taux_impo = '50% imposable (IRS)';
        }

        return [
            'montant'         => $total,
            'smm'             => $smm,
            'detail'          => "Tranche 1-5 ans : {$tranche1} | Tranche 6-10 ans : {$tranche2} | Tranche +10 ans : {$tranche3}",
            'n1'              => $n1, 'n2' => $n2, 'n3' => $n3,
            'imposable'       => $imposable,
            'exonere'         => $exonere,
            'taux_imposition' => $taux_impo,
        ];
    }

    /**
     * Calcule le Salaire Moyen Mensuel des 12 derniers mois (ou fallback sur salaire actuel).
     */
    private function getSalaireMoyenMensuel(Employee $employee): float
    {
        $payslips = \Modules\PaieSalaries\Models\PaySlip
            ::where('employee_id', $employee->id)
            ->orderBy('salary_month', 'desc')
            ->limit(12)
            ->get();

        if ($payslips->count() > 0) {
            return round($payslips->avg('salary_brut'));
        }
        return (float) $employee->salary;
    }

    public function getContractType($employeeId)
	{
		$employees = Employee::where('id', $employeeId)
                             ->where('is_active','=', 1)
							 ->where('company_id', Auth::user()->company_id)
							 ->first();

		// Vérifiez si l'employé existe
		if ($employees) {
			$employeeUserId = $employees->id;
            $loan  = 0;
            $nbre  = $employees->parts;
			$contracts = Contract::with('type')
								 ->where('status', 'accept')
								 ->where('company_id', Auth::user()->company_id)
								 ->where('employee_id', $employeeUserId)
								 ->first();

            $loans = Loan::with('payments')
                        ->where('employee_id', $employees->id)
                        ->where('statut','running')
                        ->where('company_id', '=', Auth::user()->company_id)
                        ->get();

            $loan = $loans->sum('amount') - $loans->sum(function($loan) {
                return $loan->payments->sum('amount');
            });

			// Retournez les données au format JSON
			return response()->json([
				'type_contrat' => $contracts->type->name,
				'type_contrat_id' => $contracts->type->id,
                'contract_id' => $contracts->id,
                'amount_loan' => $loan,
                'parts' => $nbre,
			]);
		} else {
			// Retournez une réponse vide si aucun employé n'est trouvé
			return response()->json(['type_contrat' => null]);
		}
	}

    public function getContractTypeend($employeeId)
	{
		$employees = Employee::where('id', $employeeId)
							 ->where('company_id', Auth::user()->company_id)
							 ->first();

		// Vérifiez si l'employé existe
		if ($employees) {
			$employeeUserId = $employees->id;

			$contracts = Contract::with('type')
								 ->where('status', 'decline')
								 ->where('company_id', Auth::user()->company_id)
								 ->where('employee_id', $employeeUserId)
								 ->first();

			// Retournez les données au format JSON
			return response()->json([
				'type_contrat' => $contracts->type->name,
				'type_contrat_id' => $contracts->type->id,
			]);
		} else {
			// Retournez une réponse vide si aucun employé n'est trouvé
			return response()->json(['type_contrat' => null]);
		}
	}

    public function downloadDecomptePdf($id)
    {
        $users = Auth::user();
        $company = Company::find($users->company_id);
        $date = date('Y-m-d');
        $terminations = Rupture::where('id', $id)->where('company_id', '=', $users->company_id)->first();
        $employees = Employee::where('employee_id', '=', $terminations->employee_id)->where('company_id', $users->company_id)->first();
        $categories    =  JobCategorie::select('*')->get();
        $emp_cate = $employees->categorie;
        $emp_ids  = $employees->id;
        $emp_name = $employees->name;
        $emp_cnps = $employees->num_cnps;
        $emp_doj  = $employees->company_doj;
        $genre    = $employees->gender;
        $poste   = Designation::where('id', $employees->designation_id)->first();
        $emp_end_date  = $employees->end_date;

        return view('ruptures::template.Decomptepdf', compact('employees','date', 'company', 'genre', 'terminations', 'emp_ids', 'categories', 'emp_name', 'emp_end_date', 'poste', 'emp_cnps', 'emp_doj', 'emp_doj', 'emp_cate'));
    }

    public function downloadDecompteDoc($id)
    {
        $users = Auth::user();

        $company = Company::find($users->company_id);
        $date = date('Y-m-d');
        $terminations = Rupture::where('id', $id)->where('company_id', '=', $users->company_id)->first();
        $employees = Employee::where('employee_id', '=', $terminations->employee_id)->where('company_id', $users->company_id)->first();
        $categories    =  JobCategorie::select('*')->get();
        $emp_cate = $employees->categorie;
        $emp_ids  = $employees->id;
        $emp_name = $employees->name;
        $emp_cnps = $employees->num_cnps;
        $emp_doj  = $employees->company_doj;
        $genre    = $employees->gender;
        $poste   = Designation::where('id', $employees->designation_id)->first();
        $emp_end_date  = $employees->end_date;

        return view('ruptures::template.Decomptedocx', compact('employees','date', 'company', 'genre', 'terminations', 'emp_ids', 'categories', 'emp_name', 'emp_end_date', 'poste', 'emp_cnps', 'emp_doj', 'emp_doj', 'emp_cate'));
    }

    public function downloadRelevePdf($id)
    {
        $users = Auth::user();

        $date = date('Y-m-d');
        $terminations = Rupture::where('id', $id)->where('company_id', '=', $users->company_id)->first();
        $employees = Employee::where('employee_id', '=', $terminations->employee_id)->where('company_id', $users->company_id)->first();
        $company = Company::find($users->company_id);
        $secs = strtotime($company->company_start_time) - strtotime("00:00");
        $result = date("H:i", strtotime($company->company_end_time) - $secs);
        $categories    =  JobCategorie::select('*')->get();
        $emp_cate = $employees->categorie;
        $emp_ids  = $employees->id;
        $emp_name = $employees->name;
        $emp_cnps = $employees->num_cnps;
        $emp_doj  = $employees->company_doj;
        $genre    = $employees->gender;
        $poste   = Designation::where('id', $employees->designation_id)->first();
        $emp_end_date  = $employees->end_date;

        return view('ruptures::template.Relevepdf', compact('employees','date', 'company', 'genre', 'terminations', 'emp_ids', 'categories', 'emp_name', 'emp_end_date', 'poste', 'emp_cnps', 'emp_doj', 'emp_doj', 'emp_cate'));
    }

    public function downloadReleveDoc($id)
    {
        $users = Auth::user();

        $date = date('Y-m-d');
        $terminations = Rupture::where('id', $id)->where('company_id', '=', $users->company_id)->first();
        $employees = Employee::where('employee_id', '=', $terminations->employee_id)->where('company_id', $users->company_id)->first();
        $company = Company::find($users->company_id);
        $categories    =  JobCategorie::select('*')->get();
        $emp_cate = $employees->categorie;
        $emp_ids  = $employees->id;
        $emp_name = $employees->name;
        $emp_cnps = $employees->num_cnps;
        $emp_doj  = $employees->company_doj;
        $genre    = $employees->gender;
        $poste   = Designation::where('id', $employees->designation_id)->first();
        $emp_end_date  = $employees->end_date;

        return view('ruptures::template.Relevedocx', compact('employees','date', 'genre', 'company', 'terminations', 'emp_ids', 'categories', 'emp_name', 'emp_end_date', 'poste', 'emp_cnps', 'emp_doj', 'emp_doj', 'emp_cate'));
    }

    public function downloadSoldePdf($id)
    {
        $users = Auth::user();

        $date = date('Y-m-d');
        $terminations = Rupture::where('id', $id)->where('company_id', '=', $users->company_id)->first();
        $employees = Employee::where('employee_id', '=', $terminations->employee_id)->where('company_id', $users->company_id)->first();
        $company = Company::find($users->company_id);
        $categories    =  JobCategorie::select('*')->get();
        $emp_cate = $employees->categorie;
        $emp_ids  = $employees->id;
        $emp_name = $employees->name;
        $emp_cnps = $employees->num_cnps;
        $emp_doj  = $employees->company_doj;
        $genre    = $employees->gender;
        $poste   = Designation::where('id', $employees->designation_id)->first();
        $emp_end_date  = $employees->end_date;

        return view('ruptures::template.Soldepdf', compact('employees','date', 'genre', 'company', 'terminations', 'emp_ids', 'categories', 'emp_name', 'emp_end_date', 'poste', 'emp_cnps', 'emp_doj', 'emp_doj', 'emp_cate'));
    }

    public function downloadSoldeDoc($id)
    {
        $users = Auth::user();

        $date = date('Y-m-d');
        $terminations = Rupture::where('id', $id)->where('company_id', '=', $users->company_id)->first();
        $employees = Employee::where('employee_id', '=', $terminations->employee_id)->where('company_id', $users->company_id)->first();
        $company = Company::find($users->company_id);
        $categories    =  JobCategorie::select('*')->get();
        $emp_cate = $employees->categorie;
        $emp_ids  = $employees->id;
        $emp_name = $employees->name;
        $emp_cnps = $employees->num_cnps;
        $emp_doj  = $employees->company_doj;
        $genre    = $employees->gender;
        $poste   = Designation::where('id', $employees->designation_id)->first();
        $emp_end_date  = $employees->end_date;

        return view('ruptures::template.Soldedocx', compact('employees','date', 'genre', 'company', 'terminations', 'emp_ids', 'categories', 'emp_name', 'emp_end_date', 'poste', 'emp_cnps', 'emp_doj', 'emp_doj', 'emp_cate'));
    }
}
