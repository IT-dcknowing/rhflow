<?php

namespace Modules\Time\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employees\Models\Employee;
use App\Models\PaiePeriode;

class Overtime extends Model
{
    use HasFactory;

    protected $table = 'overtimes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'periode_id',
        'start_date',
        'end_date',
        'quar_heure',
        'heure_audd',
        'heure_nuit_ferie',
        'heure_dim_ferie',
        'heure_nuit_dim_ferie',
        'taux_hour',
        'montant',
        'statut',
        'paid',
        'remark',
        'company_id'
    ];

    public function employee(){
		return $this->belongsTo(Employee::class);
	}
	
	public function periode(){
		return $this->belongsTo(PaiePeriode::class);
	}

}
