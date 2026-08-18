<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    protected $fillable = [
        'name',
        'alias',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec les plans (packs) via la table pivot
     */
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'pack_modules')->withPivot('is_active')->withTimestamps();
    }

    /**
     * Vérifier si le module est actif pour un plan donné
     */
    public function isActiveForPlan(Plan $plan): bool
    {
        return $this->plans()->wherePivot('plan_id', $plan->id)->wherePivot('is_active', true)->exists();
    }
}
