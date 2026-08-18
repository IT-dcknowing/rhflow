<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'has_time_clock',
        'time_clock_ip',
        'time_clock_mac',
        'check_in_start',
        'check_in_end',
        'check_out_start',
        'check_out_end',
        'allow_remote_clock',
        'max_distance_meters',
        'branch_id',
        'company_id',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'has_time_clock' => 'boolean',
        'check_in_start' => 'datetime:H:i:s',
        'check_in_end' => 'datetime:H:i:s',
        'check_out_start' => 'datetime:H:i:s',
        'check_out_end' => 'datetime:H:i:s',
        'allow_remote_clock' => 'boolean',
        'max_distance_meters' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec l'entreprise
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    /**
     * Relation avec la branche/succursale
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    /**
     * Relation avec le manager
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'manager_id');
    }

    /**
     * Scope pour les emplacements actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les emplacements d'une entreprise
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope pour les emplacements avec pointeuse
     */
    public function scopeWithTimeClock($query)
    {
        return $query->where('has_time_clock', true);
    }

    /**
     * Scope par type d'emplacement
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Obtenir l'adresse complète
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code
        ]);

        return implode(', ', $parts);
    }

    /**
     * Vérifier si l'emplacement a une pointeuse
     */
    public function hasTimeClock(): bool
    {
        return $this->has_time_clock;
    }

    /**
     * Obtenir les horaires de check-in
     */
    public function getCheckInScheduleAttribute(): array
    {
        return [
            'start' => $this->check_in_start,
            'end' => $this->check_in_end,
        ];
    }

    /**
     * Obtenir les horaires de check-out
     */
    public function getCheckOutScheduleAttribute(): array
    {
        return [
            'start' => $this->check_out_start,
            'end' => $this->check_out_end,
        ];
    }
}
