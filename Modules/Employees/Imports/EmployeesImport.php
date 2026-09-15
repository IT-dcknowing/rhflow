<?php

namespace Modules\Employees\Imports;

use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\MaritalStatus;
use Modules\Employees\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EmployeesImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{
    protected $company;
    protected $creator;
    public $importedCount = 0;
    public $skippedCount  = 0;
    public $errors        = [];

    public function __construct($company, $creator)
    {
        $this->company = $company;
        $this->creator = $creator;
    }

    /**
     * Paramètres CSV — on essaie le point-virgule d'abord
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => $this->detectDelimiter(),
        ];
    }

    /**
     * Détection automatique du délimiteur
     * On charge le fichier temporaire pour inspecter la première ligne
     */
    private function detectDelimiter(): string
    {
        // Par défaut point-virgule (format FR)
        return ';';
    }

    /**
     * Traitement de toutes les lignes en une fois (plus flexible)
     */
    public function collection(Collection $rows)
    {
        // Log des colonnes détectées pour diagnostic
        if ($rows->isNotEmpty()) {
            $firstRow = $rows->first();
            $detectedKeys = array_keys($firstRow->toArray());
            \Log::info('Import - Colonnes détectées dans le fichier: ' . implode(' | ', $detectedKeys));
        }

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 car ligne 1 = en-têtes

            try {
                $data = $this->normalizeRow($row->toArray());

                // Ignorer les lignes vraiment vides
                if ($this->isEmptyRow($data)) {
                    continue;
                }

                // Construire le nom complet
                $nomComplet = $this->resolveNomComplet($data);
                if (empty($nomComplet)) {
                    $this->errors[] = "Ligne {$rowNumber}: Nom de l'employé introuvable (colonnes Nom/Prénom ou Nom Complet vides).";
                    $this->skippedCount++;
                    continue;
                }

                // Email (obligatoire — on en génère un si absent)
                $email = $this->resolveValue($data, ['email', 'e_mail', 'adresse_email', 'courriel']);
                if (empty($email)) {
                    // Générer un email basé sur le nom
                    $slug  = Str::slug($nomComplet, '.');
                    $email = strtolower($slug) . '.' . rand(100, 999) . '@' . ($this->company->email ? explode('@', $this->company->email)[1] : 'rhflow.local');
                }

                // Vérifier si l'email existe déjà
                if (User::where('email', $email)->exists()) {
                    $this->errors[] = "Ligne {$rowNumber}: Email '{$email}' déjà utilisé, ligne ignorée.";
                    $this->skippedCount++;
                    continue;
                }

                DB::beginTransaction();

                // Résolution des IDs
                $branchName     = $this->resolveValue($data, ['succursale', 'direction', 'site', 'agence', 'etablissement', 'branch']);
                $departmentName = $this->resolveValue($data, ['service', 'departement', 'department', 'dept']);
                $posteName      = $this->resolveValue($data, ['poste', 'fonction', 'designation', 'titre', 'emploi']);
                $salary         = $this->resolveValue($data, ['salaire_de_base', 'salaire', 'salaire_brut', 'remuneration', 'salary', 'salaire_mensuel']);

                $branchId      = $this->findBranchId($branchName);
                $departmentId  = $this->findDepartmentId($departmentName);
                $designationId = $this->findDesignationId($posteName);
                $maritalName   = $this->resolveValue($data, ['situation_matrimoniale', 'statut_matrimonial', 'etat_civil', 'matrimonial']);
                $maritalId     = $this->findMaritalStatusId($maritalName);
                $countryName   = $this->resolveValue($data, ['nationalite', 'pays', 'nationality', 'country']);
                $countryId     = $this->findCountryId($countryName);

                // Créer l'utilisateur
                $username = $this->resolveValue($data, ['nom_dutilisateur', 'username', 'login', 'nom_utilisateur']);
                if (empty($username)) {
                    // Même format que le formulaire de création : 4 lettres du nom + 2 chiffres (ex. KOUA70)
                    $username = User::genererUsername($nomComplet);
                } else {
                    // S'assurer que le username fourni est unique
                    $baseUsername = $username;
                    $counter = 1;
                    while (User::where('username', $username)->exists()) {
                        $username = $baseUsername . $counter++;
                    }
                }

                $user = User::create([
                    'name'              => $nomComplet,
                    'email'             => $email,
                    'username'          => $username,
                    'password'          => Hash::make($this->resolveValue($data, ['mot_de_passe', 'password', 'mdp']) ?? 'password123'),
                    'type'              => 'employee',
                    'lang'              => 'fr',
                    'company_id'        => $this->company->id,
                    'email_verified_at' => now(),
                    'created_by'        => $this->creator->id,
                ]);

                // Matricule
                $matricule = $this->resolveValue($data, ['matricule', 'matricule_id_employe', 'id_employe', 'employee_id', 'numero_employe', 'ref']);
                if (empty($matricule)) {
                    $matricule = Employee::generateUniqueId($this->company->id);
                } else {
                    // Si un matricule est fourni, s'assurer qu'il est unique pour cette entreprise
                    $baseMatricule = $matricule;
                    $counter = 1;
                    while (Employee::where('company_id', $this->company->id)->where('employee_id', $matricule)->exists()) {
                        $matricule = $baseMatricule . '-' . $counter++;
                    }
                }

                // Données employé
                $employee = Employee::create([
                    'user_id'            => $user->id,
                    'company_id'         => $this->company->id,
                    'name'               => $nomComplet,
                    'dob'                => $this->formatDate($this->resolveValue($data, ['date_de_naissance', 'dob', 'naissance', 'date_naissance'])),
                    'gender'             => $this->convertGender($this->resolveValue($data, ['sexe', 'genre', 'gender', 'sex']) ?? 'Homme'),
                    'nationality'        => $countryId,
                    'phone'              => $this->resolveValue($data, ['telephone', 'tel', 'phone', 'mobile', 'portable', 'contact']),
                    'martalstatu_id'     => $maritalId ?? 1,
                    'enfant'             => intval($this->resolveValue($data, ['nombre_denfants', 'enfants', 'nb_enfants', 'children']) ?? 0),
                    'personneinf'        => intval($this->resolveValue($data, ['personnes_infirmes_a_charge', 'handicap', 'infirmes']) ?? 0),
                    'parts'              => $this->resolveValue($data, ['nombre_de_parts', 'parts', 'nb_parts']) ?? '1',
                    'cmu'                => intval($this->resolveValue($data, ['cmu', 'assurance_maladie']) ?? 1),
                    'charge_expat'       => $this->resolveExpat($this->resolveValue($data, ['statut_expat_local_ou_expat', 'statut', 'type_contrat', 'expat']) ?? 'local'),
                    'num_cnps'           => $this->resolveValue($data, ['num_cnps', 'cnps', 'numero_cnps']),
                    'num_secu_soc'       => $this->resolveValue($data, ['num_cmu', 'num_secu_soc', 'secu', 'securite_sociale']),
                    'address'            => $this->resolveValue($data, ['adresse', 'address', 'domicile']),
                    'email'              => $email,
                    'employee_id'        => $matricule,
                    'branch_id'          => $branchId,
                    'department_id'      => $departmentId,
                    'designation_id'     => $designationId,
                    'category_job_id'    => null,
                    'category_id'        => null,
                    'end_leave'          => $this->formatDate($this->resolveValue($data, ['retour_du_dernier_conge', 'fin_conge', 'retour_conge'])),
                    'charge_cmu'         => $this->convertYesNo($this->resolveValue($data, ['prise_en_charge_cmu', 'charge_cmu', 'cmu_pris_en_charge']) ?? 'oui'),
                    'charge_cnps'        => $this->convertYesNo($this->resolveValue($data, ['prise_en_charge_cnps', 'charge_cnps', 'cnps_pris_en_charge']) ?? 'oui'),
                    'charge_its'         => $this->convertYesNo($this->resolveValue($data, ['prise_en_charge_its', 'charge_its', 'its_pris_en_charge']) ?? 'oui'),
                    'salary'             => floatval(str_replace([' ', ','], ['', '.'], $salary ?? 0)),
                    'account_holder_name'=> $this->resolveValue($data, ['nom_du_titulaire_du_compte', 'titulaire', 'nom_titulaire']) ?? $nomComplet,
                    'account_number'     => $this->resolveValue($data, ['numero_de_compte', 'num_compte', 'rib', 'iban']),
                    'bank_name'          => $this->resolveValue($data, ['nom_de_la_banque', 'banque', 'bank']),
                    'bank_identifier_code'=> $this->resolveValue($data, ['adresse_de_domiciliation', 'domiciliation', 'bic', 'swift']),
                    'orange_money'       => $this->resolveValue($data, ['orange_money', 'orange']),
                    'mtn_money'          => $this->resolveValue($data, ['mtn_money', 'mtn']),
                    'moov_money'         => $this->resolveValue($data, ['moov_money', 'moov']),
                    'wave_money'         => $this->resolveValue($data, ['wave_money', 'wave']),
                    'tax_payer_id'       => 30,
                    'is_active'          => 1,
                    'created_by'         => $this->creator->id,
                ]);

                DB::commit();
                $this->importedCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error("Erreur importation ligne {$rowNumber}: " . $e->getMessage());
                $this->errors[] = "Ligne {$rowNumber}: " . $e->getMessage();
                $this->skippedCount++;
            }
        }
    }

    /**
     * Normalise les clés du tableau : minuscules, sans accents, underscores
     */
    private function normalizeRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalizedKey = $this->normalizeKey((string) $key);
            $normalized[$normalizedKey] = is_string($value) ? trim($value) : $value;
        }
        return $normalized;
    }

    /**
     * Normalise une clé : minuscules, supprime accents, espaces → underscore
     */
    private function normalizeKey(string $key): string
    {
        // Supprime les accents
        $key = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $key) ?: $key;
        // Minuscules
        $key = strtolower($key);
        // Remplace espaces, tirets, points par underscore
        $key = preg_replace('/[\s\-\.\/]+/', '_', $key);
        // Supprime les caractères non alphanumériques (sauf underscore)
        $key = preg_replace('/[^a-z0-9_]/', '', $key);
        // Supprime les underscores en début/fin
        $key = trim($key, '_');
        return $key;
    }

    /**
     * Cherche une valeur dans le tableau avec plusieurs clés possibles
     */
    private function resolveValue(array $data, array $keys): ?string
    {
        foreach ($keys as $key) {
            $normalizedKey = $this->normalizeKey($key);
            if (isset($data[$normalizedKey]) && $data[$normalizedKey] !== '' && $data[$normalizedKey] !== null) {
                return (string) $data[$normalizedKey];
            }
        }
        return null;
    }

    /**
     * Construit le nom complet depuis différentes combinaisons de colonnes
     */
    private function resolveNomComplet(array $data): ?string
    {
        // 1. Colonne "Nom Complet" directe
        $nomComplet = $this->resolveValue($data, ['nom_complet', 'nomcomplet', 'fullname', 'full_name', 'nom_et_prenom']);
        if (!empty($nomComplet)) {
            return $nomComplet;
        }

        // 2. Combinaison Prénom + Nom
        $prenom = $this->resolveValue($data, ['prenom', 'prenoms', 'firstname', 'first_name', 'given_name']);
        $nom    = $this->resolveValue($data, ['nom', 'lastname', 'last_name', 'surname', 'family_name', 'name']);

        if (!empty($prenom) && !empty($nom)) {
            return trim($prenom . ' ' . $nom);
        }
        if (!empty($nom)) {
            return $nom;
        }
        if (!empty($prenom)) {
            return $prenom;
        }

        return null;
    }

    /**
     * Vérifie si une ligne est entièrement vide
     */
    private function isEmptyRow(array $data): bool
    {
        foreach ($data as $value) {
            if (!empty($value) && $value !== null && $value !== '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Résoudre le statut expat
     */
    private function resolveExpat(?string $value): string
    {
        if (!$value) return 'local';
        $v = strtolower(trim($value));
        return in_array($v, ['expat', 'expatrie', 'expatrié', 'expatriee', 'expatriée', 'international']) ? 'expat' : 'local';
    }

    /**
     * Helper pour trouver la branche
     */
    private function findBranchId(?string $name): ?int
    {
        if (!$name) return null;
        return Branch::where('company_id', $this->company->id)
            ->where(function($q) use ($name) {
                $q->where('name', 'LIKE', '%' . $name . '%')
                  ->orWhere('name', 'LIKE', $name . '%');
            })->first()?->id;
    }

    /**
     * Helper pour trouver le département
     */
    private function findDepartmentId(?string $name): ?int
    {
        if (!$name) return null;
        return Department::where('company_id', $this->company->id)
            ->where('name', 'LIKE', '%' . $name . '%')
            ->first()?->id;
    }

    /**
     * Helper pour trouver le poste
     */
    private function findDesignationId(?string $name): ?int
    {
        if (!$name) return null;
        return Designation::where('company_id', $this->company->id)
            ->where('name', 'LIKE', '%' . $name . '%')
            ->first()?->id;
    }

    /**
     * Helper pour trouver le pays
     */
    private function findCountryId(?string $name): ?int
    {
        if (!$name) return null;
        return \App\Models\Country::where('name', 'LIKE', '%' . $name . '%')->first()?->id;
    }

    /**
     * Helper pour trouver la situation matrimoniale
     */
    private function findMaritalStatusId(?string $name): ?int
    {
        if (!$name) return 1;
        return MaritalStatus::where('name', 'LIKE', '%' . $name . '%')->first()?->id ?? 1;
    }

    /**
     * Convertir Oui/Non en 1/0
     */
    private function convertYesNo(?string $value): int
    {
        if (!$value) return 0;
        $value = strtolower(trim($value));
        return in_array($value, ['oui', 'yes', '1', 'vrai', 'true', 'o', 'y']) ? 1 : 0;
    }

    /**
     * Convertir le genre
     */
    private function convertGender(?string $gender): string
    {
        if (!$gender) return 'Male';
        $gender = strtolower(trim($gender));
        if (in_array($gender, ['homme', 'masculin', 'male', 'm', 'h'])) return 'Male';
        if (in_array($gender, ['femme', 'féminin', 'feminin', 'female', 'f'])) return 'Female';
        return 'Male';
    }

    /**
     * Formater la date
     */
    private function formatDate($date): ?string
    {
        if (!$date || $date === '') return null;
        try {
            if (is_numeric($date)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
            }
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Générer un ID employé unique si non fourni
     */
    private function generateEmployeeId(): string
    {
        return Employee::generateUniqueId($this->company->id);
    }
}
