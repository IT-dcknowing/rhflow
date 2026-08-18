<?php

namespace Modules\Evenements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Employees\Models\Employee;

class Promotion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'designation_id',
        'promotion_title',
        'promotion_date',
        'description',
        'status',
        'company_id'
    ];

    protected $dates = ['promotion_date', 'deleted_at'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function designation()
    {
        return $this->belongsTo(\App\Models\Designation::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
