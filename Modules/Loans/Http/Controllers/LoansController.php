<?php

namespace Modules\Loans\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Designation;
use App\Models\Allowance;
use App\Models\AllowanceOption;
use App\Models\JobCategorie;
use App\Models\PaieExercice;
use App\Models\PaiePeriode;
use Carbon\Carbon; 
use Modules\Loans\Models\Loan;
use Modules\Settings\Models\LoanType;
use Modules\Loans\Services\LoanCalculatorService;
use Modules\Employees\Models\Employee;
use Modules\PaieSalaries\Models\PaySlip;
use Modules\PaieSalaries\Models\Retenue;
use Modules\PaieSalaries\Models\TypeRetenue;
use Modules\PaieSalaries\Models\SetSalarie;

class LoansController extends Controller
{
    /**
     * Display a listing of the resource.
     */ 
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;
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

        if ($periode) {
            $loans = Loan::with(['employee', 'loanOption', 'branch', 'payments'])
                        ->where('company_id', $company_id)
                        ->where(function($query) use ($periode) {
                            $query->whereHas('payments', function ($q) use ($periode) {
                                $q->where('periode_id', $periode->id);
                            })->orWhere('periode_id', $periode->id);
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();
        } else {
            $loans = collect();
        }

        $periodes = PaiePeriode::with("exercice")
            ->where("company_id", $company_id)
            ->orderBy("date_debut", "desc")
            ->get();

        $exercices = PaieExercice::with("periodes")
            ->where("company_id", $company_id)
            ->orderBy("date_debut", "desc")
            ->get();

        return view('loans::index', compact('loans', 'periode', 'periodes', 'exercices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employees = Employee::where('company_id', Auth::user()->company_id)
                            ->where('is_active', true)
                            ->get();
        
        $branches = Branch::where('company_id', Auth::user()->company_id)->get();
        
        $companyId = Auth::user()->company_id;
        $loanOptions = LoanType::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        \Log::info('Loan Options Loaded:', [
            'count' => $loanOptions->count(),
            'company_id' => $companyId,
            'options' => $loanOptions->toArray()
        ]);
        
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
        
        return view('loans::create', compact('employees', 'branches', 'loanOptions', 'periode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'loan_option' => 'required|exists:loan_types,id',
                'branch_id' => 'nullable|exists:branches,id',
                'periode_id' => 'required|exists:paie_periodes,id',
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'amount_deduc' => 'required|numeric|min:0',
                'nbre_mois' => 'required|integer|min:1',
                'type' => 'required|in:fixe,pourcentage',
                'pourcentage_montant' => 'nullable|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'reason' => 'nullable|string',
                'statut' => 'required|in:pending,running,completed,cancelled',
            ]);

            // Calculer la date de fin si non fournie
            if (!$request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date);
                $endDate = $startDate->copy()->addMonths($request->nbre_mois);
                $request->merge(['end_date' => $endDate->format('Y-m-d')]);
            }

            // Montant à déduire et à payer
            $amount_deduc = 0;
            $amountpaie = 0;
            if($request->type == 'fixe'){
                $amount_deduc = $request->amount_deduc;
                $amountpaie = null;
            }else{
                $amountpaie = $request->amount_deduc;
                $amount_deduc = $request->pourcentage_montant;
            }

            // Créer le prêt
            $loan = new Loan([
                'employee_id' => $request->employee_id,
                'loan_option' => $request->loan_option,
                'branche_id' => $request->branch_id,
                'periode_id' => $request->periode_id,
                'title' => $request->title,
                'type' => $request->type,
                'amount' => $request->amount,
                'amount_deduc' => $amount_deduc,
                'amountpaie' => $amountpaie,
                'nbre_mois' => $request->nbre_mois,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'statut' => $request->statut,
                'month_paie' => now()->format('Y-m'),
                'is_active' => true,
                'company_id' => Auth::user()->company_id,
            ]);
            
            $loan->save();

            return redirect()->route('company.loans.show', $loan->id)
                            ->with('success', 'Prêt créé avec succès.');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $loan = Loan::with(['employee', 'loanOption', 'branch', 'payments'])
                    ->where('company_id', Auth::user()->company_id)
                    ->findOrFail($id);
        
        $calculator = new LoanCalculatorService();
        
        $totalPaid = $calculator->calculateTotalPaid($loan);
        $remainingAmount = $calculator->calculateRemainingAmount($loan);
        $repaymentPercentage = $calculator->calculateRepaymentPercentage($loan);
        $repaymentSchedule = $calculator->generateRepaymentSchedule($loan);
        
        return view('loans::show', compact('loan', 'totalPaid', 'remainingAmount', 'repaymentPercentage', 'repaymentSchedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $loan = Loan::where('company_id', Auth::user()->company_id)
                    ->findOrFail($id);
        
        $employees = Employee::active()
                            ->where('company_id', Auth::user()->company_id)
                            ->get();
        
        $branches = Branch::where('company_id', Auth::user()->company_id)->get();
        
        $loanOptions = LoanType::where('company_id', Auth::user()->company_id)
                                ->where('is_active', true)
                                ->get();
       
        return view('loans::edit', compact('loan', 'employees', 'branches', 'loanOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'loan_option' => 'required|exists:loan_types,id',
            'branch_id' => 'nullable|exists:branches,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'amount_deduc' => 'required|numeric|min:0',
            'nbre_mois' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'statut' => 'required|in:pending,running,completed,cancelled',
        ]);

        $loan = Loan::where('company_id', Auth::user()->company_id)
                    ->findOrFail($id);

        // Calculer la date de fin si non fournie
        if (!$request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = $startDate->copy()->addMonths($request->nbre_mois);
            $request->merge(['end_date' => $endDate->format('Y-m-d')]);
        }

        // Montant à déduire et à payer
        $amount_deduc = 0;
        $amountpaie = 0;
        if($request->type == 'fixe'){
            $amount_deduc = $request->amount_deduc;
            $amountpaie = null;
        }else{
            $amountpaie = $request->amount_deduc;
            $amount_deduc = $request->pourcentage_montant;
        }

        // Mettre à jour le prêt
        $loan->update([
            'employee_id' => $request->employee_id,
            'loan_option' => $request->loan_option,
            'branche_id' => $request->branche_id,
            'title' => $request->title,
            'amount' => $request->amount,
            'nbre_mois' => $request->nbre_mois,
            'amount_deduc' => $amount_deduc,
            'amountpaie' => $amountpaie,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'statut' => $request->statut,
        ]);

        return redirect()->route('company.loans.show', $loan->id)
                        ->with('success', 'Prêt mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $loan = Loan::where('company_id', Auth::user()->company_id)
                    ->findOrFail($id);
        
        // Vérifier si le prêt a des paiements
        if ($loan->payments && $loan->payments->count() > 0) {
            return redirect()->back()
                            ->with('error', 'Impossible de supprimer ce prêt car il possède des remboursements associés.');
        }
        
        $loan->delete();

        return redirect()->back()
                        ->with('success', 'Prêt supprimé avec succès.');
    }

    /**
     * Désactive un prêt : il sort de la paie des périodes encore modifiables.
     *
     * Les périodes verrouillées (validée / payée / clôturée / annulée, ou bulletin déjà
     * généré) sont laissées intactes : une paie émise ne se réécrit pas. Elles sont
     * listées dans le message de retour pour que l'utilisateur sache ce qui subsiste.
     */
    public function deactivate($id)
    {
        $user = Auth::user();
        $loan = Loan::where('company_id', $user->company_id)->findOrFail($id);

        if (!$loan->is_active) {
            return redirect()->back()->with('error', 'Ce prêt est déjà désactivé.');
        }

        $retenues = Retenue::where('loan_id', $loan->id)
            ->where('company_id', $user->company_id)
            ->get();

        $sauvegarde = [];
        $periodesVerrouillees = [];

        \DB::beginTransaction();
        try {
            foreach ($retenues as $retenue) {
                $periode = PaiePeriode::where('company_id', $user->company_id)
                    ->find($retenue->periode_id);

                // Une période introuvable (supprimée) ne bloque rien : la ligne est orpheline.
                if ($periode && $error = $this->paieLockError($periode, $retenue->employee_id)) {
                    $periodesVerrouillees[] = $periode->nom ?? ('#' . $periode->id);
                    continue;
                }

                // On mémorise la ligne telle quelle : son montant a pu être ajusté sur
                // cette période, le prêt seul ne permettrait pas de la reconstruire.
                $attributs = $retenue->getAttributes();
                unset($attributs['id'], $attributs['created_at'], $attributs['updated_at']);
                $sauvegarde[] = $attributs;

                $retenue->delete();
            }

            $loan->is_active = 0;
            $loan->retenues_backup = $sauvegarde;
            $loan->save();

            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Désactivation prêt échouée', ['loan_id' => $loan->id, 'error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'La désactivation du prêt a échoué : ' . $e->getMessage());
        }

        $message = 'Prêt désactivé. ' . count($sauvegarde) . ' retenue(s) retirée(s) de la paie.';
        if ($periodesVerrouillees) {
            $message .= ' Périodes non modifiées car la paie y est verrouillée : '
                . implode(', ', $periodesVerrouillees) . '.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Réactive un prêt en restaurant les retenues retirées lors de sa désactivation.
     *
     * On rejoue la sauvegarde plutôt que de recalculer depuis le prêt : les dates des
     * périodes ne recoupent pas toujours celles du prêt dans les données existantes, et
     * le montant de la retenue a pu être ajusté période par période.
     */
    public function activate($id)
    {
        $user = Auth::user();
        $loan = Loan::where('company_id', $user->company_id)->findOrFail($id);

        if ($loan->is_active) {
            return redirect()->back()->with('error', 'Ce prêt est déjà actif.');
        }

        $sauvegarde = $loan->retenues_backup ?: [];
        $restaurees = 0;
        $ignorees = [];

        \DB::beginTransaction();
        try {
            foreach ($sauvegarde as $attributs) {
                $periode = PaiePeriode::where('company_id', $user->company_id)
                    ->find($attributs['periode_id'] ?? null);

                // La période a pu être clôturée ou supprimée depuis la désactivation.
                if (!$periode) {
                    $ignorees[] = 'période #' . ($attributs['periode_id'] ?? '?') . ' introuvable';
                    continue;
                }

                if ($this->paieLockError($periode, $attributs['employee_id'] ?? $loan->employee_id)) {
                    $ignorees[] = $periode->nom ?? ('#' . $periode->id);
                    continue;
                }

                $dejaPresente = Retenue::where('loan_id', $loan->id)
                    ->where('periode_id', $periode->id)
                    ->exists();

                if ($dejaPresente) {
                    continue;
                }

                $attributs['loan_id'] = $loan->id;
                Retenue::create($attributs);
                $restaurees++;
            }

            $loan->is_active = 1;
            $loan->retenues_backup = null;
            $loan->save();

            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Réactivation prêt échouée', ['loan_id' => $loan->id, 'error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'La réactivation du prêt a échoué : ' . $e->getMessage());
        }

        $message = 'Prêt réactivé. ' . $restaurees . ' retenue(s) restaurée(s) dans la paie.';
        if ($ignorees) {
            $message .= ' Non restaurées : ' . implode(', ', $ignorees) . '.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Renvoie un message si la paie de cette période ne peut plus être modifiée,
     * null sinon. Même règle que la désactivation d'un congé.
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

    /**
     * Afficher les prêts actifs pour la paie
     */
    public function activeLoans()
    {
        $loans = Loan::with(['employee', 'loanOption'])
                    ->where('company_id', Auth::user()->company_id)
                    ->where('statut', 'running')
                    ->whereDate('end_date', '>=', now())
                    ->get();
        
        return view('loans::active', compact('loans'));
    }

    /**
     * Générer un rapport des prêts
     */
    public function report(Request $request)
    {
        $status = $request->input('statut');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $employeeId = $request->input('employee_id');
        
        $query = Loan::with(['employee', 'loanOption', 'branche', 'payments'])
                    ->where('company_id', Auth::user()->company_id);
        
        // Filtrer par statut
        if ($status) {
            $query->where('statut', $status);
        }
        
        // Filtrer par date de début
        if ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        }
        
        // Filtrer par date de fin
        if ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }
        
        // Filtrer par employé
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        
        $loans = $query->orderBy('created_at', 'desc')->get();
        
        // Calculer les statistiques
        $totalAmount = $loans->sum('amount');
        $totalPaid = $loans->sum(function($loan) {
            return $loan->payments->sum('amount');
        });
        $totalRemaining = $totalAmount - $totalPaid;
        
        $employees = Employee::where('company_id', Auth::user()->company_id)
                            ->where('statut', 1)
                            ->get();
        
        return view('loans::report', compact('loans', 'employees', 'statut', 'startDate', 'endDate', 'employeeId', 'totalAmount', 'totalPaid', 'totalRemaining'));
    }
}
