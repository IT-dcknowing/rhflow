<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Contracts\Models\Contract;
use App\Models\Company;

class ContractAttechements extends Model
{
    use HasFactory;


    protected $table = 'contract_attechments';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'contract_id',
        'employee_id',
        'files',
        'file_name',
        'company_id',
        'uploaded_by',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
