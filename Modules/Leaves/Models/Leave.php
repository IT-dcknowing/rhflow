<?php

namespace Modules\Leaves\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company;
use Modules\Employees\Models\Employee;
use App\Models\PaiePeriode;
use Modules\Leaves\Models\LeaveType;

class Leave extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
		'leave_type_id',
        'periode_id',
		'applied_on',
		'leave_back',
		'start_date',
		'end_date',
		'total_leave_days',
		'amount_leave',
		'amount_leave_net',
		'leave_sit',
		'leave_reason',
		'month_leave',
		'sb_leave',
		'days_leave',
		'remark',
		'status',
		'leave_statut',
		'company_id',
        'created_by',
        'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function periode(){
        return $this->belongsTo(PaiePeriode::class);
    }

}
