<?php

namespace Modules\Employees\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $table = 'employee_documents';

    protected $fillable = [
        'employee_id',
        'libelle',
        'document_value',
        'company_id', 
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function employeeDay(){
        return $this->belongsTo(EmployeeDay::class);
    }

    public function compagnies(){
        return $this->belongsTo(Company::class, 'company_id');
    }
}
