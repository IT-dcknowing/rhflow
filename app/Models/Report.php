<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'parameters',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'parameters' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur qui a créé le rapport
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope pour les rapports actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Obtenir les types de rapports disponibles
     */
    public static function getAvailableTypes()
    {
        return [
            'users' => 'Rapport utilisateurs',
            'companies' => 'Rapport entreprises',
            'plans' => 'Rapport abonnements',
            'revenue' => 'Rapport revenus',
            'activity' => 'Rapport activité',
            'custom' => 'Rapport personnalisé'
        ];
    }
}
