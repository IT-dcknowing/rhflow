<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Contracts\Models\Contract;
use Modules\Employees\Models\Employee;
use Modules\Contracts\Models\ContractType;
use App\Models\Company;
use Modules\Contracts\Models\ContractAttechements;
use Modules\Contracts\Models\ContractAvenant;

class Contract extends Model
{
    use HasFactory;


    protected $table = 'contracts';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'subject',
		'employee_id', 
		'value',	
		'type_id',	
        'duration',
		'start_date',
		'end_date',	
		'notes',	
		'status',	
		'description',	
		'contract_description',	
		'employee_signature',	
		'company_signature',	
		'company_id',
        'created_by',
        'updated_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function type()
    {
        return $this->belongsTo(ContractType::class);
    }

    public function attachments()
    {
        return $this->hasMany(ContractAttechements::class);
    }

    public function avenants()
    {
        return $this->hasMany(ContractAvenant::class);
    }
}
