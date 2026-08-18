<?php

namespace Modules\Simulator\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaieExercice;
use App\Models\PaiePeriode;
use Modules\Employees\Models\Employee;
use App\Models\MaritalStatus;
use App\Models\AllowanceOption;
use App\Models\Allowance;

class SimulatorController extends Controller
{
    /**
     * Configuration des paramètres fiscaux et sociaux
     */
    private const TAX_BRACKETS = [
        ['min' => 0, 'max' => 75000, 'rate' => 0],
        ['min' => 75000, 'max' => 240000, 'rate' => 16],
        ['min' => 240000, 'max' => 800000, 'rate' => 21],
        ['min' => 800000, 'max' => 2400000, 'rate' => 24],
        ['min' => 2400000, 'max' => 8000000, 'rate' => 28],
        ['min' => 8000000, 'max' => null, 'rate' => 32]
    ];

    private const FAMILY_ALLOWANCES = [
        1 => 0,
        1.5 => 5500,
        2 => 11000,
        2.5 => 16500,
        3 => 22000,
        3.5 => 27500,
        4 => 33000,
        4.5 => 38500,
        5 => 44000
    ];

    private const SOCIAL_RATES = [
        'cnps' => 6.3,
        'cmu_base' => 500,
        'cmu_threshold' => 7,
        'cmu_additional' => 1000,
        'cmu_max' => 3000
    ];

    private const TRANSPORT_PRIME_LIMITS = [
        22000, 24000, 30000
    ];

    /**
     * Dashboard du simulateur
     */
    public function dashboard()
    {        
        // Récupérer les employés de l'entreprise
        $employees = Employee::where('company_id', auth()->user()->company_id ?? null)
                    ->active()
                    ->orderBy('name')
                    ->get();
        
        $periode = PaiePeriode::where('company_id', auth()->user()->company_id ?? null)->latest()->first();

        // Récupérer les situations matrimoniales
        $situations = MaritalStatus::all();

        // Récupérer les options d'allocations
        $allowanceOptions = AllowanceOption::where('company_id', auth()->user()->company_id ?? null)
            ->active()
            ->orderBy('name')
            ->get();

        return view('simulator::dashboard', [
            'employees' => $employees,
            'periode' => $periode,
            'situations' => $situations,
            'allowanceOptions' => $allowanceOptions,
            'taxConfig' => [
                'brackets' => self::TAX_BRACKETS,
                'familyAllowances' => self::FAMILY_ALLOWANCES,
                'socialRates' => self::SOCIAL_RATES,
                'transportLimits' => self::TRANSPORT_PRIME_LIMITS,
                'minSalary' => 75000,
                'maxIterations' => 100000,
                'cnpsCeiling' => 3375000
            ]
        ]);
    }

    /**
     * Calcul de simulation via API (optionnel pour future amélioration)
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'net_salary' => 'required|numeric|min:75000',
            'marital_status' => 'required|integer',
            'children' => 'required|integer|min:0|max:10',
            'disabled_dependents' => 'required|integer|min:0|max:5',
            'cmu_beneficiaries' => 'required|integer|min:0|max:10',
            'transport_prime' => 'required|numeric|min:0',
            'tax_exempt_100' => 'required|numeric|min:0',
            'tax_exempt_10' => 'required|numeric|min:0',
            'social_exempt' => 'required|numeric|min:0'
        ]);

        // Logique de calcul ici (sera implémentée plus tard)
        return response()->json(['status' => 'success']);
    }
}
