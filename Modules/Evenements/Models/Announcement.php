<?php

namespace Modules\Evenements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Employees\Models\Employee;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'branch_id',
        'department_id',
        'description',
        'company_id'
    ];

    protected $dates = ['start_date', 'end_date', 'deleted_at'];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'announcement_employee')
            ->withTimestamps()
            ->withPivot('company_id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
