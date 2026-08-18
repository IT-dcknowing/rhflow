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
            $periode = PaiePeriode::with("exercice")->findOrFail(
                $request->periode_id,
            );
        }
        
        $exercices = PaieExercice::with("periodes")
            ->where("company_id", auth()->user()->company_id)
            ->orderBy("date_debut", "desc")
            ->get();
        
        // Récupération des congés avec pagination et chargement des relations
        $leaves = Leave::with([
                'employee', 
                'leaveType',
                'periode.exercice'
            ])
            ->where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
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
            $periode = PaiePeriode::with("exercice")->findOrFail(
                $request->periode_id,
            );
        }
        
        $leaveTypes = LeaveType::where('company_id', $company_id)
            ->where('is_active', 1)
            ->get();
            
        $employees = Employee::where('company_id', $company_id)
            ->where('is_active', 1)
            ->get();
            
        return view('leaves::create', compact('leaveTypes', 'employees', 'periode'));
    }

    /**
     * Enregistre une nouvelle demande de congé
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
                'employee_id' => 'required',
                'leave_type_id' => 'required',
                'start_date' => 'required|date',
                'back_date' => 'required|date',
                'end_date' => 'required|date',
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
        $leave->month_leave = json_encode($request->month_leave);
        $leave->sb_leave = json_encode($request->salaire_brut);
        $leave->days_leave = json_encode($request->total_jours);
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
            
        return view('leaves::show', compact('leave'));
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
            $leave->month_leave = json_encode($request->month_leave);
            $leave->sb_leave = json_encode($request->salary_brut);
            $leave->days_leave = json_encode($request->nbre_jour);
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
        
        if($request->status == 'Approuvé'){
            $employee = Employee::find($leave->employee_id);

            if($leave->amount_leave >= 0){
                $allowance = new Allowance();
                $allowance->code = '123';
                $allowance->code_compta = '6613';
                $allowance->employee_id = $leave->employee_id;
                $allowance->allowance_option_id = 23; // Utiliser $value qui contient item_brut
                $allowance->periode_id = $leave->periode_id;
                $allowance->title = 'Allocation congé';
                $allowance->trait_fisc = 'exo 0%';
                $allowance->trait_cnps = 'Soumis';
                $allowance->base_heures = 0;
                $allowance->jours_leave = 0;
                $allowance->amount_imp = round($leave->amount_leave);
                $allowance->amount = round($leave->amount_leave);
                $allowance->montant = round($leave->amount_leave);
                $allowance->details = $leave->leave_reason;
                $allowance->jours_work = $employee->tax_payer_id;
                $allowance->type_amount = 0;
                $allowance->type = "fixed";
                $allowance->company_id = Auth::user()->company_id;
                $allowance->created_by = Auth::user()->id;
                $allowance->save();
            }
        }
        
        return redirect()->back()
            ->with('success', 'Statut de la demande mis à jour avec succès.');
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
        return response()->json([
                                    'leave_date' => date_format($employee_leave_date,"Y-m-d"),
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
