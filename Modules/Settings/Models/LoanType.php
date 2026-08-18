<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Company;

class LoanType extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'max_amount',
        'interest_rate',
        'repayment_period_max',
        'repayment_period_min',
        'requires_guarantor',
        'guarantor_conditions',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'max_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'requires_guarantor' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relation avec l'entreprise
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation avec l'utilisateur qui a créé
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur qui a modifié
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope pour les types actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les types de l'entreprise
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Obtenir le taux d'intérêt formaté
     */
    public function getFormattedInterestRateAttribute()
    {
        return number_format($this->interest_rate, 2) . '%';
    }

    /**
     * Obtenir le montant maximum formaté
     */
    public function getFormattedMaxAmountAttribute()
    {
        return number_format($this->max_amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtenir la période de remboursement formatée
     */
    public function getFormattedRepaymentPeriodAttribute()
    {
        if ($this->repayment_period_min == $this->repayment_period_max) {
            return $this->repayment_period_min . ' mois';
        }
        return $this->repayment_period_min . ' - ' . $this->repayment_period_max . ' mois';
    }
}
