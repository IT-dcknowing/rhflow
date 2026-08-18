<?php

namespace Modules\Evenements\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Modules\Employees\Models\Employee;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'event_type_id',
        'title',
        'start_date',
        'end_date',
        'color',
        'description',
        'company_id',
        'send_reminder',
        'reminder_minutes_before',
        'google_calendar_event_id',
        'outlook_event_id',
        'ical_uid',
        'status',
        'location',
        'is_private',
        'recurrence_rule',
        'recurrence_until'
    ];

    protected $dates = [
        'start_date', 
        'end_date', 
        'deleted_at',
        'recurrence_until',
        'reminder_sent_at'
    ];
    
    protected $casts = [
        'send_reminder' => 'boolean',
        'is_private' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Relations
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'event_employees')
            ->withTimestamps()
            ->withPivot('company_id');
    }
    
    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }
    
    public function type()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
    
    // Scopes
    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('start_date', '>=', now())
                    ->where('start_date', '<=', now()->addDays($days))
                    ->orderBy('start_date');
    }
    
    public function scopeForCalendar($query, $start, $end)
    {
        return $query->where(function($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
              ->orWhereBetween('end_date', [$start, $end])
              ->orWhere(function($q) use ($start, $end) {
                  $q->where('start_date', '<=', $start)
                    ->where('end_date', '>=', $end);
              });
        })->where('status', 'published');
    }
    
    // Accessors & Mutators
    public function getDurationAttribute()
    {
        if (!$this->end_date) {
            return null;
        }
        
        return $this->start_date->diffForHumans($this->end_date, true);
    }
    
    public function getIsRecurringAttribute()
    {
        return !empty($this->recurrence_rule);
    }
    
    // Méthodes
    public function isUpcoming()
    {
        return $this->start_date->isFuture();
    }
    
    public function isHappeningNow()
    {
        $now = now();
        return $this->start_date->lte($now) && 
               (!$this->end_date || $this->end_date->gte($now));
    }
    
    public function needsReminder()
    {
        if (!$this->send_reminder || $this->reminder_sent_at || !$this->reminder_minutes_before) {
            return false;
        }
        
        $reminderTime = $this->start_date->subMinutes($this->reminder_minutes_before);
        return now() >= $reminderTime && now() <= $this->start_date;
    }
    
    public function markReminderSent()
    {
        $this->reminder_sent_at = now();
        $this->save();
    }
    
    public function getCalendarEventData()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start_date->toIso8601String(),
            'end' => $this->end_date ? $this->end_date->toIso8601String() : null,
            'allDay' => !$this->end_date || $this->start_date->diffInHours($this->end_date) > 24,
            'color' => $this->color,
            'textColor' => $this->getTextColor(),
            'extendedProps' => [
                'type_id' => $this->event_type_id,
                'location' => $this->location,
                'description' => $this->description,
                'status' => $this->status,
                'isPrivate' => $this->is_private,
            ]
        ];
    }
    
    protected function getTextColor()
    {
        // Déterminer une couleur de texte lisible en fonction de la couleur de fond
        if (empty($this->color)) {
            return '#000000';
        }
        
        $hex = str_replace('#', '', $this->color);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }
}
