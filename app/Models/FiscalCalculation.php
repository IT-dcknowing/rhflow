<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FiscalCalculation extends Model
{
    protected $fillable = [
        'company_id',
        'employee_id',
        'calculation_type',
        'period',
        'gross_salary',
        'taxable_income',
        'tax_amount',
        'social_contributions',
        'net_salary',
        'tax_country',
        'tax_year',
        'calculation_details',
        'is_processed',
    ];

    protected $casts = [
        'gross_salary' => 'decimal:2',
        'taxable_income' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'social_contributions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'calculation_details' => 'array',
        'is_processed' => 'boolean',
    ];

    /**
     * Relation avec l'entreprise
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation avec l'employé
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Calculer les impôts pour l'Afrique de l'Ouest
     */
    public static function calculateWestAfricanTaxes(float $grossSalary, string $country = 'SN'): array
    {
        $taxRates = self::getWestAfricanTaxRates($country);
        $socialRates = self::getWestAfricanSocialRates($country);

        $taxableIncome = $grossSalary;
        $taxAmount = 0;
        $socialContributions = 0;

        // Calcul de l'impôt sur le revenu
        foreach ($taxRates as $bracket) {
            if ($taxableIncome > $bracket['min']) {
                $taxableInBracket = min($taxableIncome, $bracket['max'] ?? $taxableIncome) - $bracket['min'];
                $taxAmount += $taxableInBracket * ($bracket['rate'] / 100);
            }
        }

        // Calcul des cotisations sociales
        foreach ($socialRates as $contribution) {
            $socialContributions += $grossSalary * ($contribution['rate'] / 100);
        }

        $netSalary = $grossSalary - $taxAmount - $socialContributions;

        return [
            'gross_salary' => $grossSalary,
            'taxable_income' => $taxableIncome,
            'tax_amount' => $taxAmount,
            'social_contributions' => $socialContributions,
            'net_salary' => $netSalary,
            'calculation_details' => [
                'tax_brackets' => $taxRates,
                'social_contributions' => $socialRates,
            ],
        ];
    }

    /**
     * Barèmes d'imposition pour l'Afrique de l'Ouest
     */
    private static function getWestAfricanTaxRates(string $country): array
    {
        return match ($country) {
            'SN' => [ // Sénégal
                ['min' => 0, 'max' => 630000, 'rate' => 0],
                ['min' => 630000, 'max' => 1500000, 'rate' => 20],
                ['min' => 1500000, 'max' => 4000000, 'rate' => 30],
                ['min' => 4000000, 'max' => 8000000, 'rate' => 35],
                ['min' => 8000000, 'rate' => 40],
            ],
            'BF' => [ // Burkina Faso
                ['min' => 0, 'max' => 30000, 'rate' => 0],
                ['min' => 30000, 'max' => 50000, 'rate' => 12.1],
                ['min' => 50000, 'max' => 80000, 'rate' => 19.2],
                ['min' => 80000, 'max' => 120000, 'rate' => 24.1],
                ['min' => 120000, 'max' => 200000, 'rate' => 30.9],
                ['min' => 200000, 'rate' => 35.9],
            ],
            'CI' => [ // Côte d'Ivoire
                ['min' => 0, 'max' => 600000, 'rate' => 0],
                ['min' => 600000, 'max' => 1000000, 'rate' => 10],
                ['min' => 1000000, 'max' => 2000000, 'rate' => 15],
                ['min' => 2000000, 'max' => 3000000, 'rate' => 25],
                ['min' => 3000000, 'rate' => 30],
            ],
            default => [ // Défaut
                ['min' => 0, 'max' => 1000000, 'rate' => 15],
                ['min' => 1000000, 'rate' => 25],
            ],
        };
    }

    /**
     * Taux de cotisations sociales pour l'Afrique de l'Ouest
     */
    private static function getWestAfricanSocialRates(string $country): array
    {
        return match ($country) {
            'SN' => [ // Sénégal
                ['name' => 'Sécurité sociale', 'rate' => 5.6],
                ['name' => 'Retraite complémentaire', 'rate' => 3.0],
                ['name' => 'Mutuelle', 'rate' => 2.0],
            ],
            'BF' => [ // Burkina Faso
                ['name' => 'CNSS', 'rate' => 5.5],
                ['name' => 'Accidents du travail', 'rate' => 3.0],
                ['name' => 'Retraite', 'rate' => 5.0],
            ],
            'CI' => [ // Côte d'Ivoire
                ['name' => 'CNPS', 'rate' => 6.3],
                ['name' => 'Accidents du travail', 'rate' => 2.5],
                ['name' => 'Mutuelle', 'rate' => 1.5],
            ],
            default => [ // Défaut
                ['name' => 'Sécurité sociale', 'rate' => 5.0],
                ['name' => 'Retraite', 'rate' => 3.0],
            ],
        };
    }

    /**
     * Générer les déclarations fiscales
     */
    public function generateFiscalDeclaration(): array
    {
        return [
            'company_name' => $this->company->name,
            'employee_name' => $this->employee->name,
            'period' => $this->period,
            'gross_salary' => $this->gross_salary,
            'tax_amount' => $this->tax_amount,
            'social_contributions' => $this->social_contributions,
            'net_salary' => $this->net_salary,
            'tax_country' => $this->tax_country,
            'generated_at' => now(),
        ];
    }

    /**
     * Scope pour les calculs d'une entreprise
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope pour les calculs d'un employé
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope pour une période spécifique
     */
    public function scopeForPeriod($query, $period)
    {
        return $query->where('period', $period);
    }
}
