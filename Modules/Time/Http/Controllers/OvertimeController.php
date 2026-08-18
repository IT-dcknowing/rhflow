<?php

namespace Modules\Time\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Modules\Time\Models\Overtime;
use Modules\Employees\Models\Employee; 
use App\Models\PaieExercice;
use App\Models\PaiePeriode;
use App\Models\Allowance;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    /**
     * Afficher la liste des heures supplémentaires
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
       
        $periode = null;
        if ($request->has("periode_id")) {
            $periode = PaiePeriode::with("exercice")->findOrFail(
                $request->periode_id,
            );
            // Récupérer le mois sélectionné ou le mois en cours
            $selectedMonth = $request->input('month', $periode->date_debut->format('Y-m'));
        }else{
            // Récupérer le mois sélectionné ou le mois en cours
            $selectedMonth = $request->input('month', date('Y-m'));
        }
        
        $exercices = PaieExercice::with("periodes")
            ->where("company_id", Auth::user()->company_id)
            ->orderBy("date_debut", "desc")
            ->get();

        $currentYear = date('Y');
        $currentMonth = date('m');
        
        // Récupérer les employés en fonction des permissions
        $user = Auth::user();
        $employees = [];
        
        if ($user->type == 'employee') {
            // Si l'utilisateur est un employé, on ne récupère que ses propres heures supplémentaires
            $employee = $user->employee;
            $overtimes = Overtime::where('employee_id', $employee->id ?? 0)
                ->where('start_date', 'LIKE', "%$selectedMonth%")
                ->orderBy('start_date', 'desc')
                ->get();
        } else {
            // Si l'utilisateur est un administrateur ou un manager, on récupère tous les employés
            $employees = Employee::where('is_active', 1)
                        ->where('company_id', $user->company_id)
                        ->get();
                        
            $filtreEmployees = Employee::where('is_active', 1)
                        ->where('company_id', $user->company_id)
                        ->get()
                        ->pluck('id','name');

            // Récupérer les heures supplémentaires en fonction des filtres
            $overtimes = Overtime::query();
            
            // Filtrer par mois
            $overtimes->where('start_date', 'LIKE', "%$selectedMonth%");
            
            // Si l'utilisateur n'est pas un super admin, on filtre par entreprise
            if ($user->type != 'super admin') {
                $overtimes->where('company_id', $user->company_id);
            }
            
            $overtimes = $overtimes->orderBy('start_date', 'desc')->get();
        }
        
        return view('time::overtimes.index', compact(
            'overtimes', 
            'employees', 
            'filtreEmployees',
            'selectedMonth', 
            'currentYear',
            'currentMonth',
            'periode',
            'exercices'  
        ));
    }
    
    /**
     * Enregistrer une nouvelle heure supplémentaire
     * Taux légaux — Décret n°96-203 du 7 mars 1996 :
     *   quar_heure           : +15%  (41e–46e h/semaine)
     *   heure_audd           : +50%  (47e–55e h/semaine)
     *   heure_nuit_ferie     : +75%  (nuit JO 21h–05h)
     *   heure_dim_ferie      : +75%  (dimanche/férié journée)
     *   heure_nuit_dim_ferie : +100% (nuit dimanche/férié)
     */
    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'employee_id'          => 'required|exists:employees,id',
            'periode_id'           => 'required|exists:paie_periodes,id',
            'start_date'           => 'required|date',
            'end_date'             => 'required|date|after_or_equal:start_date',
            'quar_heure'           => 'nullable|numeric|min:0',
            'heure_audd'           => 'nullable|numeric|min:0',
            'heure_nuit_ferie'     => 'nullable|numeric|min:0',
            'heure_dim_ferie'      => 'nullable|numeric|min:0',
            'heure_nuit_dim_ferie' => 'nullable|numeric|min:0',
            'taux_hour'            => 'required|numeric|min:0',
            'montant'              => 'required|numeric|min:0',
            'remark'               => 'nullable|string',
        ]);
        
        try {
            $employee = Employee::findOrFail($validatedData['employee_id']);
            
            $h15   = (float) ($validatedData['quar_heure']            ?? 0);
            $h50   = (float) ($validatedData['heure_audd']             ?? 0);
            $h75a  = (float) ($validatedData['heure_nuit_ferie']       ?? 0);
            $h75b  = (float) ($validatedData['heure_dim_ferie']        ?? 0);
            $h100  = (float) ($validatedData['heure_nuit_dim_ferie']   ?? 0);
            $taux  = (float)  $validatedData['taux_hour'];

            // --- Calcul serveur selon taux légaux ivoiriens (Décret n°96-203) ---
            $montant_legal = round(
                ($h15  * $taux * 1.15) +
                ($h50  * $taux * 1.50) +
                ($h75a * $taux * 1.75) +
                ($h75b * $taux * 1.75) +
                ($h100 * $taux * 2.00)
            );

            // Logger un avertissement si l'écart avec le montant soumis dépasse 5%
            $montant_soumis = (float) $validatedData['montant'];
            if ($montant_legal > 0 && abs($montant_soumis - $montant_legal) / $montant_legal > 0.05) {
                \Log::warning("HS: écart > 5% pour employé #{$validatedData['employee_id']} "
                    . "soumis={$montant_soumis} calculé={$montant_legal} (Décret n°96-203)");
            }

            $totalHours = $h15 + $h50 + $h75a + $h75b + $h100;
            
            $overtime = new Overtime();
            $overtime->employee_id          = $validatedData['employee_id'];
            $overtime->periode_id           = $validatedData['periode_id'];
            $overtime->start_date           = $validatedData['start_date'];
            $overtime->end_date             = $validatedData['end_date'];
            $overtime->quar_heure           = $h15;
            $overtime->heure_audd           = $h50;
            $overtime->heure_nuit_ferie     = $h75a;
            $overtime->heure_dim_ferie      = $h75b;
            $overtime->heure_nuit_dim_ferie = $h100;
            $overtime->taux_hour            = $taux;
            // Utiliser le montant calculé légalement (ou soumis si pas d'heures détaillées)
            $overtime->montant              = $montant_legal > 0 ? $montant_legal : $montant_soumis;
            $overtime->statut               = 'pending';
            $overtime->company_id           = Auth::user()->company_id;
            $overtime->save();
            
            return response()->json([
                'success' => true,
                'message' => __('Heure supplémentaire enregistrée avec succès.'),
                'data'    => $overtime
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création des heures supplémentaires: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Afficher les détails d'une heure supplémentaire
     */
    public function show($id)
    {
        $overtime = Overtime::with('employee')->findOrFail($id);
        
        // Vérifier les autorisations
        $this->checkPermissions($overtime);
        
        return response()->json([
            'success' => true,
            'data' => $overtime
        ]);
    }
    
    /**
     * Afficher le formulaire de modification d'une heure supplémentaire
     */
    public function edit($id)
    {
        $overtime = Overtime::with('employee')->findOrFail($id);
        
        // Vérifier les autorisations
        $this->checkPermissions($overtime);
        
        // Récupérer la liste des employés pour le select
        $employees = [];
        if (Auth::user()->type != 'employee') {
            $employees = Employee::where('company_id', Auth::user()->company_id)
                ->get();
        }
        
        return view('time::overtimes.modals.edit', compact('overtime', 'employees'));
    }
    
    /**
     * Mettre à jour une heure supplémentaire
     */
    public function update(Request $request, $id)
    {
        $overtime = Overtime::findOrFail($id);
        
        // Vérifier les autorisations
        $this->checkPermissions($overtime);
        
        // Validation des données
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'quar_heure' => 'nullable|numeric|min:0',
            'heure_audd' => 'nullable|numeric|min:0',
            'heure_nuit_ferie' => 'nullable|numeric|min:0',
            'heure_dim_ferie' => 'nullable|numeric|min:0',
            'heure_nuit_dim_ferie' => 'nullable|numeric|min:0',
            'taux_hour' => 'required|numeric|min:0',
            'montant' => 'required|numeric|min:0',
            'remark' => 'nullable|string',
        ]);
        
        try {
            // Calculer le nombre d'heures total
            $totalHours = $validatedData['quar_heure'] + $validatedData['heure_audd'] + 
                         $validatedData['heure_nuit_ferie'] + $validatedData['heure_dim_ferie'] + 
                         $validatedData['heure_nuit_dim_ferie'];
            
            // Mettre à jour l'heure supplémentaire
            $overtime->update($validatedData);
            
            return redirect()->back()->with('success','Heure supplémentaire mise à jour avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création des heures supplémentaires: ' . $e->getMessage());
            return redirect()->back()->with('error','Une erreur est survenue lors de la mise à jour de l\'heure supplémentaire.');
        }
    }
    
    /**
     * Supprimer une heure supplémentaire
     */
    public function destroy($id)
    {
        $overtime = Overtime::findOrFail($id);
        
        // Vérifier les autorisations
        $this->checkPermissions($overtime);
        
        try {
            // Supprimer l'heure supplémentaire
            $overtime->delete();
            
            return response()->json([
                'success' => true,
                'message' => __('Heure supplémentaire supprimée avec succès.')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Une erreur est survenue lors de la suppression de l\'heure supplémentaire.') . ' ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Marquer une heure supplémentaire comme payée
     */
    public function markAsPaid($id)
    {
        $overtime = Overtime::findOrFail($id);
        $employee = Employee::findOrFail($overtime->employee_id);
        
        // Vérifier les autorisations
        $this->checkPermissions($overtime);
        
        try {
            // Mettre à jour le statut de paiement
            $overtime->update([
                'paid' => 'paid',
                'statut' => 'approved'
            ]);

            $allowance = new Allowance();
            $allowance->code = '103';
            $allowance->code_compta = '6611';
            $allowance->employee_id = $overtime->employee_id;
            $allowance->allowance_option_id = 3; // Utiliser $value qui contient item_brut
            $allowance->periode_id = $overtime->periode_id;
            $allowance->title = 'Heures Supplémentaires';
            $allowance->trait_fisc = 'exo 0%';
            $allowance->trait_cnps = 'Soumis';
            $allowance->base_heures = 0;
            $allowance->jours_leave = 0;
            $allowance->amount_imp = round($overtime->montant);
            $allowance->amount = round($overtime->montant);
            $allowance->montant = round($overtime->montant);
            $allowance->details = $overtime->remark;
            $allowance->jours_work = $employee->tax_payer_id;
            $allowance->type_amount = 0;
            $allowance->type = "fixed";
            $allowance->company_id = Auth::user()->company_id;
            $allowance->created_by = Auth::user()->id;
            $allowance->save();
            
            return response()->json([
                'success' => true,
                'message' => __('Heure supplémentaire marquée comme payée avec succès.'),
                'data' => $overtime
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Une erreur est survenue lors du marquage de l\'heure supplémentaire comme payée.') . ' ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAsUnpaid($id){
        $overtime = Overtime::findOrFail($id);
        
        // Vérifier les autorisations
        $this->checkPermissions($overtime);
        
        try {
            // Mettre à jour le statut de paiement
            $overtime->update([
                'paid' => 'unpaid',
                'statut' => 'rejected'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => __('Heure supplémentaire marquée comme non payée avec succès.'),
                'data' => $overtime
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Une erreur est survenue lors du marquage de l\'heure supplémentaire comme non payée.') . ' ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Exporter les heures supplémentaires au format Excel
     */
    public function export(Request $request)
    {
        $selectedMonth = $request->input('month', date('Y-m'));
        $user = Auth::user();
        
        // Récupérer les heures supplémentaires en fonction des filtres
        $overtimes = Overtime::query()
            ->with('employee')
            ->where('start_date', 'LIKE', "%$selectedMonth%");
        
        // Si l'utilisateur n'est pas un super admin, on filtre par entreprise
        if ($user->type != 'super admin') {
            $overtimes->where('company_id', $user->company_id);
        }
        
        $overtimes = $overtimes->orderBy('start_date', 'desc')->get();
        
        // Générer le fichier Excel
        $fileName = 'heures_supplementaires_' . $selectedMonth . '.xlsx';
        
        // Utiliser la classe d'exportation Excel (à créer)
        return (new OvertimeExport($overtimes))->download($fileName);
    }
    
    /**
     * Vérifier les autorisations de l'utilisateur
     */
    private function checkPermissions($overtime)
    {
        $user = Auth::user();
        
        // Si l'utilisateur est un employé, il ne peut accéder qu'à ses propres heures supplémentaires
        if ($user->type == 'employee') {
            $employeeId = $user->employee->id ?? 0;
            if ($overtime->employee_id != $employeeId) {
                abort(403, 'Accès non autorisé.');
            }
        }
        // Si l'utilisateur est un manager ou admin, il ne peut accéder qu'aux heures supplémentaires de son entreprise
        elseif ($user->type != 'super_admin') {
            if ($overtime->company_id != $user->company_id) {
                abort(403, 'Accès non autorisé.');
            }
        }
    }
}
