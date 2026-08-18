<?php

namespace Modules\Employees\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employees\Models\Demande;
use Modules\Employees\Models\Employee;
use Modules\Loans\Models\Loan;
use Modules\Loans\Models\LoanOption;
use Modules\Time\Models\TimeSheet;
use Modules\Leaves\Models\Leave;
use App\Models\PaiePeriode;
use App\Models\PaieExercice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DemandeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company_id = auth()->user()->company_id;
        
        $demandes = Demande::with(['employee', 'company'])
            ->where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item->is_leave = false;
                return $item;
            });
            
        $leaves = Leave::with(['employee', 'company'])
            ->where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($leave) {
                $demande = new Demande();
                $demande->incrementing = false;
                $demande->keyType = 'string';
                $demande->id = 'L_' . $leave->id;
                $demande->employee_id = $leave->employee_id;
                $demande->employee = $leave->employee;
                $demande->company_id = $leave->company_id;
                $demande->company = $leave->company;
                $demande->categorie_demandes = 'absence';
                $demande->demande_types = $leave->leave_type_id;
                $demande->start_date = $leave->start_date;
                $demande->end_date = $leave->end_date;
                $demande->status = $leave->status;
                if ($demande->status === 'Approuvé') $demande->status = 'Approved';
                if ($demande->status === 'Rejeté') $demande->status = 'Rejected';
                $demande->is_leave = true;
                $demande->created_at = $leave->created_at;
                return $demande;
            });
            
        $allDemandes = $demandes->concat($leaves)->sortByDesc('created_at')->values();
        
        // Grouper par catégorie pour les onglets
        $demandesParType = $allDemandes->groupBy('categorie_demandes');
        
        return view('employees::demandes.index', compact('demandesParType'));
    }

    /**
     * Valider une demande et transférer vers la table appropriée
     */
    public function validateDemande(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $isLeave = str_starts_with($id, 'L_');
            $realId = $isLeave ? substr($id, 2) : $id;
            
            if ($isLeave) {
                $demande = Leave::findOrFail($realId);
                $statusField = $demande->status;
                $isPending = ($statusField === 'Pending' || $statusField === 'En attente');
            } else {
                $demande = Demande::findOrFail($realId);
                $isPending = ($demande->status === 'Pending');
            }
            
            if (!$isPending) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande a déjà été traitée'
                ]);
            }
            
            // Récupérer ou mettre à jour la période
            $periodeId = $request->input('periode_id');
            if ($periodeId) {
                $demande->periode_id = $periodeId;
                $demande->save();
            } elseif (!$demande->periode_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une période doit être sélectionnée'
                ]);
            }
            
            // Transférer selon la catégorie
            if (!$isLeave) {
                if ($demande->categorie_demandes === 'pret') {
                    $this->transferToLoan($demande);
                } elseif ($demande->categorie_demandes === 'absence') {
                    $this->transferToTimeSheet($demande);
                }
                $demande->status = 'Approved';
            } else {
                // C'est un Leave (Absence)
                $dummy = new Demande();
                $dummy->employee_id = $demande->employee_id;
                $dummy->periode_id = $demande->periode_id;
                $dummy->start_date = $demande->start_date;
                $dummy->end_date = $demande->end_date;
                $dummy->demande_reason = $demande->leave_reason;
                $dummy->file_path = null; 
                $dummy->company_id = $demande->company_id;
                $this->transferToTimeSheet($dummy);
                
                $demande->status = 'Approuvé';
            }
            
            $demande->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Demande validée avec succès'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur validation demande: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation de la demande'
            ]);
        }
    }
    
    /**
     * Rejeter une demande
     */
    public function rejectDemande(Request $request, $id)
    {
        try {
            $isLeave = str_starts_with($id, 'L_');
            $realId = $isLeave ? substr($id, 2) : $id;
            
            if ($isLeave) {
                $demande = Leave::findOrFail($realId);
                $statusField = $demande->status;
                $isPending = ($statusField === 'Pending' || $statusField === 'En attente');
            } else {
                $demande = Demande::findOrFail($realId);
                $isPending = ($demande->status === 'Pending');
            }
            
            if (!$isPending) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande a déjà été traitée'
                ]);
            }
            
            // Récupérer ou mettre à jour la période
            $periodeId = $request->input('periode_id');
            if ($periodeId) {
                $demande->periode_id = $periodeId;
                $demande->save();
            } elseif (!$demande->periode_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une période doit être sélectionnée'
                ]);
            }
            
            $demande->status = $isLeave ? 'Rejeté' : 'Rejected';
            $demande->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Demande rejetée avec succès'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur rejet demande: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rejet de la demande'
            ]);
        }
    }
    
    /**
     * Transférer une demande de prêt vers la table loans
     */
    private function transferToLoan(Demande $demande)
    {
        $libelle = '';
        $types = [
            'maladie'            => 'Maladie',
            'rdv_medical'        => 'Rendez-vous médical',
            'urgence_familiale'  => 'Urgence familiale',
            'formation'          => 'Formation',
            'pret_person'        => 'Prêt personnel',
            'avance_salaire'     => 'Avance sur salaire',
            'pret_immobilier'    => 'Prêt immobilier',
            'materiel'           => 'Matériel de travail',
            'formation_demande'  => 'Demande de formation',
            'amenagement_horaire'=> 'Aménagement horaire',
            'autre'              => 'Autre',
        ];

        $libelle = $types[$demande->demande_types] ?? 'Demande Type';


        $loanOption = LoanOption::where('name', 'LIKE', '%' . $libelle . '%')->where('company_id', $demande->company_id)->first();

        if (!$loanOption) {
            $loanOption = LoanOption::updateOrCreate([
                'name' => $libelle,
                'company_id' => $demande->company_id
            ]);
        }

        // Calculer la date de fin si non fournie
        $startDate = \Carbon\Carbon::parse($demande->start_date);
        $endDate = \Carbon\Carbon::parse($demande->end_date);

        // Calculer la différence
        $nbre_mois = round($startDate->diffInMonths($endDate));
        
        // Créer le prêt
        Loan::create([
            'employee_id' => $demande->employee_id,
            'periode_id' => $demande->periode_id,
            'branche_id' => Employee::find($demande->employee_id)?->branch_id,
            'loan_option' => $loanOption->id,///
            'title' => $libelle,
            'amount' => $demande->montant,
            'amount_deduc' => round($demande->montant / $nbre_mois),
            'amountpaie' => 0,
            'type' => 'fixe',
            'nbre_mois' => $nbre_mois,
            'start_date' => $demande->start_date,
            'end_date' => $demande->end_date,
            'reason' => $demande->demande_reason,
            'statut' => 'running',
            'month_paie' => now()->format('Y-m'),
            'is_active' => true,
            'prochaine_echeance' => $demande->start_date,
            'company_id' => $demande->company_id
        ]);            
    }
    
    /**
     * Transférer une demande d'absence vers la table time_sheets
     */
    private function transferToTimeSheet(Demande $demande)
    {
        // $demande->start_date et $demande->end_date sont des instances de Carbon
        $startDate = \Carbon\Carbon::parse($demande->start_date);
        $endDate = \Carbon\Carbon::parse($demande->end_date);

        // Calculer la différence
        $diff = $startDate->diff($endDate);

        // Récupérer le nombre de jours et d'heures
        $days = $diff->d;
        
        TimeSheet::create([
            'employee_id' => $demande->employee_id,
            'periode_id' => $demande->periode_id,
            'date' => $demande->start_date,
            'arrival_date' => $demande->end_date,
            'hours' => round($days*8),
            'motif_justify' => 'Oui',//
            'type_permis' => NULL,//
            'remark' => $demande->demande_reason,
            'retenue' => $days,
            'deduc_abs' => 0,
            'statut' => 'approved',
            'document' => $demande->file_path,
            'monthpaie' => now()->format('Y-m'),
            'company_id' => $demande->company_id
        ]);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $isLeave = str_starts_with($id, 'L_');
        $realId = $isLeave ? substr($id, 2) : $id;
        
        if ($isLeave) {
            $leave = Leave::with(['employee', 'company'])->findOrFail($realId);
            $demande = new Demande();
            $demande->incrementing = false;
            $demande->keyType = 'string';
            $demande->id = 'L_' . $leave->id;
            $demande->employee_id = $leave->employee_id;
            $demande->employee = $leave->employee;
            $demande->company_id = $leave->company_id;
            $demande->company = $leave->company;
            $demande->categorie_demandes = 'absence';
            $demande->demande_types = $leave->leave_type_id;
            $demande->start_date = $leave->start_date;
            $demande->end_date = $leave->end_date;
            $demande->demande_reason = $leave->leave_reason;
            $demande->status = $leave->status;
            if ($demande->status === 'Approuvé') $demande->status = 'Approved';
            if ($demande->status === 'Rejeté') $demande->status = 'Rejected';
            $demande->is_leave = true;
            $demande->periode_id = $leave->periode_id;
        } else {
            $demande = Demande::with(['employee', 'company'])->findOrFail($realId);
        }
        
        // Récupérer tous les exercices et périodes
        $exercices = PaieExercice::where('company_id', $demande->company_id)->get();
        $periodes = PaiePeriode::where('company_id', $demande->company_id)
                    ->get();
        
        return view('employees::demandes.show', compact('demande', 'periodes', 'exercices'));
    }
}
