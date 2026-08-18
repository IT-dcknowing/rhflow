<?php

namespace Modules\PaieSalaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company;
use Modules\PaieSalaries\Models\PaiePeriode;

class SetSalarie extends Model
{
    use HasFactory;

    protected $table = 'set_salaries';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'periode_id',
        'rubriques',
        'amount_total',
        'month_paie',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function periode()
    {
        return $this->belongsTo(PaiePeriode::class, 'periode_id');
    }
}
