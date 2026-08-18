<?php

namespace Modules\Evenements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AwardType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'company_id'
    ];

    protected $dates = ['deleted_at'];

    public function awards()
    {
        return $this->hasMany(Award::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
