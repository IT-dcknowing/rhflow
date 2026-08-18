<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AllowanceOption extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui peuvent être assignés en masse
     */
    protected $fillable = [
        'code', 
        'code_compta',
        'name',
        'param_fiscal',
        'param_social',
        'type',
        'type_montant',
        'company_id',
        'is_active',
        'created_by',
    ];

    /**
     * Les attributs qui doivent être convertis
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Relation avec l'utilisateur (User)
     * Une option d'allocation appartient à un utilisateur
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Relation avec l'entreprise (Company)
     * Une option d'allocation appartient à une entreprise
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation avec les allocations (Allowances)
     * Une option d'allocation peut avoir plusieurs allocations
     */
    public function allowances(): HasMany
    {
        return $this->hasMany(Allowance::class);
    }

    /**
     * Scope pour filtrer par entreprise
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les options actives (non supprimées)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
