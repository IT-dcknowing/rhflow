<?php

namespace Database\Seeders;

use App\Models\Allowance;
use App\Models\AllowanceOption;
use Modules\Employees\Models\Employee;
use App\Models\Company;
use Illuminate\Database\Seeder;

class AllowanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les entreprises et employés existants
        $companies = Company::all();
        $employees = Employee::all();

        if ($companies->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('Aucune entreprise ou employé trouvé. Création des allocations annulée.');
            return;
        }

        $allowancesCreated = 0;

        foreach ($companies as $company) {
            // Récupérer les options d'allocation pour cette entreprise
            $allowanceOptions = AllowanceOption::byCompany($company->id)->active()->get();

            if ($allowanceOptions->isEmpty()) {
                $this->command->warn("Aucune option d'allocation trouvée pour {$company->name}. Passer.");
                continue;
            }

            // Récupérer les employés de cette entreprise
            $companyEmployees = $employees->where('company_id', $company->id);

            if ($companyEmployees->isEmpty()) {
                $this->command->warn("Aucun employé trouvé pour {$company->name}. Passer.");
                continue;
            }

            // Créer des allocations pour quelques employés
            foreach ($companyEmployees->take(5) as $employee) {
                // Sélectionner aléatoirement 2-4 options d'allocation
                $selectedOptions = $allowanceOptions->random(min(rand(2, 4), $allowanceOptions->count()));

                foreach ($selectedOptions as $option) {
                    // Générer des montants réalistes selon le type d'allocation
                    $amount = $this->generateAmount($option->name);

                    // Vérifier que l'allocation n'existe pas déjà pour cet employé
                    $exists = Allowance::where('employee_id', $employee->id)
                        ->where('allowance_option_id', $option->id)
                        ->exists();

                    if (!$exists) {
                        Allowance::create([
                            'code' => $this->generateCode($option->name, $employee->id),
                            'code_compta' => rand(600000, 699999), // Code comptable pour les charges
                            'employee_id' => $employee->id,
                            'allowance_option_id' => $option->id,
                            'title' => $option->name,
                            'trait_fisc' => $option->param_fiscal,
                            'trait_cnps' => $option->param_social,
                            'base_heures' => false,
                            'amount' => $amount,
                            'amount_imp' => $this->calculateImposableAmount($amount, $option->param_fiscal),
                            'montant' => $amount,
                            'jours_work' => 30, // Jours travaillés dans le mois
                            'jours_leave' => 0,
                            'type' => 'fixed',
                            'type_amount' => false,
                            'details' => 'Allocation générée automatiquement',
                            'company_id' => $company->id,
                        ]);

                        $allowancesCreated++;
                    }
                }
            }

            $this->command->info("{$allowancesCreated} allocations créées pour {$company->name}");
        }

        $this->command->info("Total: {$allowancesCreated} allocations créées");
    }

    /**
     * Génère un code unique pour l'allocation
     */
    private function generateCode(string $optionName, int $employeeId): string
    {
        $prefix = 'ALL';
        $timestamp = now()->format('Ymd');
        $suffix = substr(md5($optionName . $employeeId), 0, 4);
        return strtoupper($prefix . '-' . $timestamp . '-' . $suffix);
    }

    /**
     * Génère un montant réaliste selon le type d'allocation
     */
    private function generateAmount(string $optionName): float
    {
        $normalizedName = strtolower($optionName);

        if (str_contains($normalizedName, 'transport')) {
            return (float) rand(20000, 30000); // 20k-30k FCFA
        } elseif (str_contains($normalizedName, 'logement')) {
            return (float) rand(50000, 100000); // 50k-100k FCFA
        } elseif (str_contains($normalizedName, 'panier') || str_contains($normalizedName, 'cantine')) {
            return (float) rand(5000, 15000); // 5k-15k FCFA
        } elseif (str_contains($normalizedName, 'responsabilité') || str_contains($normalizedName, 'fonction')) {
            return (float) rand(25000, 50000); // 25k-50k FCFA
        } elseif (str_contains($normalizedName, 'ancienneté') || str_contains($normalizedName, 'stage')) {
            return (float) rand(15000, 30000); // 15k-30k FCFA
        } elseif (str_contains($normalizedName, 'performance') || str_contains($normalizedName, 'rendement')) {
            return (float) rand(20000, 40000); // 20k-40k FCFA
        } elseif (str_contains($normalizedName, 'risque') || str_contains($normalizedName, 'pénibilité')) {
            return (float) rand(30000, 60000); // 30k-60k FCFA
        } elseif (str_contains($normalizedName, 'caisse') || str_contains($normalizedName, 'équipement')) {
            return (float) rand(10000, 20000); // 10k-20k FCFA
        } else {
            return (float) rand(10000, 25000); // 10k-25k FCFA par défaut
        }
    }

    /**
     * Calcule le montant imposable selon le traitement fiscal
     */
    private function calculateImposableAmount(float $amount, ?string $traitFisc): float
    {
        if (!$traitFisc) {
            return $amount; // Si pas de traitement fiscal spécifié, tout est imposable
        }

        $traitFisc = strtolower($traitFisc);

        if (str_contains($traitFisc, 'exo 100%') || str_contains($traitFisc, 'exonéré')) {
            return 0.0; // Entièrement exonéré
        } elseif (str_contains($traitFisc, 'exo 10%')) {
            return $amount * 0.9; // 90% imposable
        } elseif (str_contains($traitFisc, 'imposable 100%')) {
            return $amount; // Entièrement imposable
        } elseif (str_contains($traitFisc, 'exo 0%')) {
            return $amount; // Entièrement imposable (0% exonéré)
        } else {
            return $amount; // Par défaut, tout est imposable
        }
    }
}
