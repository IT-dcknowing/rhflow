<?php

namespace Modules\Leaves\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Facades\DB;
use Modules\Leaves\Models\Leave;
use Modules\Employees\Models\Employee;
use Modules\Leaves\Models\LeaveType;
use App\Models\PaieExercice;
use App\Models\PaiePeriode;
use App\Models\Allowance;
use Modules\PaieSalaries\Models\PaySlip;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class LeavesController extends Controller
{
    /**
     * Affiche la liste des demandes de congé
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
            ->where("company_id", auth()->user()->company_id)
            ->orderBy("date_debut", "desc")
            ->get();
        
        // Récupération des congés avec pagination et chargement des relations
        $leaves = Leave::with([
                'employee',
                'leaveType',
                'periode.exercice',
                'activatedPeriode'
            ])
            ->where('company_id', $company_id)
            ->when($request->filled('employee_id'), function ($query) use ($request) {
                $query->where('employee_id', $request->employee_id);
            })
            ->when($request->filled('leave_type_id'), function ($query) use ($request) {
                $query->where('leave_type_id', $request->leave_type_id);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $leaveTypes = LeaveType::where('company_id', $company_id)
            ->where('is_active', 1)
            ->get();
            
        $employees = Employee::where('company_id', $company_id)
            ->where('is_active', 1)
            ->get();

        if ($request->has("period")) {
            return redirect()->back()->with('success', 'Resultat du filtre.');
        }
            
        return view('leaves::index', compact('leaves', 'leaveTypes', 'employees', 'periode', 'exercices'));
    }

    /**
     * Affiche le formulaire de création d'une demande de congé
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
        
        $leaveTypes = LeaveType::where('company_id', $company_id)
            ->where('is_active', 1)
            ->get();

        $employees = Employee::where('company_id', $company_id)
            ->where('is_active', 1)
            ->get();

        // Périodes ouvertes, proposées quand on arrive sans periode_id (ex. depuis la fiche employé).
        $periodes = PaiePeriode::with('exercice')
            ->where('company_id', $company_id)
            ->whereIn('statut', ['brouillon', 'en_cours'])
            ->orderBy('date_debut', 'desc')
            ->get();

        // Employé pré-sélectionné quand on crée le congé depuis la liste des employés.
        $selectedEmployeeId = $request->employee_id;

        return view('leaves::create', compact('leaveTypes', 'employees', 'periode', 'periodes', 'selectedEmployeeId'));
    }

    /**
     * Enregistre une nouvelle demande de congé
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'employee_id' => 'required',
                'leave_type_id' => 'required',
                'periode_id' => 'required|exists:paie_periodes,id',
                'start_date' => 'required|date',
                'back_date' => 'required|date',
                'end_date' => 'required|date',
            ],
            [
                'periode_id.required' => 'Vous devez rattacher ce congé à une période de paie.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Vérifier si l'employé a déjà pris un congé
        $existingLeave = Leave::where('company_id', '=', Auth::user()->company_id)->orderBy('created_at', 'desc')->get();

        if($existingLeave->isNotEmpty()){
            foreach($existingLeave as $leave_exist){
                if($leave_exist->employee_id == $request->input('employee_id')){
                    if ($leave_exist->leave_type_id == $request->input('leave_type_id')) {
                        // Si un congé existe, vérifier son statut
                        if ($leave_exist->status == 'Pending') {
                            // Si le type de congé est le même, renvoyer un message d'erreur
                            return redirect()->back()->with('error', __('Un congé de ce type est déjà en attente d approbation.'));

                        }else if ($leave_exist->status == 'Approuvé') {

                            if ($leave_exist->end_date >= now()) {
                                // Si le type de congé est le même, renvoyer un message d'erreur
                                return redirect()->back()->with('error', __('Vous avez déjà un congé en cours de ce type.'));
                            } else {
                                return $this->saveLeave($request);
                            }
                        } else {
                            // Si l'employé n'a jamais pris de congé, enregistrer le nouveau congé
                            return $this->saveLeave($request);
                        }
                    }
                }else{
                    // Si l'employé n'a jamais pris de congé, enregistrer le nouveau congé
                    return $this->saveLeave($request);
                }
            }
        }else{
            // Si l'employé n'a jamais pris de congé, enregistrer le nouveau congé
            return $this->saveLeave($request);
        }
    }

    private function saveLeave($request)
    {
        // Convert empty or 'NaN' values to 0
        $amountLeave = is_numeric($request->allo_conge_brut) ? $request->allo_conge_brut : 0;
        $amountLeaveNet = is_numeric($request->allo_conge_net) ? $request->allo_conge_net : 0;

        $leave = new Leave();
        $leave->employee_id = $request->employee_id;
        $leave->periode_id = $request->periode_id;
        $leave->leave_type_id = $request->leave_type_id;
        $leave->applied_on = now();
        $leave->leave_back = $request->back_date;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->total_leave_days = $request->nb_jours_conge;
        $leave->leave_reason = $request->leave_reason;
        $leave->amount_leave = $amountLeave;
        $leave->amount_leave_net = $amountLeaveNet;
        $leave->month_leave = json_encode($request->month_leave ?? []);
        $leave->sb_leave = json_encode($request->salaire_brut ?? []);
        $leave->days_leave = json_encode($request->total_jours ?? []);
        $leave->remark = $request->periode_reference;
        $leave->status = 'Pending';
        $leave->leave_statut = 1;
        $leave->company_id = Auth::user()->company_id;
        $leave->created_by = Auth::user()->id;
        $leave->save();

        $employee = Employee::where('id', $request->employee_id)->firstOrFail();
        $employee->end_leave = $request->end_date;
        $employee->statut_emp = 'En attente congé';
        $employee->save();

        return redirect()->route('company.leaves.show', $leave->id)->with('success', __('Demande de congé créé avec succès.'));
    }

    
    /**
     * Affiche les détails d'une demande de congé
     */
    public function show($id)
    {
        $user = Auth::user();
        $leave = Leave::with([
                'employee', 
                'leaveType',
                'periode.exercice'  // Chargement de la relation periode avec son exercice
            ])
            ->where('company_id', $user->company_id)
            ->findOrFail($id);

        // Période à laquelle renvoie le bouton "Retour". La période d'origine du congé
        // peut avoir été supprimée (soft delete) : dans ce cas on retombe sur la période
        // ouverte la plus récente, sinon l'index afficherait l'écran de sélection.
        $retourPeriodeId = $leave->periode?->id
            ?? PaiePeriode::where('company_id', $user->company_id)
                ->whereIn('statut', ['brouillon', 'en_cours'])
                ->orderBy('date_debut', 'desc')
                ->value('id');

        return view('leaves::show', compact('leave', 'retourPeriodeId'));
    }
    
    /**
     * Affiche le formulaire de modification d'une demande de congé
     */
    public function edit($id)
    {
        $user = Auth::user();
        $leave = Leave::where('company_id', $user->company_id)
            ->findOrFail($id);
            
        $leaveTypes = LeaveType::where('company_id', $user->company_id)
            ->where('is_active', 1)
            ->get();

        $employees = Employee::where('company_id', $user->company_id)
            ->where('is_active', 1)
            ->get();

        
        $otherLeaves = Leave::where('company_id', $user->company_id)
            ->where('id', '!=', $id)
            ->get();
            
        return view('leaves::edit', compact('leave', 'leaveTypes', 'employees', 'otherLeaves'));
    }
    
    /**
     * Met à jour une demande de congé
     */
    public function update(Request $request, $id)
    {
        $leave = Leave::find($id);
        $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $employee = Employee::where('id', $request->employee_id)->firstOrFail();
        // Récupérer le type de congé

        $leave->employee_id = $request->employee_id;
        $leave->leave_type_id = $request->leave_type_id;
        $leave->leave_back = $request->back_date;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        if($request->leave_type_id == 1)
        {
            $leave->total_leave_days = $request->nb_jours_conge;
            $leave->amount_leave = $request->allo_conge_brut;
            $leave->amount_leave_net = $request->allo_conge_net;
            $leave->month_leave = json_encode($request->month_leave ?? []);
            $leave->sb_leave = json_encode($request->salary_brut ?? []);
            $leave->days_leave = json_encode($request->nbre_jour ?? []);
        }
        $leave->updated_by = Auth::user()->id;
        $leave->remark = $request->periode_reference;
        $leave->save();

        $employee->end_leave = $request->end_date;
        $employee->statut_emp = 'En attente congé';
        $employee->save();

        return redirect()->route('company.leaves.show', $leave->id)->with('success', 'Demande de congé mise à jour avec succès.');
    }
    
    /**
     * Supprime une demande de congé
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $leave = Leave::where('company_id', $user->company_id)
            ->findOrFail($id);
            
        // Vérifier si la demande peut être supprimée
        if (!in_array($leave->status, ['pending', 'rejected'])) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer une demande de congé déjà approuvée ou en cours.');
        }
        
        $leave->delete();
        
        return redirect()->route('company.leaves.index')
            ->with('success', 'Demande de congé supprimée avec succès.');
    }
    
    /**
     * Affiche le calendrier des congés
     */
    public function calendar()
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        
        $leaves = Leave::with(['employee', 'leaveType'])
            ->where('company_id', $company_id)
            ->whereIn('status', ['approved'])
            ->get()
            ->map(function($leave) {
                return [
                    'title' => $leave->employee->name . ' - ' . $leave->leaveType->name,
                    'start' => $leave->start_date,
                    'end' => Carbon::parse($leave->end_date)->addDay()->format('Y-m-d'), // Ajoute un jour pour inclure la fin
                    'color' => $this->getStatusColor($leave->status),
                    'url' => route('company.leaves.show', $leave->id)
                ];
            });
            
        return view('leaves::calendar', compact('leaves'));
    }
    
    /**
     * Change le statut d'une demande de congé
     */
    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approuvé,Rejeté',
            'leave_reason' => 'nullable|string|max:1000'
        ]);
        
        $user = Auth::user();
        $leave = Leave::where('company_id', $user->company_id)
            ->findOrFail($id);
            
        $leave->status = $request->status;
        $leave->leave_reason = $request->leave_reason;
        $leave->save();
        
        // L'approbation ne crée plus la ligne de paie : elle rend seulement le congé activable.
        // C'est l'activation explicite (activate) qui génère l'"Allocation congé".
        if ($request->status == 'Approuvé') {
            return redirect()->back()->with(
                'success',
                "Congé approuvé. Activez-le sur une période pour qu'il soit pris en compte dans la paie."
            );
        }

        return redirect()->back()
            ->with('success', 'Statut de la demande mis à jour avec succès.');
    }

    /**
     * Formulaire d'activation : choix de la période de paie et du montant de l'allocation.
     */
    public function activateForm($id)
    {
        $user = Auth::user();
        $leave = Leave::with(['employee', 'leaveType', 'periode'])
            ->where('company_id', $user->company_id)
            ->findOrFail($id);

        // Périodes encore ouvertes : une paie validée / payée ne doit plus bouger.
        $periodes = PaiePeriode::with('exercice')
            ->where('company_id', $user->company_id)
            ->whereIn('statut', ['brouillon', 'en_cours'])
            ->orderBy('date_debut', 'desc')
            ->get();

        return view('leaves::modals.activate', compact('leave', 'periodes'));
    }

    /**
     * Active le congé pour la paie d'une période : crée (ou restaure) la ligne "Allocation congé".
     */
    public function activate(Request $request, $id)
    {
        $user = Auth::user();
        $leave = Leave::where('company_id', $user->company_id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'periode_id' => 'required|exists:paie_periodes,id',
            'amount_leave' => 'required|numeric|min:0',
        ], [
            'periode_id.required' => 'Vous devez choisir une période de paie.',
            'amount_leave.required' => "Le montant de l'allocation congé est obligatoire.",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!$leave->isActivable()) {
            return redirect()->back()->with('error', 'Seul un congé approuvé peut être activé pour la paie.');
        }

        $periode = PaiePeriode::where('company_id', $user->company_id)->findOrFail($request->periode_id);

        if ($error = $this->paieLockError($periode, $leave->employee_id)) {
            return redirect()->back()->with('error', $error);
        }

        $amount = round((float) $request->amount_leave);
        $employee = Employee::findOrFail($leave->employee_id);

        \DB::beginTransaction();
        try {
            // Idempotent : on reprend la ligne existante du congé (même soft-deleted)
            // au lieu d'en créer une seconde.
            $allowance = Allowance::withTrashed()
                ->where('company_id', $user->company_id)
                ->where('code', $leave->allowanceCode())
                ->first();

            if (!$allowance) {
                $allowance = new Allowance();
                $allowance->code = $leave->allowanceCode();
                $allowance->created_by = $user->id;
            } else {
                if ($allowance->trashed()) {
                    $allowance->restore();
                }
                $allowance->updated_by = $user->id;
            }

            $allowance->code_compta = '6613';
            $allowance->employee_id = $leave->employee_id;
            $allowance->allowance_option_id = 23;
            $allowance->periode_id = $periode->id;
            $allowance->title = 'Allocation congé';
            $allowance->trait_fisc = 'exo 0%';
            $allowance->trait_cnps = 'Soumis';
            $allowance->base_heures = 0;
            $allowance->jours_leave = (int) $leave->total_leave_days;
            $allowance->amount_imp = $amount;
            $allowance->amount = $amount;
            $allowance->montant = $amount;
            $allowance->details = $leave->leave_reason;
            $allowance->jours_work = $employee->tax_payer_id;
            $allowance->type_amount = 0;
            $allowance->type = 'fixed';
            $allowance->is_active = 1;
            $allowance->company_id = $user->company_id;
            $allowance->save();

            $leave->is_active = true;
            $leave->allowance_id = $allowance->id;
            $leave->activated_periode_id = $periode->id;
            $leave->activated_at = now();
            $leave->amount_leave = $amount;
            $leave->updated_by = $user->id;
            $leave->save();

            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Activation congé échouée', ['leave_id' => $leave->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', "L'activation du congé a échoué : " . $e->getMessage());
        }

        return redirect()->back()->with(
            'success',
            "Congé activé sur la période « " . ($periode->nom ?? $periode->id) . " » : l'allocation entrera dans le bulletin."
        );
    }

    /**
     * Désactive le congé : la ligne "Allocation congé" sort de la paie.
     * Refusé si le bulletin de la période est déjà généré — une paie émise ne doit pas bouger.
     */
    public function deactivate($id)
    {
        $user = Auth::user();
        $leave = Leave::where('company_id', $user->company_id)->findOrFail($id);

        if (!$leave->is_active) {
            return redirect()->back()->with('error', "Ce congé n'est pas activé pour la paie.");
        }

        $periode = $leave->activated_periode_id
            ? PaiePeriode::where('company_id', $user->company_id)->find($leave->activated_periode_id)
            : null;

        if ($periode && $error = $this->paieLockError($periode, $leave->employee_id)) {
            return redirect()->back()->with('error', $error);
        }

        \DB::beginTransaction();
        try {
            $allowance = Allowance::where('company_id', $user->company_id)
                ->where('code', $leave->allowanceCode())
                ->first();

            if ($allowance) {
                // Soft delete : la ligne sort de tous les calculs de paie sans être perdue,
                // et reste restaurable si le congé est réactivé.
                $allowance->is_active = 0;
                $allowance->updated_by = $user->id;
                $allowance->save();
                $allowance->delete();
            }

            $leave->is_active = false;
            $leave->activated_periode_id = null;
            $leave->activated_at = null;
            $leave->updated_by = $user->id;
            $leave->save();

            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Désactivation congé échouée', ['leave_id' => $leave->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', "La désactivation du congé a échoué : " . $e->getMessage());
        }

        return redirect()->back()->with(
            'success',
            "Congé désactivé : l'allocation n'est plus prise en compte dans la paie."
        );
    }

    /**
     * Retourne un message d'erreur si la paie de cette période est déjà figée pour cet employé,
     * null si l'on peut encore y toucher. Une paie générée ou validée ne doit jamais être modifiée.
     */
    private function paieLockError(PaiePeriode $periode, $employeeId)
    {
        if (in_array($periode->statut, ['validee', 'payee', 'cloture', 'annulee'])) {
            return 'La période « ' . ($periode->nom ?? $periode->id) . ' » est ' . $periode->statut
                . ' : sa paie ne peut plus être modifiée.';
        }

        $payslipExists = PaySlip::where('company_id', Auth::user()->company_id)
            ->where('employee_id', $employeeId)
            ->where('periode_id', $periode->id)
            ->exists();

        if ($payslipExists) {
            return 'Le bulletin de cet employé est déjà généré pour cette période : la paie émise ne peut plus être modifiée.';
        }

        return null;
    }

    public function startLeave($id)
    {
        $id_type = '';
        $date = date('Y-m-d');

        // Mettez à jour la ligne correspondante dans la table "Leave"
        $Leave = Leave::find($id);
        if ($Leave) {
            $id_type = $Leave->leave_type_id;
            $Leave->start_date =  \Carbon\Carbon::createFromFormat('Y-m-d',$date);
            $Leave->status = 'Démarré';
            $Leave->save();

            $leavetype = LeaveType::find($id_type);

            // Mettre à jour la date de fin du congé pour l'employé
            $employee = Employee::findOrFail($Leave->employee_id);
            if ($leavetype) {
                $employee->statut_emp = $leavetype->title ?? 'En congé';
                $employee->is_active = 4;
                $employee->save();
            }

            return redirect()->back()->with('success', __('Congé mis à jour avec succès.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function endLeave($id)
    {
        $date = date('Y-m-d');

        // Mettez à jour la ligne correspondante dans la table "Leave"
        $Leave = Leave::find($id);
        if ($Leave) {
            $Leave->end_date =  \Carbon\Carbon::createFromFormat('Y-m-d',$date);
            $Leave->status = 'Terminé';
            $Leave->save();
            // Mettre à jour la date de fin du congé pour l'employé
            $employee = Employee::findOrFail($Leave->employee_id);
            $employee->end_leave = \Carbon\Carbon::createFromFormat('Y-m-d',$date);
            $employee->is_active = 1;
            $employee->statut_emp = NULL;
            $employee->save();
            return redirect()->back()->with('success', __('Congé mis à jour avec succès.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
    
    /**
     * Retourne la couleur associée à un statut
     */
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => '#ffc107', // Jaune
            'approved' => '#28a745', // Vert
            'rejected' => '#dc3545', // Rouge
            'cancelled' => '#6c757d' // Gris
        ];
        
        return $colors[strtolower($status)] ?? '#007bff';
    }

    public function datasLeave(Request $request)
    {
        $date = date('Y-m');
        $leave_counts = LeaveType::select(DB::raw('COALESCE(SUM(leaves.total_leave_days),0) AS total_leave, leave_types.title, leave_types.days,leave_types.id'))
            ->leftjoin(
                'leaves',
                function ($join) use ($request, $date) {
                    $join->on('leaves.leave_type_id', '=', 'leave_types.id');
                    $join->where('leaves.employee_id', '=', $request->employee_id);
                    $join->where('leaves.status', '=', 'Approved');
                    $join->whereBetween('leaves.created_at', [$date.'-01',$date.'-31']);
                }
            )->where('leave_types.company_id', '=', Auth::user()->company_id)->groupBy('leave_types.id')->get();

        return $leave_counts;
    }

    public function getEmployeeLeaveSB(Request $request)
    {
        $netMonth = PaySlip::where('company_id', Auth::user()->company_id)
                                ->where('employee_id', $request->employee_id)
                                ->get();

        $netPayables = PaySlip::where('company_id', Auth::user()->company_id)
                                ->where('employee_id', $request->employee_id)
                                ->get();

        // Retourner la date de congé et le nombre de bulletins de paie en JSON
        return response()->json([
                                    'netPayables' => $netPayables,
                                    'netMonth' => $netMonth,
                                ]);
    }

    public function getEmployeeLeaveDate(Request $request)
    {
        $employee_leave_date = Employee::where('company_id', Auth::user()->company_id)
                                        ->where('is_active', 1)
                                        ->where('id', $request->employee_id)
                                        ->pluck('end_leave')
                                        ->first();

        $employee_company_doj = Employee::where('company_id', Auth::user()->company_id)
                                        ->where('is_active', 1)
                                        ->where('id', $request->employee_id)
                                        ->pluck('company_doj')
                                        ->first();

        $employee_salary = Employee::where('company_id', Auth::user()->company_id)
                                        ->where('is_active', 1)
                                        ->where('id', $request->employee_id)
                                        ->pluck('salary')
                                        ->first();

        $employee_parts = Employee::where('company_id', Auth::user()->company_id)
                                        ->where('is_active', 1)
                                        ->where('id', $request->employee_id)
                                        ->pluck('parts')
                                        ->first();

        $type_emp = Employee::where('company_id', Auth::user()->company_id)
                                        ->where('is_active', 1)
                                        ->where('id', $request->employee_id)
                                        ->pluck('charge_expat')
                                        ->first();

        $name_emp = Employee::where('company_id', Auth::user()->company_id)
                                        ->where('is_active', 1)
                                        ->where('id', $request->employee_id)
                                        ->pluck('name')
                                        ->first();

        // Compter le nombre de bulletins de paie pour l'employé
        $payslipsCount = PaySlip::where('company_id', Auth::user()->company_id)
                                ->where('employee_id', $request->employee_id)
                                ->count();

        $netPayables = PaySlip::where('company_id', Auth::user()->company_id)
                                ->where('employee_id', $request->employee_id)
                                ->pluck('salary_brut', 'salary_month');

        // Retourner la date de congé et le nombre de bulletins de paie en JSON
        // end_leave est null pour un employé qui n'a jamais pris de congé : on retombe sur sa date d'embauche.
        return response()->json([
                                    'leave_date' => $employee_leave_date
                                        ? Carbon::parse($employee_leave_date)->format('Y-m-d')
                                        : ($employee_company_doj ? Carbon::parse($employee_company_doj)->format('Y-m-d') : null),
                                    'company_doj' => $employee_company_doj,
                                    'leave_salary' => $employee_salary,
                                    'payslips_count' => $payslipsCount,
                                    'netPayables' => $netPayables,
                                    'nb_parts' => $employee_parts,
                                    'type_emp' => $type_emp,
                                    'name_emp'=> $name_emp,
                                ]);
    }

    public function getLeaveData(Request $request)
    {
        $arrayJson = [];
            $data = Leave::where('company_id', Auth::user()->company_id)->get();

            foreach ($data as $val) {
                $end_date = date_create($val->end_date);
                date_add($end_date, date_interval_create_from_date_string("1 days"));
                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => !empty($val->leave_type_id) ? $val->leaveType->title : '',
                    "start" => $val->start_date,
                    "end" => date_format($end_date, "Y-m-d"),
                    "className" => $val->color,
                    "textColor" => '#FFF',
                    "allDay" => true,
                    "url" => route('leave.action', $val['id']),
                ];
            }

        return $arrayJson;
    }

    public function attestationForm($id)
    {
        // Si vous avez besoin du dernier congé approuvé:
        $leave = Leave::where('id', $id)
                    ->where(function($query) {
                        $query->where('status', 'Approuvé')
                              ->orWhere('status', 'Démarré')
                              ->orWhere('status', 'Terminé');
                    })
                    ->first();

        $employee = Employee::find($leave->employee_id);

        $netPayables = PaySlip::where('company_id', Auth::user()->company_id)
                ->where('employee_id', $leave->employee_id)
                ->get();

        if (!$leave) {
            return redirect()->back()->with('error', __('Permission réfusée.'));
        }
        return view('company.leaves.attestation', compact('employee', 'leave', 'netPayables'));
    }
}
