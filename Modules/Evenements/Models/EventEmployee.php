<?php

namespace Modules\Evenements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventEmployee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_id', 
        'employee_id', 
        'company_id', 
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Get the event that owns the event employee.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the employee that owns the event employee.
     */
    public function employee()
    {
        return $this->belongsTo(\Modules\Employees\Models\Employee::class);
    }

    /**
     * Get the company that owns the event employee.
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
