<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Contracts\Models\Contract;
use App\Models\Company;

class ContractType extends Model
{
    use HasFactory;

    protected $table = 'contract_types';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'names',
        'is_active',
        'type',
        'company_id',
    ];

    public function companie(){
        return $this->belongsTo(Company::class);
    }

    public function contracts(){
        return $this->hasMany(Contract::class, 'type_id');
    }
}
