<?php

namespace Modules\Evenements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'color',
        'icon',
        'company_id',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    // Relations
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'event_type_id');
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId)
                    ->orWhereNull('company_id');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Méthodes
    public static function getDefaultType()
    {
        return static::default()->first() ?? new static([
            'name' => 'Général',
            'color' => '#3b82f6',
            'icon' => 'calendar',
            'is_default' => true
        ]);
    }
}
