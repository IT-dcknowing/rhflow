<?php

namespace Modules\PaieSalaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PaySlip extends Model
{
    use HasFactory;

    protected $table = 'pay_slips';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
		'exercice_id',
        'employee_id',
		'periode_id',
		'net_payble',
		'salary_month',
		'status',
		'salary_brut',
		'net_imposable',
		'net_sociale',
		'basic_salary',
		'total_retenue',
		'total_patronale',
		'allowances',
		'retenues',
		'avtg_real',
		'avtg_real2',
		'avtg_bareme',
		'pay_type',
		'nbre_jour',
		'address_emp',
		'situation_emp',
		'enfant_emp',
		'num_cnps_emp',
		'anciennete_emp',
		'categories_emp',
		'emploi',
		'phone_emp',
		'parts_emp',
		'nom_etp',
		'adresse_etp',
		'phone_etp',
		'btp_etp',
		'code',
		'company_id', 
    ];

	public function employee(){
        return $this->belongsTo(\Modules\Employees\Models\Employee::class, 'employee_id');
    }

    public function periode(){
		return $this->belongsTo(\App\Models\PaiePeriode::class, 'periode_id');
	}

	public function exercice(){
		return $this->belongsTo(\App\Models\PaieExercice::class, 'exercice_id');
	}
}
