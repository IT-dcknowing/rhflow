<?php

namespace Modules\Employees\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company;

class EmployeeCmu extends Model
{
    use HasFactory;

    protected $table = 'employee_cmus';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'family_id',
        'name_cmu',
        'prenom_cmu',
        'date_naiss_cmu',
        'genre_cmu',
        'num_cmu', 
        'type_cmu',
        'company_id'
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function family(){
        return $this->belongsTo(Family::class);
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }
}
