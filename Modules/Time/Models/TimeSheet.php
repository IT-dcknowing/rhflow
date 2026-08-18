<?php

namespace Modules\Time\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employees\Models\Employee;
use App\Models\PaiePeriode;

class TimeSheet extends Model
{
    use HasFactory;

    protected $table = 'time_sheets';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
		'periode_id',
		'date',	
		'arrival_date',
		'hours',	
		'type_permis',
		'motif_justify',
		'remark',	
		'retenue',	
		'deduc_abs',	
		'approbation',	
		'statut',	
		'document',	
		'monthpaie',
		'company_id'
    ];

	public function employee(){
		return $this->belongsTo(Employee::class);
	}
	
	public function periode(){
		return $this->belongsTo(PaiePeriode::class);
	}
}
