<?php

namespace Modules\Ruptures\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Contracts\Models\Contract;
use App\Models\PaiePeriode;
use Modules\Employees\Models\Employee;

class Rupture extends Model
{
    use HasFactory;
    
    protected $table = 'ruptures';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'rupture_type_id',
        'periode_id',
        'contract_id',
        'notice_date',
        'termination_date',
        'indem_comp',
        'indem_comp_cong',
        'imdem_prea',
        'indem_licence',
        'aggravation',
        'dom_inter',
        'amount_cnps',
        'amount_its',
        'amount_loan',
        'solde',
        'description',
        'status',
        'traiter',
        'company_id'
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
    
    public function ruptureType(){
        return $this->belongsTo(RuptureType::class);
    }

    public function periode(){
        return $this->belongsTo(PaiePeriode::class);
    }

    public function contract(){
        return $this->belongsTo(Contract::class, 'id');
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

}
