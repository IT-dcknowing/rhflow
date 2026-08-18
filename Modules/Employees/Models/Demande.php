<?php

namespace Modules\Employees\Models;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use App\Models\PaiePeriode;

class Demande extends Model
{
    protected $table = 'demandes';  

    protected $fillable = [  
        'employee_id',	
        'periode_id',	
        'categorie_demandes',	
        'demande_types',	
        'montant',	
        'file_path',	
        'start_date',	
        'end_date',
        'demande_reason',	
        'status',	
        'company_id'
    ];	

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function periode()
    {
        return $this->belongsTo(PaiePeriode::class);
    }
}