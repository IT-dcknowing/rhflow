<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentType extends Model
{
    use HasFactory;

    protected $table = 'payment_types';
    
    protected $fillable = [
        'name',
        'is_active',
        'company_id',
        'created_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
