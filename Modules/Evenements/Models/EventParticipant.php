<?php

namespace Modules\Evenements\Models;

use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    protected $fillable = [
        'event_id',
        'participant_id',
        'participant_type',
        'email',
        'name',
        'status',
        'response_notes',
        'responded_at',
        'calendar_event_id'
    ];

    protected $dates = [
        'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime'
    ];

    // Relations
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function participant()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $this->scopeWithStatus($query, 'pending');
    }

    public function scopeAccepted($query)
    {
        return $this->scopeWithStatus($query, 'accepted');
    }

    public function scopeDeclined($query)
    {
        return $this->scopeWithStatus($query, 'declined');
    }

    // Méthodes
    public function markAsAccepted($notes = null)
    {
        $this->update([
            'status' => 'accepted',
            'response_notes' => $notes,
            'responded_at' => now()
        ]);
    }

    public function markAsDeclined($notes = null)
    {
        $this->update([
            'status' => 'declined',
            'response_notes' => $notes,
            'responded_at' => now()
        ]);
    }

    public function markAsTentative($notes = null)
    {
        $this->update([
            'status' => 'tentative',
            'response_notes' => $notes,
            'responded_at' => now()
        ]);
    }

    public function hasResponded()
    {
        return $this->status !== 'pending' && $this->responded_at !== null;
    }
}
