<?php

namespace Modules\Employees\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company;

class Family extends Model
{
    use HasFactory;

    protected $table = 'familys';

    protected $fillable = [
        'employee_id',
        'employee_day_id',
        'nom',
        'prenoms',
        'cmu',
        'num_cmu',
        'date_naiss_membre',
        'genre_membre',
        'type_membre', 
        'document',
        'company_id'
    ];

    public function employeeMonth(){
        return $this->belongsTo(Employee::class);
    }
    
    public function company(){
        return $this->belongsTo(Company::class);
    }
}
