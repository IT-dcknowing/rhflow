<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_amount',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active',
        'created_by',
        'applicable_plans',
        'restrictions',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'applicable_plans' => 'array',
        'restrictions' => 'array',
    ];

    /**
     * Générer un code de coupon unique
     */
    public static function generateCouponCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Relation avec l'utilisateur qui a créé le coupon
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec les commandes qui utilisent ce coupon
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Vérifier si le coupon est valide
     */
    public function isValid(): bool
    {
        return $this->is_active &&
               !$this->isExpired() &&
               !$this->hasReachedMaxUses();
    }

    /**
     * Vérifier si le coupon est expiré
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Vérifier si le coupon a atteint le nombre maximum d'utilisations
     */
    public function hasReachedMaxUses(): bool
    {
        return $this->max_uses > 0 && $this->used_count >= $this->max_uses;
    }

    /**
     * Vérifier si le coupon s'applique à un plan spécifique
     */
    public function appliesToPlan(int $planId): bool
    {
        // Si applicable_plans est null, le coupon s'applique à tous les plans
        if (!$this->applicable_plans) {
            return true;
        }

        return in_array($planId, $this->applicable_plans);
    }

    /**
     * Calculer la réduction pour un montant donné
     */
    public function calculateDiscount(float $amount, int $planId): float
    {
        // Vérifier si le coupon s'applique au plan
        if (!$this->appliesToPlan($planId)) {
            return 0;
        }

        // Vérifier le montant minimum
        if ($amount < $this->min_amount) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return min($amount * ($this->value / 100), $amount);
        } else {
            return min($this->value, $amount);
        }
    }

    /**
     * Appliquer le coupon à une commande
     */
    public function applyToOrder(float $amount, int $planId): array
    {
        if (!$this->isValid()) {
            return [
                'valid' => false,
                'discount' => 0,
                'error' => 'Coupon invalide ou expiré'
            ];
        }

        $discount = $this->calculateDiscount($amount, $planId);

        if ($discount > 0) {
            // Incrémenter le compteur d'utilisation
            $this->increment('used_count');
        }

        return [
            'valid' => $discount > 0,
            'discount' => $discount,
            'error' => $discount > 0 ? null : 'Coupon non applicable à cette commande'
        ];
    }

    /**
     * Obtenir le statut avec label lisible
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactif';
        }

        if ($this->isExpired()) {
            return 'Expiré';
        }

        if ($this->hasReachedMaxUses()) {
            return 'Épuisé';
        }

        return 'Actif';
    }

    /**
     * Obtenir la couleur du statut pour l'affichage
     */
    public function getStatusColorAttribute(): string
    {
        if (!$this->is_active) {
            return 'secondary';
        }

        if ($this->isExpired()) {
            return 'danger';
        }

        if ($this->hasReachedMaxUses()) {
            return 'warning';
        }

        return 'success';
    }

    /**
     * Obtenir le taux d'utilisation du coupon
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->max_uses === 0) {
            return 0;
        }

        return min(($this->used_count / $this->max_uses) * 100, 100);
    }

    /**
     * Scope pour les coupons actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    })
                    ->where(function ($q) {
                        $q->where('max_uses', 0)
                          ->orWhereColumn('used_count', '<', 'max_uses');
                    });
    }

    /**
     * Scope pour les coupons expirés
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope pour les coupons utilisés au maximum
     */
    public function scopeFullyUsed($query)
    {
        return $query->where('max_uses', '>', 0)
                    ->whereColumn('used_count', '>=', 'max_uses');
    }
}
