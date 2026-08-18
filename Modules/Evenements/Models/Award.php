<?php

namespace Modules\Evenements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Employees\Models\Employee;

class Award extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'award_type_id',
        'date',
        'gift',
        'description',
        'company_id'
    ];

    protected $dates = ['date', 'deleted_at'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function awardType()
    {
        return $this->belongsTo(AwardType::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
