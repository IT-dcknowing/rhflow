<?php

namespace Modules\Loans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company;
use App\Models\Branch;
use App\Models\PaieExercice;
use App\Models\PaiePeriode;
use Modules\Employees\Models\Employee;
use Modules\Settings\Models\LoanType;
use Modules\Loans\Models\LoanPayment;

class Loan extends Model
{
    use HasFactory;

    protected $table = 'loans';

    /**  
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
		'branche_id',
		'loan_option',
        'periode_id',
		'title',
		'amount',
		'amount_deduc',
		'amountpaie',
		'type',
		'deduc_loan',
		'nbre_mois',
		'start_date',
		'end_date',
		'reason',
		'statut',
		'month_paie',
        'is_active',
        'prochaine_echeance',
		'company_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function loanOption()
    {
        return $this->belongsTo(LoanType::class, 'loan_option');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branche_id');
    }

    public function periode()
    {
        return $this->belongsTo(PaiePeriode::class);
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class, 'loan_id');
    }
}