<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Contracts\Models\Contract;
use Modules\Contracts\Models\Employee;
use App\Models\Company;

class ContractAvenant extends Model
{
    use HasFactory;

    protected $table = 'contract_avenants';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'contract_id',
		'employee_id',
		'type_avenant',
        'duration',
		'amount',
		'description',
		'company_id',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
