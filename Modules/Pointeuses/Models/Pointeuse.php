<?php

namespace Modules\Pointeuses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Pointeuses\Database\Factories\PointeuseFactory;

class Pointeuse extends Model
{
    use HasFactory;

    protected $table = 'pointeuses';

    /** 
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'auth_date_time',
        'auth_date',
        'auth_time',
        'type',
        'event_out',
        'direction',
        'name_device',
        'no_device',
        'emp_id',
        'name_emp',
        'card_no',
        'location_id',
        'status',
        'company_id'
    ];
}
