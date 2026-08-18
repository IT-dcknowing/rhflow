<?php

namespace Modules\Evenements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Employees\Models\Employee;

class Transfer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'branch_id',
        'department_id',
        'transfer_date',
        'transfer_branch_id',
        'transfer_department_id',
        'description',
        'company_id'
    ];

    protected $dates = ['transfer_date', 'deleted_at'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }

    public function transferBranch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'transfer_branch_id');
    }

    public function transferDepartment()
    {
        return $this->belongsTo(\App\Models\Department::class, 'transfer_department_id');
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
