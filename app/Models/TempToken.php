<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;

class TempToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'token',	
        'expires_at',
        'company_id'
    ];

    /**
     * Relation avec l'entreprise (si l'utilisateur est lié à une entreprise)
    */
    public function company()
    {
        return $this->hasOne(Company::class, 'company_id');
    }
}
