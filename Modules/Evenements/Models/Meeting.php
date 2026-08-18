<?php

namespace Modules\Evenements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Employees\Models\Employee;

class Meeting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'title',
        'date',
        'time',
        'note',
        'company_id',
        'status'
    ];

    protected $dates = ['date', 'deleted_at'];
    protected $casts = [
        'time' => 'datetime:H:i',
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'meeting_employee')
            ->withTimestamps()
            ->withPivot('company_id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
