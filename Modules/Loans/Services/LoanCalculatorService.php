<?php

namespace Modules\Loans\Services;

use Carbon\Carbon;
use Modules\Loans\Models\Loan;

class LoanCalculatorService
{
    /** 
     * Calculer le montant mensuel à rembourser
     *
     * @param float $loanAmount Montant total du prêt
     * @param int $months Nombre de mois pour le remboursement
     * @param float $interestRate Taux d'intérêt annuel (en pourcentage)
     * @return float
     */
    public function calculateMonthlyPayment(float $loanAmount, int $months, float $interestRate = 0): float
    {
        // Si pas d'intérêt, simple division
        if ($interestRate == 0) {
            return round($loanAmount / $months, 2);
        }

        // Conversion du taux annuel en taux mensuel
        $monthlyRate = $interestRate / 100 / 12;
        
        // Formule de calcul d'un prêt avec intérêts
        $monthlyPayment = $loanAmount * $monthlyRate * pow(1 + $monthlyRate, $months) / (pow(1 + $monthlyRate, $months) - 1);
        
        return round($monthlyPayment, 2);
    }

    /**
     * Calculer la date de fin du prêt
     *
     * @param string $startDate Date de début (format Y-m-d)
     * @param int $months Nombre de mois pour le remboursement
     * @return string Date de fin (format Y-m-d)
     */
    public function calculateEndDate(string $startDate, int $months): string
    {
        return Carbon::parse($startDate)->addMonths($months)->format('Y-m-d');
    }

    /**
     * Générer un échéancier de remboursement
     *
     * @param Loan $loan
     * @return array
     */
    public function generateRepaymentSchedule(Loan $loan): array
    {
        $schedule = [];
        $startDate = Carbon::parse($loan->start_date);
        $amount = $loan->amount;
        $months = $loan->nbre_mois;
        $interestRate = $loan->loan_option->interest_rate ?? 0;
        $monthlyPayment = $this->calculateMonthlyPayment($amount, $months, $interestRate);
        
        // Montant restant initial
        $remainingAmount = $amount;
        
        // Récupérer les paiements déjà effectués
        $existingPayments = $loan->payments->keyBy(function ($payment) {
            return Carbon::parse($payment->payment_date)->format('Y-m');
        });

        for ($i = 0; $i < $months; $i++) {
            $paymentDate = $startDate->copy()->addMonths($i);
            $paymentMonth = $paymentDate->format('Y-m');
            
            // Calculer les intérêts pour ce mois
            $monthlyInterest = $remainingAmount * ($interestRate / 100 / 12);
            $principal = $monthlyPayment - $monthlyInterest;
            
            // Vérifier si un paiement existe déjà pour ce mois
            $actualPayment = isset($existingPayments[$paymentMonth]) ? $existingPayments[$paymentMonth]->amount : 0;
            $status = isset($existingPayments[$paymentMonth]) ? 'paid' : 
                      ($paymentDate->isPast() ? 'overdue' : 'pending');
            
            $schedule[] = [
                'payment_date' => $paymentDate->format('Y-m-d'),
                'month_number' => $i + 1,
                'expected_amount' => $monthlyPayment,
                'principal' => $principal,
                'interest' => $monthlyInterest,
                'actual_payment' => $actualPayment,
                'remaining_amount' => $remainingAmount - $principal,
                'status' => $status
            ];
            
            // Mettre à jour le montant restant pour le prochain mois
            $remainingAmount -= $principal;
        }
        
        return $schedule;
    }

    /**
     * Calculer le montant total remboursé
     *
     * @param Loan $loan
     * @return float
     */
    public function calculateTotalPaid(Loan $loan): float
    {
        return $loan->payments->sum('amount');
    }

    /**
     * Calculer le montant restant à payer
     *
     * @param Loan $loan
     * @return float
     */
    public function calculateRemainingAmount(Loan $loan): float
    {
        $totalPaid = $this->calculateTotalPaid($loan);
        return $loan->amount - $totalPaid;
    }

    /**
     * Calculer le pourcentage de remboursement
     *
     * @param Loan $loan
     * @return float
     */
    public function calculateRepaymentPercentage(Loan $loan): float
    {
        $totalPaid = $this->calculateTotalPaid($loan);
        return ($totalPaid / $loan->amount) * 100;
    }

    /**
     * Calcule le prochain paiement pour un prêt
     *
     * @param \Modules\Loans\Models\Loan $loan
     * @return array
     */
    public function getNextPayment(Loan $loan)
    {
        try {
            // Vérifier si le prêt est en cours
            if ($loan->statut !== 'running') {
                return [
                    'amount' => 0,
                    'due_date' => null,
                    'is_last' => false,
                    'message' => 'Le prêt n\'est pas en cours de remboursement'
                ];
            }

            // Si la date de prochaine échéance est définie, l'utiliser
            if ($loan->prochaine_echeance) {
                return [
                    'amount' => $loan->montant_echeance,
                    'due_date' => $loan->prochaine_echeance,
                    'is_last' => $this->isLastPayment($loan),
                    'message' => 'Prochaine échéance'
                ];
            }

            // Sinon, calculer la prochaine échéance
            $startDate = \Carbon\Carbon::parse($loan->date_debut);
            $now = now();
            
            // Calculer le nombre de mois écoulés depuis le début
            $monthsPassed = $startDate->diffInMonths($now);
            $nextPaymentNumber = $monthsPassed + 1;
            
            // Vérifier si on dépasse le nombre de mois du prêt
            if ($nextPaymentNumber > $loan->nbre_mois) {
                return [
                    'amount' => 0,
                    'due_date' => null,
                    'is_last' => true,
                    'message' => 'Toutes les échéances ont été payées'
                ];
            }

            // Calculer la date de la prochaine échéance
            $nextPaymentDate = (clone $startDate)->addMonths($nextPaymentNumber);
            
            return [
                'amount' => $loan->montant_echeance,
                'due_date' => $nextPaymentDate->format('Y-m-d'),
                'is_last' => $nextPaymentNumber == $loan->nbre_mois,
                'message' => 'Prochaine échéance calculée'
            ];

        } catch (\Exception $e) {
            \Log::error('Erreur lors du calcul du prochain paiement', [
                'loan_id' => $loan->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'amount' => 0,
                'due_date' => null,
                'is_last' => false,
                'message' => 'Erreur de calcul'
            ];
        }
    }

    /**
     * Vérifie si le prochain paiement est le dernier
     *
     * @param \Modules\Loans\Models\Loan $loan
     * @return bool
     */
    private function isLastPayment(Loan $loan): bool
    {
        $totalPaid = $this->calculateTotalPaid($loan);
        $remainingAmount = $this->calculateRemainingAmount($loan);
        
        return $remainingAmount <= ($loan->montant_echeance * 1.1); // Marge de 10% pour les arrondis
    }
}