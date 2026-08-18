<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'order_number',
        'status',
        'amount',
        'currency',
        'coupon_id',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'payment_method',
        'payment_reference',
        'notes',
        'paid_at',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'applicable_plans' => 'array',
        'restrictions' => 'array',
    ];

    /**
     * Générer un numéro de commande unique
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Relation avec le coupon
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Vérifier si la commande est en attente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Vérifier si la commande est payée
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Vérifier si la commande est annulée
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Vérifier si la commande est expirée
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
            ($this->expires_at && $this->expires_at->isPast());
    }

    /**
     * Marquer la commande comme payée
     */
    public function markAsPaid(string $paymentMethod = null, string $paymentReference = null): void
    {
        $this->update([
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
            'paid_at' => now(),
        ]);
    }

    /**
     * Annuler la commande
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason ? ($this->notes ? $this->notes . "\nAnnulée: " . $reason : "Annulée: " . $reason) : $this->notes,
        ]);
    }

    /**
     * Obtenir le statut avec label lisible
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'paid' => 'Payée',
            'cancelled' => 'Annulée',
            'refunded' => 'Remboursée',
            'expired' => 'Expirée',
            default => 'Inconnu',
        };
    }

    /**
     * Obtenir la couleur du statut pour l'affichage
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'paid' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'info',
            'expired' => 'secondary',
            default => 'primary',
        };
    }

    /**
     * Calculer le montant après réduction
     */
    public function calculateDiscountedAmount(): float
    {
        return $this->amount - $this->discount_amount;
    }

    /**
     * Calculer le montant total avec taxes
     */
    public function calculateTotalAmount(): float
    {
        return $this->calculateDiscountedAmount() + $this->tax_amount;
    }
}
