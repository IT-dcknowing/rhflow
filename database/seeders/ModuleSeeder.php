<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['name' => 'Employés', 'alias' => 'employee', 'description' => 'Gestion des utilisateurs et employés', 'is_active' => true],
            ['name' => 'Contrats', 'alias' => 'contract', 'description' => 'Gestion des contrats', 'is_active' => true],
            ['name' => 'Time', 'alias' => 'time', 'description' => 'Gestion des absences et heures supplémentaires', 'is_active' => true],
            ['name' => 'Congés', 'alias' => 'leave', 'description' => 'Gestion des congés', 'is_active' => true],
            ['name' => 'Déclarations', 'alias' => 'declaration', 'description' => 'Gestion des déclarations', 'is_active' => true],
            ['name' => 'Evènements', 'alias' => 'event', 'description' => 'Gestion des évènements', 'is_active' => true],
            ['name' => 'Ruptures', 'alias' => 'rupture', 'description' => 'Gestion des ruptures', 'is_active' => true],
            ['name' => 'Impot', 'alias' => 'impot', 'description' => 'Gestion des impôts', 'is_active' => true],
            ['name' => 'Salaires', 'alias' => 'salary', 'description' => 'Gestion des salaires', 'is_active' => true],
            ['name' => 'Prêts', 'alias' => 'loans', 'description' => 'Gestion des prêts', 'is_active' => true],
            ['name' => 'Avantage en nature', 'alias' => 'natureavantage', 'description' => 'Gestion des avantages en nature', 'is_active' => true],
            ['name' => 'Pointeuses', 'alias' => 'pointuse', 'description' => 'Gestion des pointeuses', 'is_active' => true],
            ['name' => 'Utilisateurs', 'alias' => 'user', 'description' => 'Gestion des utilisateurs', 'is_active' => true],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['alias' => $module['alias']],
                $module
            );
        }
    }
}
