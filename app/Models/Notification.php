<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'icon',
        'color',
        'data',
        'is_read',
        'read_at',
        'user_id',
        'sender_id',
        'source_type',
        'source_id',
        'action_url',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur destinataire
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'utilisateur émetteur
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relation avec les notifications de l'utilisateur (relation inverse)
     */
    public function notifications()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope pour les notifications non lues
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope pour les notifications lues
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope pour les notifications non expirées
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Marquer la notification comme lue
     */
    public function markAsRead(): bool
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
            return true;
        }
        return false;
    }

    /**
     * Marquer la notification comme non lue
     */
    public function markAsUnread(): bool
    {
        if ($this->is_read) {
            $this->update([
                'is_read' => false,
                'read_at' => null,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Vérifier si la notification est expirée
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Obtenir le nom de la classe source polymorphe
     */
    public function getSourceClassAttribute(): ?string
    {
        if ($this->source_type) {
            return $this->source_type;
        }
        return null;
    }

    /**
     * Obtenir l'instance source polymorphe
     */
    public function getSourceAttribute()
    {
        if ($this->source_type && $this->source_id) {
            return $this->source_type::find($this->source_id);
        }
        return null;
    }
}
