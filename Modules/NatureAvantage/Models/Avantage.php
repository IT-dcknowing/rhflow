<?php

namespace Modules\NatureAvantage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company;
use App\Models\Branch;
use Modules\Employees\Models\Employee;
use App\Models\PaiePeriode;

class Avantage extends Model
{
    use HasFactory;

    protected $table = 'avantages';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
		'branche_id',
        'periode_id',
		'type_avantage',
		'libelle',
		'amount_reel',
		'amount',     
		'details',
		'taxe_its',
		'taxe_cnps',
		'traitement',
		'status',
        'is_active',
		'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function branche()
    {
        return $this->belongsTo(Branch::class);
    }

    public function periode()
    {
        return $this->belongsTo(PaiePeriode::class);
    }
}