<?php

namespace Modules\Evenements\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Réunion',
                'color' => '#3498db',
                'icon' => 'fa fa-users',
                'is_default' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Formation',
                'color' => '#2ecc71',
                'icon' => 'fa fa-graduation-cap',
                'is_default' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Anniversaire',
                'color' => '#e74c3c',
                'icon' => 'fa fa-birthday-cake',
                'is_default' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Congé',
                'color' => '#9b59b6',
                'icon' => 'fa fa-birthday-cake',
                'is_default' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Événement entreprise',
                'color' => '#f39c12',
                'icon' => 'fa fa-birthday-cake',
                'is_default' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maintenance',
                'color' => '#7f8c8d',
                'icon' => 'fa fa-birthday-cake',
                'is_default' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rendez-vous client',
                'color' => '#1abc9c',
                'icon' => 'fa fa-birthday-cake',
                'is_default' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Vérifier si la table existe avant d'insérer
        if (DB::getSchemaBuilder()->hasTable('event_types')) {
            // Vérifier si la table est vide
            if (DB::table('event_types')->count() === 0) {
                DB::table('event_types')->insert($types);
                $this->command->info('Table event_types peuplée avec succès !');
            } else {
                $this->command->info('La table event_types contient déjà des données. Aucune insertion effectuée.');
            }
        } else {
            $this->command->error('La table event_types n\'existe pas dans la base de données.');
        }
    }
}