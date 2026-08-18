<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CompanyDocument extends Model
{
    use HasFactory;

    // Types de documents disponibles
    const DOCUMENT_TYPES = [
        'statuts' => 'Statuts',
        'rccm' => 'Registre du Commerce (RCCM)',
        'dfe' => 'Déclaration Fiscale d\'Existence (DFE)',
        'cnps' => 'Immatriculation CNPS',
        'inspection' => 'Inspection du Travail',
        'reglement_interieur' => 'Règlement Intérieur',
        'autre' => 'Autre'
    ];

    const REQUIRED_DOCUMENTS = [
        'statuts',
        'rccm',
        'dfe'
    ];

    protected $fillable = [
        'document_type',
        'document_name',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'issue_date',
        'expiry_date',
        'metadata',
        'is_required',
        'is_verified',
        'verification_date',
        'verified_by',
        'verification_notes',
        'company_id',
        'is_active',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'verification_date' => 'date',
        'is_required' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Relation avec l'entreprise
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    /**
     * Scope pour les documents actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les documents d'une entreprise
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope pour les documents requis
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope pour les documents vérifiés
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope pour les documents expirés
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    /**
     * Scope pour les documents bientôt expirés (30 jours)
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('expiry_date', '<=', now()->addDays(30))
                    ->where('expiry_date', '>', now());
    }

    /**
     * Scope par type de document
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Obtenir le nom du type de document
     */
    public function getDocumentTypeNameAttribute(): string
    {
        return self::DOCUMENT_TYPES[$this->document_type] ?? ucfirst($this->document_type);
    }

    /**
     * Obtenir l'URL du fichier
     */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return Storage::url($this->file_path);
    }

    /**
     * Vérifier si le document est expiré
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date < now();
    }

    /**
     * Vérifier si le document expire bientôt
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date <= now()->addDays(30) && $this->expiry_date > now();
    }

    /**
     * Obtenir le statut du document
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->is_verified) {
            return 'verified';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        if ($this->isExpiringSoon()) {
            return 'expiring_soon';
        }

        return 'pending';
    }

    /**
     * Obtenir la classe CSS pour le statut
     */
    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'verified' => 'success',
            'expired' => 'danger',
            'expiring_soon' => 'warning',
            'inactive' => 'secondary',
            default => 'primary',
        };
    }

    /**
     * Obtenir le badge pour le statut
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'verified' => '✅ Vérifié',
            'expired' => '❌ Expiré',
            'expiring_soon' => '⚠️ Expire bientôt',
            'inactive' => '⭕ Inactif',
            default => '⏳ En attente',
        };
    }

    /**
     * Formater la taille du fichier
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $size = $this->file_size;

        if ($size >= 1073741824) {
            return number_format($size / 1073741824, 2) . ' GB';
        }

        if ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' MB';
        }

        if ($size >= 1024) {
            return number_format($size / 1024, 2) . ' KB';
        }

        return $size . ' bytes';
    }

    /**
     * Vérifier si c'est un document requis
     */
    public function isRequired(): bool
    {
        return in_array($this->document_type, self::REQUIRED_DOCUMENTS) || $this->is_required;
    }

    /**
     * Obtenir les documents requis manquants pour une entreprise
     */
    public static function getMissingRequiredDocuments(int $companyId): array
    {
        $existingTypes = self::forCompany($companyId)
            ->active()
            ->pluck('document_type')
            ->toArray();

        $missing = [];
        foreach (self::REQUIRED_DOCUMENTS as $type) {
            if (!in_array($type, $existingTypes)) {
                $missing[] = $type;
            }
        }

        return $missing;
    }
}
