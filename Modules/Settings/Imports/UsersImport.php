<?php

namespace Modules\Settings\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Validation\Rule;

/**
 * Import d'utilisateurs depuis le même format que l'export (Nom Complet, Email, Type, Statut…).
 * Une ligne invalide est écartée et signalée, sans bloquer les autres.
 */
class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    protected $company;
    protected $creator;
    protected $skipDuplicates;

    /** Comptes créés et doublons ignorés, pour le message de fin d'import */
    public int $importes = 0;
    public int $ignores = 0;

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
        // Doublon (compte existant ou ligne répétée dans le fichier) : l'email est unique en base
        if (User::where('email', $row['email'])->exists()) {
            $this->ignores++;
            return null;
        }

        $this->importes++;

        return new User([
            'name' => $row['nom_complet'],
            'username' => User::genererUsername($row['nom_complet']),
            'email' => $row['email'],
            'password' => Hash::make($row['mot_de_passe'] ?? 'password123'),
            'type' => $row['type'],
            'company_id' => $this->company->id,
            'is_active' => $this->convertStatus($row['statut'] ?? null),
            'created_by' => $this->creator->id,
        ]);
    }

    /**
     * Libellés du fichier (« Entreprise », « RH », « Paie », « Employé ») convertis avant la validation
     */
    public function prepareForValidation($data, $index)
    {
        $data['nom_complet'] = trim((string) ($data['nom_complet'] ?? ''));
        $data['email'] = trim((string) ($data['email'] ?? ''));
        $data['type'] = $this->convertType($data['type'] ?? '');
        $data['statut'] = ($data['statut'] ?? null) !== null ? mb_strtolower(trim((string) $data['statut'])) : null;

        return $data;
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'nom_complet' => 'required|string|max:255',
            // Avec « Ignorer les doublons », un email existant est écarté dans model() au lieu d'être refusé
            'email' => array_filter([
                'required',
                'email',
                'max:255',
                $this->skipDuplicates ? null : Rule::unique('users', 'email'),
            ]),
            'type' => 'required|string|in:company,hr,payroll,employee',
            'statut' => 'nullable|string|in:actif,inactif,active,inactive,oui,non,1,0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nom_complet.required' => 'le nom complet est obligatoire.',
            'email.required' => "l'email est obligatoire.",
            'email.email' => "l'email n'est pas valide.",
            'email.unique' => 'cet email est déjà utilisé (cochez « Ignorer les doublons » pour passer ces lignes).',
            'type.required' => 'le type est obligatoire (Entreprise, RH, Paie ou Employé).',
            'type.in' => 'type inconnu, utilisez Entreprise, RH, Paie ou Employé.',
            'statut.in' => 'statut inconnu, utilisez Actif ou Inactif.',
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
            'statut' => 'Statut',
        ];
    }

    /**
     * Convertir le type d'utilisateur du format Excel vers le format base de données.
     * Une valeur inconnue est laissée telle quelle pour être signalée par la validation.
     */
    private function convertType($type): string
    {
        $valeur = mb_strtolower(trim((string) $type));
        $typeMap = [
            'entreprise' => 'company',
            'company' => 'company',
            'rh' => 'hr',
            'responsable rh' => 'hr',
            'hr' => 'hr',
            'paie' => 'payroll',
            'responsable paie' => 'payroll',
            'payroll' => 'payroll',
            'employé' => 'employee',
            'employe' => 'employee',
            'employee' => 'employee',
        ];

        return $typeMap[$valeur] ?? $valeur;
    }

    /**
     * Convertir le statut du format Excel vers boolean
     */
    private function convertStatus($status): bool
    {
        if ($status === null || $status === '') {
            return true; // Par défaut actif
        }

        return in_array(mb_strtolower((string) $status), ['actif', 'active', '1', 'true', 'oui']);
    }
}
