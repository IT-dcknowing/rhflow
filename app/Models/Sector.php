<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // Générer automatiquement le slug à partir du nom
        static::saving(function ($sector) {
            if ($sector->isDirty('name') && !$sector->slug) {
                $sector->slug = Str::slug($sector->name);
            }
        });
    }

    /**
     * Scope pour les secteurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour trier par ordre
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Méthode statique pour trier par ordre
     */
    public static function ordered()
    {
        return static::query()->ordered();
    }

    /**
     * Relation avec les entreprises (si elles utilisent ce secteur)
     */
    public function companies()
    {
        return $this->hasMany(Company::class, 'industry', 'slug');
    }

    /**
     * Obtenir le nombre d'entreprises dans ce secteur
     */
    public function getCompaniesCountAttribute()
    {
        return Company::where('industry', $this->slug)->count();
    }

    public function getJobCategorieAttribute()
    {
        return JobCategorie::where('id_secteur', $this->id)->get();
    }
}
