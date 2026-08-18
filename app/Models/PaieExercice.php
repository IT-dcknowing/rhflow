<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use App\Models\User;

class PaieExercice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'code',
        'date_debut',
        'date_fin',
        'years',
        'statut',
        'description',
        'company_id',
        'created_by'
    ];

    protected $dates = [
        'date_debut',
        'date_fin',
        'deleted_at'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Entreprise à laquelle appartient l'exercice
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Utilisateur qui a créé l'exercice
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Périodes de paie de l'exercice
     */
    public function periodes(): HasMany
    {
        return $this->hasMany(PaiePeriode::class, 'exercice_id', 'id');
    }

    /**
     * Vérifie si l'exercice est en cours
     */
    public function isEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }

    /**
     * Vérifie si l'exercice est clôturé
     */
    public function isCloture(): bool
    {
        return $this->statut === 'cloture';
    }

    /**
     * Vérifie si une date est dans l'exercice
     */
    public function contientDate(\DateTime $date): bool
    {
        return $date >= $this->date_debut && $date <= $this->date_fin;
    }

    /**
     * Génère un code unique pour l'exercice
     */
    public static function genererCode(): string
    {
        do {
            $code = 'EX-' . date('Y') . '-' . strtoupper(\Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
