<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PaieExercice;
use App\Models\Company;
use App\Models\User;
use Modules\PaieSalaries\Models\PaySlip;

class PaiePeriode extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'code',
        'exercice_id',
        'date_debut',
        'date_fin',
        'date_paiement',
        'type_periode',
        'num_monthly',
        'statut',
        'notes',
        'company_id',
        'created_by'
    ];

    protected $dates = [
        'date_debut',
        'date_fin',
        'date_paiement',
        'deleted_at'
    ];

    protected $casts = [
        'date_debut' => 'date:Y-m-d',
        'date_fin' => 'date:Y-m-d',
        'date_paiement' => 'date:Y-m-d',
    ];

    /**
     * Exercice auquel appartient la période
     */
    public function exercice(): BelongsTo
    {
        return $this->belongsTo(PaieExercice::class, 'exercice_id');
    }

    /**
     * Entreprise à laquelle appartient la période
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Utilisateur qui a créé la période
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Bulletins de paie de la période
     */
    public function bulletins(): HasMany
    {
        return $this->hasMany(PaySlip::class, 'periode_id');
    }

    /**
     * Vérifie si la période est en cours
     */
    public function isEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }

    /**
     * Vérifie si la période est validée
     */
    public function isValidee(): bool
    {
        return $this->statut === 'validee';
    }

    /**
     * Vérifie si la période est payée
     */
    public function isPayee(): bool
    {
        return $this->statut === 'payee';
    }

    /**
     * Vérifie si la période est annulée
     */
    public function isAnnulee(): bool
    {
        return $this->statut === 'annulee';
    }

    /**
     * Génère un code unique pour la période
     */
    public static function genererCode(PaieExercice $exercice, string $typePeriode): string
    {
        $prefix = '';
        
        switch ($typePeriode) {
            case 'mensuelle':
                $prefix = 'M';
                break;
            case 'quinzaine':
                $prefix = 'Q';
                break;
            case 'hebdomadaire':
                $prefix = 'H';
                break;
            default:
                $prefix = 'A';
        }

        $count = self::where('exercice_id', $exercice->id)
            ->where('type_periode', $typePeriode)
            ->withTrashed()
            ->count() + 1;

        return $exercice->code . '-' . $prefix . str_pad($count, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Vérifie si une date est dans la période
     */
    public function contientDate(\DateTime $date): bool
    {
        return $date >= $this->date_debut && $date <= $this->date_fin;
    }

    public function periode()
    {
        return $this->belongsTo(PaiePeriode::class, 'periode_id');
    }

    /**
     * Accesseur pour obtenir le nombre de bulletins
     */
    public function getBulletinsCountAttribute()
    {
        return $this->bulletins()->count();
    }

    /**
     * Accesseur pour obtenir la somme des salaires bruts
     */
    public function getBulletinsSumBrutAttribute()
    {
        return $this->bulletins()->sum('salary_brut');
    }

    /**
     * Accesseur pour obtenir la somme des salaires nets
     */
    public function getBulletinsSumNetAttribute()
    {
        return $this->bulletins()->sum('net_payble');
    }
}
