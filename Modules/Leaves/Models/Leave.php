<?php

namespace Modules\Leaves\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company;
use Modules\Employees\Models\Employee;
use App\Models\PaiePeriode;
use Modules\Leaves\Models\LeaveType;

class Leave extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
		'leave_type_id',
        'periode_id',
		'applied_on',
		'leave_back',
		'start_date',
		'end_date',
		'total_leave_days',
		'amount_leave',
		'amount_leave_net',
		'leave_sit',
		'leave_reason',
		'month_leave',
		'sb_leave',
		'days_leave',
		'remark',
		'status',
		'leave_statut',
		'is_active',
		'allowance_id',
		'activated_periode_id',
		'activated_at',
		'company_id',
        'created_by',
        'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function periode(){
        return $this->belongsTo(PaiePeriode::class);
    }

    /**
     * Période de paie sur laquelle le congé est activé.
     */
    public function activatedPeriode()
    {
        return $this->belongsTo(PaiePeriode::class, 'activated_periode_id');
    }

    /**
     * Ligne "Allocation congé" générée dans la paie lors de l'activation.
     */
    public function allowance()
    {
        return $this->belongsTo(\App\Models\Allowance::class, 'allowance_id');
    }

    /**
     * Code unique de la ligne de paie liée à ce congé : sert de clé d'idempotence
     * pour ne jamais créer deux "Allocation congé" pour le même congé.
     */
    public function allowanceCode()
    {
        return 'CONGE-' . $this->id;
    }

    /**
     * Le congé peut-il être activé pour la paie ?
     */
    public function isActivable()
    {
        return in_array($this->status, ['Approuvé', 'Démarré', 'Terminé']);
    }

}
