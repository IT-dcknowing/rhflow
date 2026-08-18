<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

     /**
     * ✅ Récupérer une valeur de paramètre depuis le cache ou la base
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? static::castFromString($setting->value, $setting->type) : $default;
        });
    }

    /**
     * ✅ Définir ou mettre à jour une valeur de paramètre
     */
    
    public static function set(string $key, $value, string $type = 'string', string $group = 'general', ?string $label = null, ?string $description = null): void
    {
        $storedValue = match ($type) {
            'boolean' => (int) filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_encode($value),
            'array' => is_array($value) ? json_encode($value) : (string) $value,
            default => (string) $value,
        };

        // Sauvegarde en base
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storedValue,
                'type' => $type,
                'group' => $group,
                'label' => $label ?? ucfirst(str_replace('_', ' ', $key)),
                'description' => $description ?? '',
                'is_public' => true,
            ]
        );

        // Nettoyer le cache après modification
        Cache::forget("setting_{$key}");
        Cache::forget("settings_group_{$group}");
    }
    /**
     * ✅ Conversion des types lors de la lecture
     */
    protected static function castFromString($value, $type)
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'json', 'array' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * ✅ Supprimer un paramètre et nettoyer le cache
     */
    public static function forget(string $key): void
    {
        static::where('key', $key)->delete();
        Cache::forget("setting_{$key}");
    }

    /**
     * ✅ Retourner tous les paramètres d’un groupe
     */
    public static function group(string $group): array
    {
        return Cache::rememberForever("settings_group_{$group}", function () use ($group) {
            return static::where('group', $group)->get()
                ->mapWithKeys(fn ($item) => [
                    $item->key => static::castFromString($item->value, $item->type),
                ])
                ->toArray();
        });
    }


    // Ajoutez ces méthodes manquantes
    public static function getGroup(string $group): array
    {
        return Cache::rememberForever("settings_group_{$group}", function () use ($group) {
            return static::where('group', $group)
                ->pluck('value', 'key')
                ->map(function($value, $key) {
                    $setting = static::where('key', $key)->first();
                    return $setting ? static::castFromString($value, $setting->type) : $value;
                })
                ->toArray();
        });
    }

    /**
     * Get public settings
     */
    public static function getPublicSettings(): array
    {
        return static::where('is_public', true)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => static::castValue($setting->value, $setting->type)];
            })
            ->toArray();
    }

    /**
     * Cast value based on type
     */
    private static function castValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'integer' => is_numeric($value) ? (int) $value : 0,
            'float' => is_numeric($value) ? (float) $value : 0.0,
            'json' => json_decode($value, true),
            'array' => is_array($value) ? $value : explode(',', $value),
            default => (string) $value,
        };
    }

    /**
     * Cast value to string for storage
     */
    private static function castToString($value, string $type): string
    {
        if ($value === null) {
            return '';
        }

        return match ($type) {
            'boolean' => $value ? 'true' : 'false',
            'json' => json_encode($value),
            'array' => is_array($value) ? implode(',', $value) : (string) $value,
            default => (string) $value,
        };
    }

    /**
     * Get logo URL
     */
    public static function getLogoUrl(): string
    {
        $logo = static::get('app_logo');

        if ($logo && file_exists(public_path('storage/logos/' . $logo))) {
            return asset('storage/logos/' . $logo);
        }

        return asset('assets/img/logo.png'); // logo par défaut
    }

    /**
     * Set logo
     */
    public static function setLogo(string $logoPath): void
    {
        static::set('app_logo', $logoPath, 'string', 'general', 'Logo de l\'application', 'Logo affiché dans l\'application');
    }

    /**
     * Initialize default settings
     */
    public static function initializeDefaults(): void
    {
        $defaults = [
            // General settings
            [
                'key' => 'app_name',
                'value' => 'RH Flow',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Nom de l\'application',
                'description' => 'Nom affiché dans l\'application',
                'is_public' => true,
            ],
            [
                'key' => 'app_logo',
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Logo de l\'application',
                'description' => 'Chemin vers le fichier logo',
                'is_public' => true,
            ],
            [
                'key' => 'app_url',
                'value' => config('app.url'),
                'type' => 'string',
                'group' => 'general',
                'label' => 'URL de l\'application',
                'description' => 'URL principale de l\'application',
                'is_public' => true,
            ],
            [
                'key' => 'timezone',
                'value' => 'Europe/Paris',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Fuseau horaire',
                'description' => 'Fuseau horaire par défaut',
                'is_public' => false,
            ],
            [
                'key' => 'default_language',
                'value' => 'fr',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Langue par défaut',
                'description' => 'Langue par défaut de l\'application',
                'is_public' => false,
            ],

            // Maintenance settings
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'general',
                'label' => 'Mode maintenance',
                'description' => 'Activer le mode maintenance pour bloquer l\'accès aux utilisateurs',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'Le site est en maintenance. Nous serons bientôt de retour.',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Message de maintenance',
                'description' => 'Message affiché pendant la maintenance',
                'is_public' => true,
            ],

            //email settings
            [
                'key' => 'mail_driver',
                'value' => 'smtp',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Driver email',
                'description' => 'Méthode d\'envoi des emails',
                'is_public' => false,
            ],
            [
                'key' => 'mail_host',
                'value' => 'smtp.gmail.com',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Serveur SMTP',
                'description' => 'Serveur SMTP pour l\'envoi des emails',
                'is_public' => false,
            ],
            [
                'key' => 'mail_port',
                'value' => '587',
                'type' => 'integer',
                'group' => 'email',
                'label' => 'Port SMTP',
                'description' => 'Port du serveur SMTP',
                'is_public' => false,
            ],
            [
                'key' => 'mail_username',
                'value' => '',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Utilisateur SMTP',
                'description' => 'Nom d\'utilisateur SMTP',
                'is_public' => false,
            ],
            [
                'key' => 'mail_password',
                'value' => '',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Mot de passe SMTP',
                'description' => 'Mot de passe SMTP',
                'is_public' => false,
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'tls',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Chiffrement SMTP',
                'description' => 'Type de chiffrement SMTP',
                'is_public' => false,
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@rhflow.com',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Adresse expéditeur',
                'description' => 'Adresse email d\'expédition',
                'is_public' => false,
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'RH Flow',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Nom expéditeur',
                'description' => 'Nom affiché comme expéditeur',
                'is_public' => false,
            ],

            // Security settings
            [
                'key' => 'session_lifetime',
                'value' => '120',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Durée de session',
                'description' => 'Durée de vie des sessions en minutes',
                'is_public' => false,
            ],
            [
                'key' => 'password_expiry_days',
                'value' => '90',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Expiration mot de passe',
                'description' => 'Nombre de jours avant expiration des mots de passe',
                'is_public' => false,
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Tentatives de connexion',
                'description' => 'Nombre maximum de tentatives de connexion',
                'is_public' => false,
            ],
            [
                'key' => 'lockout_duration',
                'value' => '15',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Durée de blocage',
                'description' => 'Durée de blocage en minutes après tentatives échouées',
                'is_public' => false,
            ],
            [
                'key' => 'require_email_verification',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'security',
                'label' => 'Vérification email obligatoire',
                'description' => 'Exiger la vérification des emails',
                'is_public' => false,
            ],

            // Backup settings
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'string',
                'group' => 'backup',
                'label' => 'Fréquence de sauvegarde',
                'description' => 'Fréquence des sauvegardes automatiques',
                'is_public' => false,
            ],
            [
                'key' => 'retention_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'backup',
                'label' => 'Rétention des sauvegardes',
                'description' => 'Nombre de jours de rétention des sauvegardes',
                'is_public' => false,
            ],
        ];

        foreach ($defaults as $setting) {
            static::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
