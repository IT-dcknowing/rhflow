<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si un Super Admin existe déjà
        $existingSuperAdmin = User::where('type', 'super_admin')->first();

        if ($existingSuperAdmin) {
            $this->command->info('Super Admin déjà existant : ' . $existingSuperAdmin->email);
            return;
        }

        // Créer le Super Admin principal
        $superAdmin = User::create([
            'name' => 'Super Administrateur',
            'username' => 'BINO91',
            'email' => 'admin@rhflow.com',
            'password' => Hash::make('AdminRH2024!'), // Mot de passe sécurisé
            'type' => 'super_admin',
            'lang' => 'fr',
            'plan' => null, // Pas de limite pour le Super Admin
            'plan_expire_date' => null,
            'storage_limit' => 10000.00, // 10 GB pour le Super Admin
            'is_active' => 1,
            'active_status' => 1,
            'created_by' => 'system',
            'dark_mode' => 'auto',
            'messenger_color' => '#263d88',
            'colorone' => '#263d88',
            'colortwo' => '#ffffff',
            'attendance_type' => 'manuel',
            'ip_serveur' => request()->ip(),
            'email_verified_at' => now(),
        ]);

        $this->command->info('Super Admin créé avec succès !');
        $this->command->info('Email : admin@rhflow.com');
        $this->command->info('Mot de passe : AdminRH2024!');
        $this->command->warn('⚠️  Changez le mot de passe après la première connexion !');

        // Créer quelques entreprises de test
        $this->createTestCompanies();

        $this->command->info('Entreprises de test créées');
    }

    /**
     * Créer quelques entreprises de test
     */
    private function createTestCompanies(): void
    {
        $companies = [
            [
                'name' => 'TechCorp SARL',
                'email' => 'contact@techcorp.com',
                'username' => 'techcorp',
                'plan' => 3, // Enterprise
                'type' => 'company',
            ],
            [
                'name' => 'CommercePlus',
                'email' => 'info@commerceplus.com',
                'username' => 'commerceplus',
                'plan' => 2, // Professional
                'type' => 'company',
            ],
            [
                'name' => 'StartupLab',
                'email' => 'hello@startuplab.com',
                'username' => 'startuplab',
                'plan' => 1, // Starter
                'type' => 'company',
            ],
        ];

        foreach ($companies as $companyData) {
            User::create([
                'name' => $companyData['name'],
                'username' => $companyData['username'],
                'email' => $companyData['email'],
                'password' => Hash::make('password123'),
                'type' => $companyData['type'],
                'plan' => $companyData['plan'],
                'plan_expire_date' => now()->addYear(),
                'storage_limit' => $companyData['plan'] * 50.0, // 50GB par niveau de plan
                'is_active' => 1,
                'active_status' => 1,
                'created_by' => 'superadmin',
                'lang' => 'fr',
                'dark_mode' => 'auto',
                'messenger_color' => '#263d88',
                'colorone' => '#263d88',
                'colortwo' => '#ffffff',
                'attendance_type' => 'manuel',
                'ip_serveur' => '127.0.0.1',
                'email_verified_at' => now(),
            ]);
        }
    }
}
