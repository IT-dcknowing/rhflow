<?php

namespace Modules\Loans\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Loans\Models\Loan;
use Modules\Loans\Models\LoanPayment;
use Modules\PaieSalaries\Models\Retenue;
use Illuminate\Support\Facades\Auth;

class LoanPaymentController extends Controller
{
    /**
     * Afficher le formulaire de création d'un nouveau remboursement
     * @return Renderable
     */
    public function create($loanId)
    {
        $loan = Loan::where('company_id', Auth::user()->company_id)
                ->findOrFail($loanId);
        
        return view('loans::payments.create', compact('loan'));
    }

    /**
     * Enregistrer un nouveau remboursement
     * @param Request $request
     * @return Redirect
     */
    public function store(Request $request, $loanId)
    {
        $loan = Loan::where('company_id', Auth::user()->company_id)
                    ->findOrFail($loanId);

        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:' . ($loan->amount - $loan->payments()->sum('amount')),
            ],
            'payment_date' => 'required|date|before_or_equal:today',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            $payment = new LoanPayment([
                'loan_id' => $loan->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'note' => $request->note,
                'company_id' => Auth::user()->company_id,
            ]);

            $payment->save();

            $lastOrder = \Modules\Employees\Models\Employee::where("employees.is_active", 1)
            ->where("employees.company_id", Auth::user()->company_id)
            ->join("retenues", "employees.id", "=", "retenues.employee_id")
            ->orderBy("retenues.ordre", "desc")
            ->first();

            $newRetenue = new Retenue();
            $newRetenue->fill([
                'libelle' => $loan->title, 
                'type_retenue_id' => 30,
                'employee_id' => $loan->employee_id,
                'periode_id' => $loan->periode_id,
                'code' => 500,
                'ordre' => $lastOrder ? $lastOrder->ordre + 1 : 1,
                'patronale' => 0,
                'salariale' => 1,
                'base' => $loan->amount,
                'taux' => $loan->nbre_mois, 
                'amount' => $request->amount,
                'date_application' => now(),
                'is_active' => true,
                'type' => 'add',
                'month_paie' => now()->format('Y-m'),
                'company_id' => Auth::user()->company_id,
            ]);

            if ($newRetenue->save()) {
                \Illuminate\Support\Facades\Log::info('Retenue ajoutée avec succès', [
                    'employee_id' => $loan->employee_id,
                    'loan_id' => $loan->id
                ]);
            }

            // Mettre à jour le statut du prêt si nécessaire
            $totalPaid = $loan->payments()->sum('amount');
            if ($totalPaid >= $loan->amount) {
                $loan->statut = 'completed';
                $loan->save();
            } elseif ($loan->statut === 'pending') {
                $loan->statut = 'running';
                $loan->save();
            }

            return redirect()
                ->route('company.loans.show', $loan->id)
                ->with('success', 'Le remboursement a été enregistré avec succès.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement du remboursement.');
        }
    }

    /**
     * Afficher le formulaire de modification d'un remboursement
     * @param int $id
     * @return Renderable
     */
    public function edit($loanId, $id)
    {
        $payment = LoanPayment::where('company_id', Auth::user()->company_id)
                            ->findOrFail($id);
        $loan = $payment->loan;
        
        return view('loans::payments.edit', compact('payment', 'loan'));
    }

    /**
     * Mettre à jour un remboursement
     * @param Request $request
     * @param int $id
     * @return Redirect
     */
    public function update(Request $request, $loanId, $id)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $payment = LoanPayment::where('company_id', Auth::user()->company_id)
                            ->findOrFail($id);
        $loan = $payment->loan;

        // Calculer le montant restant à payer (en excluant ce paiement)
        $totalPaid = $loan->payments->where('id', '!=', $id)->sum('amount') ?? 0;
        $remainingAmount = $loan->amount - $totalPaid;

        // Vérifier si le montant du paiement ne dépasse pas le montant restant
        if ($request->amount > $remainingAmount) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => 'Le montant du paiement ne peut pas dépasser le montant restant à payer (' . number_format($remainingAmount, 0, ',', ' ') . ' FCFA).']);
        }

        // Mettre à jour le paiement
        $payment->update([
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        // Mettre à jour le statut du prêt si nécessaire
        $this->updateLoanStatus($loan);

        return redirect()->route('company.loans.show', $loan->id)
            ->with('success', 'Remboursement mis à jour avec succès.');
    }

    /**
     * Supprimer un remboursement
     * @param int $id
     * @return Redirect
     */
    public function destroy($loanId, $paymentId)
    {
        $payment = LoanPayment::where('company_id', Auth::user()->company_id)
                            ->findOrFail($paymentId);
        $loan = $payment->loan;
        
        $payment->delete();

        // Mettre à jour le statut du prêt si nécessaire
        $this->updateLoanStatus($loan);

        return redirect()->route('company.loans.show', $loan->id)
            ->with('success', 'Remboursement supprimé avec succès.');
    }

    /**
     * Mettre à jour le statut du prêt en fonction des paiements
     * @param Loan $loan
     */
    private function updateLoanStatus(Loan $loan)
    {
        // Recalculer le montant total payé
        $loan->refresh();
        $totalPaid = $loan->payments->sum('amount') ?? 0;
        
        // Si le montant total est payé, marquer le prêt comme terminé
        if ($totalPaid >= $loan->amount) {
            $loan->update(['statut' => 'completed']);
        } 
        // Si des paiements existent mais le prêt n'est pas entièrement remboursé
        elseif ($totalPaid > 0) {
            $loan->update(['statut' => 'running']);
        }
    }
}