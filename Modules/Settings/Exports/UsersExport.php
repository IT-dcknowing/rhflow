<?php

namespace Modules\Settings\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    /**
     * Retourner la collection de données
     */
    public function collection(): Collection
    {
        return $this->users;
    }

    /**
     * Définir les en-têtes du fichier Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nom Complet',
            'Email',
            'Type',
            'Téléphone',
            'Branche',
            'Département',
            'Poste',
            'Statut',
            'Créé par',
            'Date de Création',
            'Dernière Connexion'
        ];
    }

    /**
     * Mapper les données pour chaque ligne
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $this->getTypeLabel($user->type),
            $user->phone ?? '',
            $user->userEmployee->branch->name ?? $user->branch->name ?? '',
            $user->userEmployee->department->name ?? $user->department->name ?? '',
            $user->userEmployee->designation->name ?? $user->designation->title ?? $user->designation->name ?? '',
            $user->is_active ? 'Actif' : 'Inactif',
            $user->creator->name ?? 'Inconnu',
            $user->created_at->format('d/m/Y H:i'),
            $user->last_login ? $user->last_login->format('d/m/Y H:i') : 'Jamais'
        ];
    }

    /**
     * Obtenir le label du type d'utilisateur
     */
    private function getTypeLabel($type): string
    {
        $labels = [
            'company' => 'Entreprise',
            'hr' => 'RH',
            'payroll' => 'Paie',
            'employee' => 'Employé'
        ];

        return $labels[$type] ?? 'Inconnu';
    }
}
