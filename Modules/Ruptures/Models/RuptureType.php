<?php

namespace Modules\Ruptures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company;

class RuptureType extends Model
{
    use HasFactory;

    protected $table = 'rupture_types';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'company_id',
    ];

    public function ruptures()
    {
        return $this->hasMany(Rupture::class);
    }    
    public function company(){
        return $this->belongsTo(Company::class);
    }
}