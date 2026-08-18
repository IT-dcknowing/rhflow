<?php

namespace Modules\PaieSalaries\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeRetenuesTableSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['libelle' => 'Impôts bruts avant RICF', 'type' => 'percentage'],
            ['libelle' => 'Réduction pour Charges de Famille', 'type' => 'percentage'],
            ['libelle' => 'Impôts Nets', 'type' => 'fixed'],
            ['libelle' => 'Cotisation Retraite CNPS', 'type' => 'percentage'],
            ['libelle' => 'Couverture Maladie Universelle', 'type' => 'fixed'],
            ['libelle' => 'Contribution Employeur', 'type' => 'percentage'],
            ['libelle' => 'Contribution employeur (Expatrié)', 'type' => 'percentage'],
            ['libelle' => 'Taxe d\'Apprentissage', 'type' => 'percentage'],
            ['libelle' => 'Taxe.F.P.C', 'type' => 'percentage'],
            ['libelle' => 'Cotisation retraite employeur', 'type' => 'percentage'],
            ['libelle' => 'Accident de travail', 'type' => 'percentage'],
            ['libelle' => 'Prestation Familiale', 'type' => 'percentage'],
            ['libelle' => 'Couverture Maladie Universelle employeur', 'type' => 'fixed'],
            ['libelle' => 'Oppositions', 'type' => 'fixed'],
            ['libelle' => 'Saisies-Arrêt', 'type' => 'fixed'],
            ['libelle' => 'Avis à tiers détenteur', 'type' => 'fixed'],
            ['libelle' => 'Prélèvement pour assurance', 'type' => 'fixed'],
            ['libelle' => 'Rétrocession de retenue', 'type' => 'fixed'],
        ];

        foreach ($types as $type) {
            DB::table('type_retenues')->insert([
                'libelle' => $type['libelle'],
                'type' => $type['type'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}