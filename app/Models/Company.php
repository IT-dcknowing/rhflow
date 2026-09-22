<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\EmployeeDay;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'website',
        'logo',
        'industry', // Secteur d'activité
        'size',
        'description',
        'tax_id',
        'registration_number',
        'working_hours_per_week', // Nombre d'heures de travail par semaine
        'working_days_per_week', // Nombre de jours travaillés par semaine
        'electronic_signature', // Signature électronique
        'electronic_stamp', // Cachet électronique
        'is_active',
        'subscription_status',
        'subscription_start_date',
        'subscription_end_date',
        'max_employees',
        'max_storage_gb',
        'current_storage_used', 
        'settings',
        'default_attendance_type',
        'allow_multiple_attendance_types',
        'attendance_settings',
        'employee_prefix',
        'accident_taux',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'max_employees' => 'integer',
        'max_storage_gb' => 'decimal:2',
        'current_storage_used' => 'decimal:2',
        'settings' => 'array',
        'allow_multiple_attendance_types' => 'boolean',
        'attendance_settings' => 'array',
        'employee_prefix' => 'string',
    ];

    /**
     * Vérifier si l'entreprise est active
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->subscription_status === 'active';
    }
 
    /**
     * Vérifier si l'abonnement est actif
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === 'active' &&
               (!$this->subscription_end_date || $this->subscription_end_date->isFuture());
    }

    /**
     * Vérifier si l'entreprise peut ajouter des employés
     */
    public function canAddEmployees(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        return !$this->max_employees || $this->users()->count() < $this->max_employees;
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Vérifier si l'entreprise peut utiliser plus de stockage
     */
    public function canUseStorage(float $additionalStorage = 0): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $currentUsage = $this->current_storage_used + $additionalStorage;
        return $currentUsage <= $this->max_storage_gb;
    }

    /**
     * Obtenir le pourcentage d'utilisation du stockage
     */
    public function getStorageUsagePercentage(): float
    {
        if ($this->max_storage_gb <= 0) {
            return 0;
        }

        return min(($this->current_storage_used / $this->max_storage_gb) * 100, 100);
    }

    /**
     * Obtenir le nombre d'employés restants possibles
     */
    public function getRemainingEmployeeSlots(): int
    {
        if (!$this->max_employees) {
            return PHP_INT_MAX; // Illimité
        }

        return max(0, $this->max_employees - $this->users()->count());
    }

    /**
     * Mettre à jour l'utilisation du stockage
     */
    public function updateStorageUsage(float $usedStorage): void
    {
        $this->update(['current_storage_used' => $usedStorage]);
    }

    /**
     * Activer l'entreprise
     */
    public function activate(): void
    {
        $this->update([
            'is_active' => true,
            'subscription_status' => 'active',
        ]);
    }

    /**
     * Désactiver l'entreprise
     */
    public function deactivate(): void
    {
        // « inactive » n'existe pas dans l'enum de la colonne
        // (trial, pending, active, suspended, cancelled, expired) : en mode SQL
        // strict la mise à jour était rejetée.
        $this->update([
            'is_active' => false,
            'subscription_status' => 'suspended',
        ]);
    }

    /**
     * Scope pour les entreprises actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('subscription_status', 'active');
    }

    /**
     * Scope pour les entreprises expirées
     */
    public function scopeExpired($query)
    {
        return $query->where('subscription_end_date', '<', now());
    }

    /**
     * Scope pour les entreprises par industrie
     */
    public function scopeByIndustry($query, string $industry)
    {
        return $query->where('industry', $industry);
    }

    /**
     * Obtenir l'URL de la signature électronique
     */
    public function getElectronicSignatureUrlAttribute(): string
    {
        if (!$this->electronic_signature) {
            return asset('images/signatures/default.png');
        }

        if (filter_var($this->electronic_signature, FILTER_VALIDATE_URL)) {
            return $this->electronic_signature;
        }

        return \Storage::url('signatures/' . $this->electronic_signature);
    }

    /**
     * Obtenir l'URL du cachet électronique
     */
    public function getElectronicStampUrlAttribute(): string
    {
        if (!$this->electronic_stamp) {
            return asset('images/stamps/default.png');
        }

        if (filter_var($this->electronic_stamp, FILTER_VALIDATE_URL)) {
            return $this->electronic_stamp;
        }

        return \Storage::url('stamps/' . $this->electronic_stamp);
    }

    /**
     * Obtenir le secteur d'activité avec label lisible
     */
    public function getIndustryLabelAttribute(): string
    {
        $industries = [
            'technology' => 'Technologie',
            'healthcare' => 'Santé',
            'finance' => 'Finance',
            'education' => 'Éducation',
            'retail' => 'Commerce de détail',
            'manufacturing' => 'Industrie',
            'construction' => 'Construction',
            'consulting' => 'Conseil',
            'real_estate' => 'Immobilier',
            'transport' => 'Transport',
            'hospitality' => 'Hôtellerie',
            'food' => 'Agroalimentaire',
            'other' => 'Autre',
        ];

        return $industries[$this->industry] ?? 'Non défini';
    }

    /**
     * Obtenir la taille de l'entreprise avec label lisible
     */
    public function getSizeLabelAttribute(): string
    {
        return match ($this->size) {
            'startup' => 'Startup',
            'small' => 'Petite entreprise (1-50 employés)',
            'medium' => 'Moyenne entreprise (51-200 employés)',
            'large' => 'Grande entreprise (201-1000 employés)',
            'enterprise' => 'Grande entreprise (1000+ employés)',
            default => 'Non définie',
        };
    }

    /**
     * Vérifier si l'entreprise peut fonctionner aujourd'hui
     */
    public function canWorkToday(): bool
    {
        $today = now()->dayOfWeekIso; // 1 = Lundi, 7 = Dimanche

        // Si l'entreprise ne travaille pas le dimanche (7)
        if ($today === 7 && $this->working_days_per_week < 7) {
            return false;
        }

        // Si aujourd'hui est samedi (6) et qu'elle ne travaille que 5 jours
        if ($today === 6 && $this->working_days_per_week <= 5) {
            return false;
        }

        return true;
    }

    /**
     * Obtenir les heures de travail normales par jour
     */
    public function getDailyWorkingHours(): float
    {
        if ($this->working_days_per_week <= 0) {
            return 0;
        }

        return $this->working_hours_per_week / $this->working_days_per_week;
    }

    /**
     * Mettre à jour la signature électronique
     */
    public function updateElectronicSignature(string $signaturePath): void
    {
        $this->update(['electronic_signature' => $signaturePath]);
    }

    /**
     * Mettre à jour le cachet électronique
     */
    public function updateElectronicStamp(string $stampPath): void
    {
        $this->update(['electronic_stamp' => $stampPath]);
    }

    public function companyPlan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function userName()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'industry', 'slug', 'id');
    }

     /**
     * Obtenir l'URL du logo
     */
    public function getLogoUrlAttribute(): string
    {
        if (!$this->logo) {
            return asset('images/logos/default.png');
        }

        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        // Utiliser Storage::url pour les fichiers du disque public
        return \Storage::url('logos/' . $this->logo);
    }

    /**
     * Obtenir les paramètres de couleur primaire
     */
    public function getPrimaryColorAttribute(): string
    {
        $settings = $this->settings ?? [];
        return $settings['primary_color'] ?? '#696cff';
    }

    /**
     * Obtenir les paramètres de couleur secondaire
     */
    public function getSecondaryColorAttribute(): string
    {
        $settings = $this->settings ?? [];
        return $settings['secondary_color'] ?? '#03c3ec';
    }

    /**
     * Obtenir la couleur de fond de l'en-tête
     */
    public function getHeaderBgColorAttribute(): string
    {
        $settings = $this->settings ?? [];
        return $settings['header_bg_color'] ?? '#ffffff';
    }

    /**
     * Obtenir la devise
     */
    public function getCurrencyAttribute(): string
    {
        $settings = $this->settings ?? [];
        return $settings['currency'] ?? 'XOF';
    }

    /**
     * Obtenir le cycle de paie
     */
    public function getPayrollCycleAttribute(): int
    {
        $settings = $this->settings ?? [];
        return $settings['payroll_cycle'] ?? 1; // 1 = mensuel
    }

    /**
     * Obtenir le jour de paie
     */
    public function getPayDayAttribute(): int
    {
        $settings = $this->settings ?? [];
        return $settings['pay_day'] ?? 25;
    }

    /**
     * Mettre à jour les paramètres de couleur
     */
    public function updateColors(array $colors): void
    {
        $settings = $this->settings ?? [];
        $settings = array_merge($settings, $colors);
        $this->update(['settings' => $settings]);
    }

    /**
     * Mettre à jour le logo
     */
    public function updateLogo(string $logoPath): void
    {
        $this->update(['logo' => $logoPath]);
    }

    /**
     * Mettre à jour la signature électronique
     */
    public function updateSignature(string $signaturePath): void
    {
        $this->update(['electronic_signature' => $signaturePath]);
    }

    /**
     * Mettre à jour le cachet électronique
     */
    public function updateStamp(string $stampPath): void
    {
        $this->update(['electronic_stamp' => $stampPath]);
    }

    /**
     * Obtenir la couleur primaire du thème avec valeur par défaut
     */
    public function getThemePrimaryColor(): string
    {
        return $this->primary_color ?: '#696cff';
    }

    /**
     * Obtenir la couleur secondaire du thème avec valeur par défaut
     */
    public function getThemeSecondaryColor(): string
    {
        return $this->secondary_color ?: '#03c3ec';
    }

    /**
     * Obtenir la couleur de fond de l'en-tête avec valeur par défaut
     */
    public function getThemeHeaderBgColor(): string
    {
        return $this->header_bg_color ?: '#ffffff';
    }

    /**
     * Obtenir le cycle de paie du thème (valeur par défaut)
     */
    public function getThemePayrollCycle(): string
    {
        return $this->payroll_cycle ?? 'monthly';
    }

    /**
     * Obtenir la couleur du texte pour l'en-tête selon la couleur de fond
     */
    public function getThemeHeaderTextColor(): string
    {
        return $this->getThemeHeaderBgColor() === '#ffffff' ? '#000000' : '#ffffff';
    }

    /**
     * Relation avec les utilisateurs de l'entreprise
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'config_company');
    }

    /**
     * branches
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * departments
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * designations
     */
    public function designations()
    {
        return $this->hasMany(Designation::class);
    }

    public function employeesMonth()
    {
        return $this->hasMany(EmployeeMonth::class);
    }

    public function employeesDay()
    {
        return $this->hasMany(EmployeeDay::class);
    }

    /**
     * Documents de société
     */
    public function documents()
    {
        return $this->hasMany(\Modules\Settings\app\Models\CompanyDocument::class);
    }

    /**
     * Obtenir le type de présence par défaut avec valeur par défaut
     */
    public function getDefaultAttendanceType(): string
    {
        return $this->default_attendance_type ?? 'manual';
    }

    /**
     * Vérifier si plusieurs types de présence sont autorisés
     */
    public function allowsMultipleAttendanceTypes(): bool
    {
        return $this->allow_multiple_attendance_types ?? false;
    }

    /**
     * Obtenir les paramètres de présence avec valeurs par défaut
     */
    public function getAttendanceSettings(): array
    {
        return $this->attendance_settings ?? [];
    }

    /**
     * Obtenir les paramètres QR Code
     */
    public function getQrCodeSettings(): array
    {
        return $this->getAttendanceSettings()['qr_code'] ?? [];
    }

    /**
     * Obtenir les paramètres biométriques
     */
    public function getBiometricSettings(): array
    {
        return $this->getAttendanceSettings()['biometric'] ?? [];
    }

    /**
     * Obtenir le fuseau horaire
     */
    public function getTimezoneAttribute(): string
    {
        $settings = $this->settings ?? [];
        return $settings['timezone'] ?? 'Africa/Abidjan';
    }

    /**
     * Obtenir l'état/région
     */
    public function getStateAttribute(): string
    {
        $settings = $this->settings ?? [];
        return $settings['state'] ?? '';
    }

    /**
     * Obtenir la latitude
     */
    public function getLatitudeAttribute(): ?float
    {
        $settings = $this->settings ?? [];
        return $settings['latitude'] ?? null;
    }

    /**
     * Obtenir la longitude
     */
    public function getLongitudeAttribute(): ?float
    {
        $settings = $this->settings ?? [];
        return $settings['longitude'] ?? null;
    }

    /**
     * Obtenir les données de localisation
     */
    public function getLocationDataAttribute(): ?string
    {
        $settings = $this->settings ?? [];
        return $settings['location_data'] ?? null;
    }
}