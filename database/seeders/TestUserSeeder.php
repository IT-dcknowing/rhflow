<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur de test avec username PHEN54
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Utilisateur Test',
                'username' => 'PHEN54',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'type' => 'employee',
                'is_active' => 1,
                'active_status' => 1,
                'created_by' => 'system',
            ]
        );

        // Créer un autre utilisateur avec username ADMIN12
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrateur Test',
                'username' => 'ADMIN12',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'type' => 'super_admin',
                'is_active' => 1,
                'active_status' => 1,
                'created_by' => 'system',
            ]
        );

        $this->command->info('Utilisateurs de test créés avec succès !');
        $this->command->info('Username: PHEN54 | Email: test@example.com | Mot de passe: password');
        $this->command->info('Username: ADMIN12 | Email: admin@example.com | Mot de passe: password');
    }
}
