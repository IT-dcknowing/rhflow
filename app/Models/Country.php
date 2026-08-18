<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'phone_code',
        'flag',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope pour les pays actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour trier par ordre alphabétique
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Relation avec les entreprises de ce pays
     */
    public function companies()
    {
        return $this->hasMany(User::class, 'country', 'code');
    }

    /**
     * Obtenir le nombre d'entreprises dans ce pays
     */
    public function getCompaniesCountAttribute()
    {
        return $this->companies()->count();
    }

    /**
     * Obtenir le nom complet avec le drapeau
     */
    public function getFullNameAttribute()
    {
        $flag = $this->flag ? $this->flag . ' ' : '';
        return $flag . $this->name;
    }

    /**
     * Recherche par nom ou code
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
    }
}
