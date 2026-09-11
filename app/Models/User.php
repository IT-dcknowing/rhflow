<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Company;
use App\Models\Order;
use Modules\Employees\Models\Employee;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * Les types d'utilisateurs disponibles
     */
    const TYPES = [
        'super_admin' => 'Super Administrateur',
        'company' => 'Entreprise',
        'hr' => 'Responsable RH',
        'paie' => 'Responsable Paie',
        'payroll' => 'Responsable Paie',
        'employee' => 'Employé'
    ];

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'email_verified_at',
        'password',
        'password_code',
        'type',
        'avatar',
        'lang',
        'plan',
        'plan_expire_date',
        'plan_cpte_trait',
        'requested_plan',
        'storage_limit',
        'last_login',
        'is_active',
        'created_by',
        'active_status',
        'dark_mode',
        'messenger_color',
        'colorone',
        'colortwo',
        'config_company',
        'attendance_type',
        'ip_serveur',
        'currency',
        'remember_token',
        'company_id',
    ];

    /**
     * Les attributs qui doivent être masqués pour la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'password_code',
        'remember_token',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'plan_expire_date' => 'date',
            'last_login' => 'datetime',
            'is_active' => 'boolean',
            'active_status' => 'boolean',
            'storage_limit' => 'decimal:2',
            'plan' => 'integer',
            'plan_cpte_trait' => 'integer',
            'requested_plan' => 'integer',
            'config_company' => 'integer',
            'currency' => 'string',
        ];
    }

    /**
     * Valeurs par défaut pour les attributs
     */
    protected $attributes = [
        'is_active' => true,
        'active_status' => 1,
    ];

    /**
     * Accesseur pour vérifier si l'utilisateur est super admin
     */
    protected function isSuperAdmin(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->type === 'super_admin',
        );
    }

    /**
     * Accesseur pour vérifier si l'utilisateur est une entreprise
     */
    protected function isCompany(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->type === 'company',
        );
    }

    /**
     * Accesseur pour vérifier si l'utilisateur est actif
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            // On lit les colonnes explicitement plutot que le $value transmis :
            // un acces en camelCase ($user->isActive) resout le meme accesseur mais
            // sans valeur, ce qui rendait false pour tout le monde.
            // property_exists() ne voit pas les attributs Eloquent (ce ne sont pas
            // des proprietes PHP) et renvoyait toujours false : l'accesseur
            // repondait alors true quoi qu'il y ait en base.
            get: fn () => (bool) ($this->attributes['is_active'] ?? false)
                && (bool) ($this->attributes['active_status'] ?? true),
        );
    }

    /**
     * Accesseur pour l'avatar avec URL complète
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->avatar) {
                    return asset('images/avatars/default.png');
                }

                // Si c'est déjà une URL complète
                if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                    return $this->avatar;
                }

                // Sinon, retourner le chemin relatif
                return asset('storage/avatars/' . $this->avatar);
            },
        );
    }

    /**
     * Mutateur pour le mot de passe avec hash automatique
     */
    // protected function password(): Attribute
    // {
    //     return Attribute::make(
    //         set: fn ($value) => bcrypt($value),
    //     );
    // }

    /**
     * Scope pour les utilisateurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les super administrateurs
     */
    public function scopeSuperAdmins($query)
    {
        return $query->where('type', 'super_admin');
    }

    /**
     * Scope pour les entreprises
     */
    public function scopeCompanies($query)
    {
        return $query->where('type', 'company');
    }
 
    /**
     * Scope pour les employés actifs
     */
    public function scopeEmployees($query)
    {
        return $query->where('type', 'employee');
    }

    /**
     * Scope pour les utilisateurs RH
     */
    public function scopeHRUsers($query)
    {
        return $query->whereIn('type', ['hr', 'paie', 'payroll']);
    }

    /**
     * Les devises disponibles dans l'application
     */
    public static function getAvailableCurrencies(): array
    {
        return [
            'XOF' => [
                'name' => 'Franc CFA (XOF)',
                'symbol' => 'FCFA',
                'position' => 'after', // Symbole après le montant
            ],
            'EUR' => [
                'name' => 'Euro',
                'symbol' => '€',
                'position' => 'after',
            ],
            'USD' => [
                'name' => 'Dollar américain',
                'symbol' => '$',
                'position' => 'before',
            ],
            'GBP' => [
                'name' => 'Livre sterling',
                'symbol' => '£',
                'position' => 'before',
            ],
            'CAD' => [
                'name' => 'Dollar canadien',
                'symbol' => 'C$',
                'position' => 'before',
            ],
            'CHF' => [
                'name' => 'Franc suisse',
                'symbol' => 'CHF',
                'position' => 'before',
            ],
            'JPY' => [
                'name' => 'Yen japonais',
                'symbol' => '¥',
                'position' => 'before',
            ],
            'CNY' => [
                'name' => 'Yuan chinois',
                'symbol' => '¥',
                'position' => 'before',
            ],
            'INR' => [
                'name' => 'Roupie indienne',
                'symbol' => '₹',
                'position' => 'before',
            ],
            'BRL' => [
                'name' => 'Réal brésilien',
                'symbol' => 'R$',
                'position' => 'before',
            ],
            'ZAR' => [
                'name' => 'Rand sud-africain',
                'symbol' => 'R',
                'position' => 'before',
            ],
            'EGP' => [
                'name' => 'Livre égyptienne',
                'symbol' => 'E£',
                'position' => 'before',
            ],
            'MAD' => [
                'name' => 'Dirham marocain',
                'symbol' => 'DH',
                'position' => 'after',
            ],
            'TND' => [
                'name' => 'Dinar tunisien',
                'symbol' => 'DT',
                'position' => 'after',
            ],
            'DZD' => [
                'name' => 'Dinar algérien',
                'symbol' => 'DA',
                'position' => 'after',
            ],
        ];
    }

    /**
     * Obtenir la devise de l'utilisateur avec ses détails
     */
    public function getCurrencyAttribute(): array
    {
        $currency = $this->attributes['currency'] ?? 'XOF';
        $currencies = self::getAvailableCurrencies();

        return $currencies[$currency] ?? $currencies['XOF'];
    }

    /**
     * Formater un prix selon la devise de l'utilisateur
     */
    public function formatPrice(float $amount, bool $includeSymbol = true): string
    {
        $currency = $this->currency;

        if ($includeSymbol) {
            if ($currency['position'] === 'after') {
                return number_format($amount, 0, ',', ' ') . ' ' . $currency['symbol'];
            } else {
                return $currency['symbol'] . ' ' . number_format($amount, 2, ',', ' ');
            }
        }

        return number_format($amount, 2, ',', ' ');
    }

    /**
     * Obtenir le pourcentage d'utilisation du stockage (pour les entreprises)
     */
    public function getStorageUsagePercentage(): float
    {
        // Si c'est une entreprise, vérifier son utilisation de stockage
        if ($this->type === 'company') {
            $company = Company::where('user_id', $this->id)->first();
            return $company ? $company->getStorageUsagePercentage() : 0;
        }

        return 0;
    }

    /**
     * Relation avec l'entreprise (si l'utilisateur est lié à une entreprise)
     */
    public function company()
    {
        return $this->hasOne(Company::class, 'user_id');
    }

    public function lastLogin()
    {
        return $this->hasOne(User::class, 'last_login');
    }

    /**
     * Relation avec les commandes de l'utilisateur
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relation avec le pays de l'utilisateur
     */
    public function coupons()
    {
        return $this->hasMany(Coupon::class, 'created_by');
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === 'active';
    }

    public function userCountry()
    {
        return $this->belongsTo(Country::class, 'country', 'code');
    }

    /**
     * Relation avec les notifications de l'utilisateur
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Relation avec la branche de l'utilisateur
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relation avec le département de l'utilisateur
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relation avec le poste de l'utilisateur
     */
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    /**
     * Relation avec l'utilisateur qui a créé cet utilisateur
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function userPlan()
    {
        return $this->belongsTo(Plan::class, 'plan');
    }

    public function userEmployee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    /**
     * Obtenir la numérotaion des employés
     */
    public function employeeIdFormat($number)
    {
        $company = Company::where('user_id', $this->id)->first();
        $prefix = $company ? $company->employee_prefix : '';

        if (empty($number)) {
            return '';
        }

        // Si le numéro n'est pas purement numérique, il est déjà pré-formaté
        if (!is_numeric($number)) {
            if ($prefix && strpos($number, $prefix) !== false) {
                return $number;
            }
            if (strpos($number, '#') === 0) {
                return $number;
            }
            // Si le préfixe a un '#' (ex: #LIT) et que le matricule n'en a pas (ex: LIT0478)
            if ($prefix && strpos($prefix, '#') === 0 && strpos($number, '#') !== 0) {
                $cleanPrefix = ltrim($prefix, '#');
                if (strpos($number, $cleanPrefix) === 0) {
                    return '#' . $number;
                }
            }
            return $number;
        }

        return $prefix . sprintf("%05d", $number);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
