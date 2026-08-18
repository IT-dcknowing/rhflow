<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les permissions pour le système RH Flow

        // Permissions Super Admin
        Permission::create(['name' => 'super.admin.access', 'guard_name' => 'web']);
        Permission::create(['name' => 'super.admin.dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'super.admin.enterprises.manage', 'guard_name' => 'web']);
        Permission::create(['name' => 'super.admin.packs.manage', 'guard_name' => 'web']);
        Permission::create(['name' => 'super.admin.users.manage', 'guard_name' => 'web']);
        Permission::create(['name' => 'super.admin.reports.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'super.admin.settings.manage', 'guard_name' => 'web']);
        Permission::create(['name' => 'super.admin.support.access', 'guard_name' => 'web']);

        // Permissions Entreprise
        Permission::create(['name' => 'company.access', 'guard_name' => 'web']);
        Permission::create(['name' => 'company.dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'company.employees.manage', 'guard_name' => 'web']);
        Permission::create(['name' => 'company.attendance.manage', 'guard_name' => 'web']);
        Permission::create(['name' => 'company.leaves.manage', 'guard_name' => 'web']);
        Permission::create(['name' => 'company.payroll.manage', 'guard_name' => 'web']);
        Permission::create(['name' => 'company.reports.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'company.settings.manage', 'guard_name' => 'web']);

        // Permissions RH
        Permission::create(['name' => 'hr.access', 'guard_name' => 'web']);
        Permission::create(['name' => 'hr.dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'hr.employees.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'hr.employees.edit', 'guard_name' => 'web']);
        Permission::create(['name' => 'hr.attendance.manage', 'guard_name' => 'web']);
        Permission::create(['name' => 'hr.leaves.approve', 'guard_name' => 'web']);
        Permission::create(['name' => 'hr.recruitment.manage', 'guard_name' => 'web']);
        Permission::create(['name' => 'hr.reports.view', 'guard_name' => 'web']);

        // Permissions fiscales pour l'Afrique de l'Ouest
        Permission::create(['name' => 'fiscal.access', 'guard_name' => 'web']);
        Permission::create(['name' => 'fiscal.calculations.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'fiscal.declarations.manage', 'guard_name' => 'web']);
        Permission::create(['name' => 'fiscal.reports.generate', 'guard_name' => 'web']);
        Permission::create(['name' => 'fiscal.west_africa.manage', 'guard_name' => 'web']); // Gestion spécifique Afrique de l'Ouest

        // Permissions Employé
        Permission::create(['name' => 'employee.access', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee.dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee.profile.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee.profile.edit', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee.attendance.check', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee.leaves.request', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee.payslips.view', 'guard_name' => 'web']);

        // Créer les rôles et assigner les permissions

        // Rôle Super Admin
        $superAdminRole = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo(Permission::all()); // Toutes les permissions

        // Rôle Entreprise
        $companyRole = Role::create(['name' => 'company', 'guard_name' => 'web']);
        $companyRole->givePermissionTo([
            'company.access',
            'company.dashboard',
            'company.employees.manage',
            'company.attendance.manage',
            'company.leaves.manage',
            'company.payroll.manage',
            'company.reports.view',
            'company.settings.manage',
        ]);

        // Rôle RH
        $hrRole = Role::create(['name' => 'hr', 'guard_name' => 'web']);
        $hrRole->givePermissionTo([
            'hr.access',
            'hr.dashboard',
            'hr.employees.view',
            'hr.employees.edit',
            'hr.attendance.manage',
            'hr.leaves.approve',
            'hr.recruitment.manage',
            'hr.reports.view',
        ]);

        // Rôle Paie (avec permissions fiscales)
        $paieRole = Role::create(['name' => 'paie', 'guard_name' => 'web']);
        $paieRole->givePermissionTo([
            'paie.access',
            'paie.dashboard',
            'paie.salaries.manage',
            'paie.payroll.generate',
            'paie.taxes.manage',
            'paie.reports.generate',
        ]);

        // Rôle Responsable Déclarations Fiscales
        $fiscalRole = Role::create(['name' => 'fiscal', 'guard_name' => 'web']);
        $fiscalRole->givePermissionTo([
            'fiscal.access',
            'fiscal.calculations.view',
            'fiscal.declarations.manage',
            'fiscal.reports.generate',
            'fiscal.west_africa.manage',
        ]);

        // Rôle Employé
        $employeeRole = Role::create(['name' => 'employee', 'guard_name' => 'web']);
        $employeeRole->givePermissionTo([
            'employee.access',
            'employee.dashboard',
            'employee.profile.view',
            'employee.profile.edit',
            'employee.attendance.check',
            'employee.leaves.request',
            'employee.payslips.view',
        ]);

        $this->command->info('Rôles et permissions créés avec succès !');
        $this->command->info('Rôles disponibles : super-admin, company, hr, paie, fiscal, employee');
    }
}
