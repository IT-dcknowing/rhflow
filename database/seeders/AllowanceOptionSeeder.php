<?php

namespace Database\Seeders;

use App\Models\AllowanceOption;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllowanceOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Options d'allocation par défaut (issues de l'ancienne base de données)
        $defaultOptions = [
            ['name' => 'Sursalaire', 'param_fiscal' => 'exo 0%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de rendement', 'param_fiscal' => 'exo 0%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Heure supplémentaire', 'param_fiscal' => null, 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime d\'ancienneté', 'param_fiscal' => 'exo 0%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Gratification', 'param_fiscal' => 'exo 0%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Indemnité de déplacement', 'param_fiscal' => 'Remboursement de frais', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de salissure', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime d\'équipement', 'param_fiscal' => 'exo 0%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de caisse', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de panier', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de transport légale', 'param_fiscal' => 'exo 100%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime d\'outillage', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de tenue de travail', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Heure complémentaire', 'param_fiscal' => 'exo 0%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime d\'exploitation', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime d\'expatriation', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de précarité', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime d\'assiduité', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime risque', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de responsabilité', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Indemnité de logement', 'param_fiscal' => 'exo 100%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de fonction', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Allocation de congé', 'param_fiscal' => null, 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de représentation', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Indemnité kilométrique de voiture', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Allocation familiales (CNPS)', 'param_fiscal' => 'exo 100%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de stage', 'param_fiscal' => 'exo 100%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Indemnité d\'apprentissage', 'param_fiscal' => 'exo 100%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Indemnité de transport versée à un agent commercial', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Autre indemnité', 'param_fiscal' => null, 'param_social' => null, 'type' => 'default'],
            ['name' => 'Cantine', 'param_fiscal' => 'exo 10%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime d\'indemnité', 'param_fiscal' => 'exo 0%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de stage (Exo)', 'param_fiscal' => '0%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime de performance', 'param_fiscal' => 'Autre', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Prime du mois', 'param_fiscal' => '0%', 'param_social' => null, 'type' => 'default'],
            ['name' => 'Autres indemnités', 'param_fiscal' => '0%', 'param_social' => null, 'type' => 'default'],
        ];

        // Créer les options pour chaque entreprise existante
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->warn('Aucune entreprise trouvée. Création des options d\'allocation annulée.');
            return;
        }

        $totalCreated = 0;
        foreach ($companies as $company) {
            $companyCreated = 0;

            foreach ($defaultOptions as $option) {
                // Vérifier si l'option existe déjà pour cette entreprise
                $exists = AllowanceOption::where('name', $option['name'])
                    ->where('company_id', $company->id)
                    ->exists();

                if (!$exists) {
                    AllowanceOption::create([
                        'name' => $option['name'],
                        'param_fiscal' => $option['param_fiscal'],
                        'param_social' => $option['param_social'],
                        'type' => $option['type'],
                        'company_id' => $company->id,
                    ]);
                    $companyCreated++;
                }
            }

            if ($companyCreated > 0) {
                $totalCreated += $companyCreated;
                $this->command->info("{$companyCreated} options d'allocation créées pour {$company->name}");
            }
        }

        $this->command->info("Total: {$totalCreated} options d'allocation créées pour {$companies->count()} entreprise(s)");
    }
}