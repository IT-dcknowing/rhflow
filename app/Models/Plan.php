<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'price_yearly',
        'popular',
        'enterprise',
        'duration',
        'max_users',
        'max_employees',
        'storage_limit',
        'nbre_trait',
        'enable_chatgpt',
        'description',
        'image',
        'features',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'popular' => 'boolean',
        'enterprise' => 'boolean',
        'max_users' => 'integer',
        'max_employees' => 'integer',
        'storage_limit' => 'decimal:2',
        'nbre_trait' => 'integer',
        'enable_chatgpt' => 'boolean',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec les entreprises utilisant ce plan
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'plan_id');
    }

    /**
     * Obtenir le nombre d'entreprises utilisant ce plan
     */
    public function getCompanyCountAttribute(): int
    {
        return $this->companies()->count();
    }

    /**
     * Obtenir le nombre d'entreprises actives utilisant ce plan
     */
    public function getActiveCompanyCountAttribute(): int
    {
        return $this->companies()->active()->count();
    }

    /**
     * Obtenir le prix formaté selon la devise de l'utilisateur
     */
    public function getFormattedPriceAttribute(): string
    {
        // Par défaut, utiliser FCFA si pas d'utilisateur connecté
        $currency = 'XOF';

        if (auth()->check()) {
            $currencyCode = auth()->user()->attributes['currency'] ?? 'XOF';
            $currencies = auth()->user()->getAvailableCurrencies();
            $currencyInfo = $currencies[$currencyCode] ?? $currencies['XOF'];

            if ($currencyInfo['position'] === 'after') {
                return number_format($this->price, 0, ',', ' ') . ' ' . $currencyInfo['symbol'];
            } else {
                return $currencyInfo['symbol'] . ' ' . number_format($this->price, 2, ',', ' ');
            }
        }

        return number_format($this->price, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtenir le prix formaté pour une durée spécifique (multiplié par un nombre de mois)
     */
    public function getFormattedPriceForDuration(int $months): string
    {
        $amount = $this->price * $months;
        $currency = 'XOF';

        if (auth()->check()) {
            $currencyCode = auth()->user()->attributes['currency'] ?? 'XOF';
            $currencies = auth()->user()->getAvailableCurrencies();
            $currencyInfo = $currencies[$currencyCode] ?? $currencies['XOF'];

            if ($currencyInfo['position'] === 'after') {
                return number_format($amount, 0, ',', ' ') . ' ' . $currencyInfo['symbol'];
            } else {
                return $currencyInfo['symbol'] . ' ' . number_format($amount, 2, ',', ' ');
            }
        }

        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Formater le prix avec une devise spécifique
     */
    public function formatPriceWithCurrency(string $currencyCode = null): string
    {
        $amount = $this->price;

        if ($currencyCode) {
            $currencies = [
                'XOF' => ['symbol' => 'FCFA', 'position' => 'after'],
                'EUR' => ['symbol' => '€', 'position' => 'after'],
                'USD' => ['symbol' => '$', 'position' => 'before'],
                'GBP' => ['symbol' => '£', 'position' => 'before'],
            ];

            $currency = $currencies[$currencyCode] ?? $currencies['XOF'];

            if ($currency['position'] === 'after') {
                return number_format($amount, 0, ',', ' ') . ' ' . $currency['symbol'];
            } else {
                return $currency['symbol'] . ' ' . number_format($amount, 2, ',', ' ');
            }
        }

        return $this->formatted_price;
    }

    /**
     * Vérifier si le plan permet ChatGPT
     */
    public function hasChatGPT(): bool
    {
        return $this->enable_chatgpt;
    }

    /**
     * Obtenir les fonctionnalités sous forme de liste (basé sur la description)
     */
    public function getFeaturesListAttribute(): array
    {
        if (!$this->features) {
            return [];
        }

        // Si c'est déjà un tableau, le retourner directement
        if (is_array($this->features)) {
            return $this->features;
        }

        // Sinon, exploser la chaîne par les retours à la ligne
        return explode('\n', $this->features);
    }

    /**
     * Vérifier si le plan supporte un nombre d'utilisateurs donné
     */
    public function supportsUserCount(int $count): bool
    {
        return $this->max_users === 0 || $count <= $this->max_users;
    }

    /**
     * Vérifier si le plan supporte un nombre d'employés donné
     */
    public function supportsEmployeeCount(int $count): bool
    {
        return $this->max_employees === 0 || $count <= $this->max_employees;
    }

    /**
     * Obtenir le prix mensuel (si le prix stocké est annuel, le diviser par 12)
     */
    public function getPriceMonthly(): float
    {
        // Si le prix est déjà mensuel ou si la durée est de 1 mois
        if ($this->duration <= 1) {
            return $this->price;
        }

        // Si le prix est annuel, calculer le prix mensuel
        return round($this->price / 12, 2);
    }

    /**
     * Obtenir le prix annuel (si le prix stocké est mensuel, le multiplier par 12)
     */
    public function getPriceYearly(): float
    {
        // Si le prix est déjà annuel ou si la durée est de 12 mois
        if ($this->duration >= 12) {
            return $this->price;
        }

        // Si le prix est mensuel, calculer le prix annuel
        return round($this->price * 12, 2);
    }

    /**
     * Scope pour les plans actifs
     */
    public function scopeActive($query)
    {
        return $query->where('id', '>', 0); // Tous les plans sont actifs par défaut
    }

    /**
     * Scope pour trier par prix
     */
    public function scopeOrderedByPrice($query)
    {
        return $query->orderBy('price');
    }

    /**
     * Scope pour les plans avec ChatGPT
     */
    public function scopeWithChatGPT($query)
    {
        return $query->where('enable_chatgpt', true);
    }

    /**
     * Relation avec les commandes utilisant ce plan
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relation avec les modules via la table pivot
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'pack_modules')->withPivot('is_active')->withTimestamps();
    }

    /**
     * Obtenir les modules actifs pour ce plan
     */
    public function activeModules()
    {
        return $this->modules()->wherePivot('is_active', true);
    }
}
