<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaritalStatus extends Model
{
    use HasFactory;

    protected $table = 'marital_statuses';

    protected $fillable = [
        'name',
        'code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Relations
     */
    public function employees()
    {
        return $this->hasMany(\Modules\Employees\Models\Employee::class, 'martalstatu_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
