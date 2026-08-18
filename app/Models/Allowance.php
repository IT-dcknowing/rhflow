<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\PaiePeriode;
use App\Models\AllowanceOption;

class Allowance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nom de la table
     */
    protected $table = 'allowances';

    /**
     * Les attributs qui peuvent être assignés en masse
     */
    protected $fillable = [
        'code',
        'code_compta',
        'employee_id',
        'periode_id',
        'allowance_option_id',
        'title',
        'trait_fisc',
        'trait_cnps',
        'base_heures',
        'amount',
        'amount_imp',
        'montant',
        'jours_work',
        'jours_leave',
        'type',
        'type_amount',
        'details',
        'created_by', 
        'updated_by',
        'company_id',
        'month_paie',
        'is_active'
    ];

    /**
     * Les attributs qui doivent être convertis
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'amount_imp' => 'decimal:2',
        'montant' => 'decimal:2',
        'base_heures' => 'boolean',
        'type_amount' => 'boolean',
        'jours_work' => 'integer',
        'jours_leave' => 'integer',
        'code_compta' => 'integer',
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
     * Clé primaire personnalisée
     */
    protected $primaryKey = 'id';

    /**
     * Relation avec l'entreprise (Company)
     * Une allocation appartient à une entreprise
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function allowances()
    {
        return $this->hasMany(Allowance::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'employé (Employee)
     * Une allocation appartient à un employé
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(\Modules\Employees\Models\Employee::class, 'employee_id');
    }

    /**
     * Relation avec l'option d'allocation (AllowanceOption)
     * Une allocation appartient à une option d'allocation
     */
    public function allowanceOption(): BelongsTo
    {
        return $this->belongsTo(AllowanceOption::class);
    }

    /**
     * Scope pour filtrer par entreprise
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope pour filtrer par employé
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour filtrer par code
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Scope pour les allocations actives (non supprimées)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope pour les allocations de type fixed
     */
    public function scopeFixed($query)
    {
        return $query->where('type', 'fixed');
    }

    /**
     * Scope pour les allocations de type variable
     */
    public function scopeVariable($query)
    {
        return $query->where('type', 'variable');
    }

    /**
     * Mutateur pour le code (générer automatiquement si vide)
     */
    public function setCodeAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['code'] = 'ALL-' . time() . '-' . rand(1000, 9999);
        } else {
            $this->attributes['code'] = $value;
        }
    }

    /**
     * Accesseur pour le montant formaté
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Accesseur pour le montant imposable formaté
     */
    public function getFormattedAmountImpAttribute()
    {
        return number_format($this->amount_imp, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Accesseur pour le montant total formaté
     */
    public function getFormattedMontantAttribute()
    {
        return number_format($this->montant, 0, ',', ' ') . ' FCFA';
    }

    public function periode()
    {
        return $this->belongsTo(PaiePeriode::class);
    }
}
