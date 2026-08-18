<?php

namespace Modules\Loans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company;
use App\Models\PaiePeriode;

class LoanPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'periode_id',
        'payment_date',
        'amount',
        'note',
        'company_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'float',
    ];

    /**
     * Relation avec le prêt
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Relation avec l'entreprise
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation avec la periode
     */
    public function periode()    
    {
        return $this->belongsTo(PaiePeriode::class);
    }
}