<?php

namespace Modules\Loans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanOption extends Model
{
    use HasFactory;

    protected $table = 'loan_options';

    /**
     * The attributes that are mass assignable.  
     */
    protected $fillable = [
        'name',
        'is_active',
        'company_id'
    ];
}
