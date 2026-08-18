<?php

namespace Modules\PaieSalaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeRetenue extends Model
{
    use HasFactory;

    protected $table = 'type_retenues';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'libelle',
        'type',
		'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function retenues()
    {
        return $this->hasMany(Retenue::class);
    }
}
