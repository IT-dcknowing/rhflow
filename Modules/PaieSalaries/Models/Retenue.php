<?php

namespace Modules\PaieSalaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\PaieSalaries\Models\TypeRetenue;
use Modules\Employees\Models\Employee;
use App\Models\PaiePeriode;
use App\Models\Company;

class Retenue extends Model
{
    use HasFactory;

    protected $table = "retenues";
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "libelle",
        "type_retenue_id",
        "employee_id",
        "branche_id",
        "periode_id",
        "ordre",
        "patronale",
        "salariale",
        "code",
        "base",
        "taux",
        "amount",
        "jours_work",
        "date_application",
        "month_paie",
        "company_id",
        "is_active",
        "type",
    ];

    public function typeRetenue()
    {
        return $this->belongsTo(TypeRetenue::class, 'type_retenue_id', 'id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
 
    public function periode()
    { 
        return $this->belongsTo(PaiePeriode::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
