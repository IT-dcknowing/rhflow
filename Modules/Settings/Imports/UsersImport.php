<?php

namespace Modules\Settings\app\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Validation\Rule;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    protected $company;
    protected $creator;
    protected $skipDuplicates;

    public function __construct($company, $creator, $skipDuplicates = false)
    {
        $this->company = $company;
        $this->creator = $creator;
        $this->skipDuplicates = $skipDuplicates;
    }

    /**
     * Créer un modèle à partir d'une ligne du fichier Excel
     */
    public function model(array $row)
    {
        // Vérifier si l'utilisateur existe déjà
        if ($this->skipDuplicates) {
            $existingUser = User::where('email', $row['email'])
                ->where('company_id', $this->company->id)
                ->first();

            if ($existingUser) {
                return null; // Ignorer cette ligne
            }
        }

        // Créer l'utilisateur
        return new User([
            'name' => $row['nom_complet'],
            'email' => $row['email'],
            'password' => Hash::make($row['mot_de_passe'] ?? 'password123'),
            'type' => $this->convertType($row['type']),
            'phone' => $row['telephone'] ?? null,
            'company_id' => $this->company->id,
            'branch_id' => $this->findBranchId($row['branche']),
            'department_id' => $this->findDepartmentId($row['departements']),
            'designation_id' => $this->findDesignationId($row['poste']),
            'is_active' => $this->convertStatus($row['statut']),
            'created_by' => $this->creator->id,
        ]);
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'nom_complet' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('company_id', $this->company->id);
                })
            ],
            'type' => 'required|string|in:company,hr,payroll,employee',
            'telephone' => 'nullable|string|max:20',
            'branche' => 'nullable|string',
            'departements' => 'nullable|string',
            'poste' => 'nullable|string',
            'statut' => 'nullable|string|in:actif,inactif,Actif,Inactif',
        ];
    }

    /**
     * Noms des colonnes personnalisés pour la validation
     */
    public function customValidationAttributes()
    {
        return [
            'nom_complet' => 'Nom Complet',
            'email' => 'Email',
            'type' => 'Type',
            'telephone' => 'Téléphone',
            'branche' => 'Branche',
            'departements' => 'Département',
            'poste' => 'Poste',
            'statut' => 'Statut',
        ];
    }

    /**
     * Convertir le type d'utilisateur du format Excel vers le format base de données
     */
    private function convertType($type): string
    {
        $typeMap = [
            'entreprise' => 'company',
            'Entreprise' => 'company',
            'company' => 'company',
            'rh' => 'hr',
            'RH' => 'hr',
            'hr' => 'hr',
            'paie' => 'payroll',
            'Paie' => 'payroll',
            'payroll' => 'payroll',
            'employé' => 'employee',
            'Employé' => 'employee',
            'employee' => 'employee',
        ];

        return $typeMap[strtolower($type)] ?? 'employee';
    }

    /**
     * Convertir le statut du format Excel vers boolean
     */
    private function convertStatus($status): bool
    {
        if (!$status) {
            return true; // Par défaut actif
        }

        $activeStatuses = ['actif', 'Actif', 'active', 'Active', '1', 'true', 'oui', 'Oui'];

        return in_array(strtolower($status), $activeStatuses);
    }

    /**
     * Trouver l'ID de la branche par son nom
     */
    private function findBranchId($branchName)
    {
        if (!$branchName) {
            return null;
        }

        $branch = $this->company->branches()
            ->where('name', 'LIKE', '%' . $branchName . '%')
            ->first();

        return $branch ? $branch->id : null;
    }

    /**
     * Trouver l'ID du département par son nom
     */
    private function findDepartmentId($departmentName)
    {
        if (!$departmentName) {
            return null;
        }

        $department = $this->company->departments()
            ->where('name', 'LIKE', '%' . $departmentName . '%')
            ->first();

        return $department ? $department->id : null;
    }

    /**
     * Trouver l'ID du poste par son titre
     */
    private function findDesignationId($designationTitle)
    {
        if (!$designationTitle) {
            return null;
        }

        $designation = $this->company->designations()
            ->where('title', 'LIKE', '%' . $designationTitle . '%')
            ->first();

        return $designation ? $designation->id : null;
    }
}
