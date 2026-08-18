<?php

namespace Modules\Time\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employees\Models\Employee;
use App\Models\PaiePeriode;
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
        'periode_id',
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

    public function employee(){
		return $this->belongsTo(Employee::class);
	}
	
	public function periode(){
		return $this->belongsTo(PaiePeriode::class);
	}
}
