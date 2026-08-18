<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Créer une nouvelle notification
     */
    public static function create(
        User $user,
        string $type,
        string $title,
        string $message,
        array $options = []
    ): Notification {
        return Notification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $options['icon'] ?? null,
            'color' => $options['color'] ?? 'primary',
            'data' => $options['data'] ?? null,
            'user_id' => $user->id,
            'sender_id' => $options['sender_id'] ?? Auth::id(),
            'source_type' => $options['source_type'] ?? null,
            'source_id' => $options['source_id'] ?? null,
            'action_url' => $options['action_url'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
        ]);
    }

    /**
     * Créer une notification pour tous les admins
     */
    public static function createForAdmins(
        string $type,
        string $title,
        string $message,
        array $options = []
    ): array {
        $admins = User::whereIn('type', ['admin', 'super_admin'])->get();
        $notifications = [];

        foreach ($admins as $admin) {
            $notifications[] = self::create($admin, $type, $title, $message, $options);
        }

        return $notifications;
    }

    /**
     * Créer une notification pour tous les utilisateurs d'une entreprise
     */
    public static function createForCompany(
        User $company,
        string $type,
        string $title,
        string $message,
        array $options = []
    ): array {
        $users = User::where('company_id', $company->id)
            ->orWhere('id', $company->id)
            ->get();

        $notifications = [];

        foreach ($users as $user) {
            $notifications[] = self::create($user, $type, $title, $message, $options);
        }

        return $notifications;
    }

    /**
     * Créer une notification système (pour le super admin uniquement)
     */
    public static function createSystemNotification(
        string $type,
        string $title,
        string $message,
        array $options = []
    ): ?Notification {
        $superAdmin = User::where('type', 'super_admin')->first();

        if (!$superAdmin) {
            return null;
        }

        return self::create($superAdmin, $type, $title, $message, $options);
    }

    /**
     * Types de notifications prédéfinis
     */
    public static function getNotificationTypes(): array
    {
        return [
            'info' => [
                'color' => 'primary',
                'icon' => 'ti-info-circle'
            ],
            'success' => [
                'color' => 'success',
                'icon' => 'ti-check-circle'
            ],
            'warning' => [
                'color' => 'warning',
                'icon' => 'ti-alert-triangle'
            ],
            'error' => [
                'color' => 'danger',
                'icon' => 'ti-x-circle'
            ],
            'user' => [
                'color' => 'info',
                'icon' => 'ti-user'
            ],
            'company' => [
                'color' => 'info',
                'icon' => 'ti-building'
            ],
            'order' => [
                'color' => 'success',
                'icon' => 'ti-shopping-cart'
            ],
            'payment' => [
                'color' => 'warning',
                'icon' => 'ti-credit-card'
            ],
        ];
    }

    /**
     * Créer une notification avec un type prédéfini
     */
    public static function createTyped(
        User $user,
        string $notificationType,
        string $title,
        string $message,
        array $options = []
    ): Notification {
        $types = self::getNotificationTypes();

        if (isset($types[$notificationType])) {
            $options = array_merge($types[$notificationType], $options);
        }

        return self::create($user, $notificationType, $title, $message, $options);
    }

    /**
     * Notifications système automatiques
     */
    public static function notifyUserRegistered(User $user): Notification
    {
        return self::createTyped(
            User::where('type', 'super_admin')->first(),
            'user',
            'Nouvel utilisateur inscrit',
            "L'utilisateur {$user->name} s'est inscrit sur la plateforme.",
            [
                'source_type' => User::class,
                'source_id' => $user->id,
                'action_url' => route('admin.users.show', $user),
            ]
        );
    }

    public static function notifyCompanyCreated(User $company): Notification
    {
        return self::createTyped(
            User::where('type', 'super_admin')->first(),
            'company',
            'Nouvelle entreprise créée',
            "L'entreprise {$company->name} a été créée sur la plateforme.",
            [
                'source_type' => User::class,
                'source_id' => $company->id,
                'action_url' => route('super-admin.enterprises.show', $company),
            ]
        );
    }

    public static function notifyOrderPlaced(User $user, array $orderData): Notification
    {
        return self::createTyped(
            User::where('type', 'super_admin')->first(),
            'order',
            'Nouvelle commande passée',
            "L'utilisateur {$user->name} a passé une commande.",
            [
                'data' => $orderData,
                'source_type' => User::class,
                'source_id' => $user->id,
            ]
        );
    }

    /**
     * Nettoyer les notifications expirées
     */
    public static function cleanupExpired(): int
    {
        return Notification::where('expires_at', '<', now())
            ->delete();
    }

    /**
     * Statistiques globales des notifications
     */
    public static function getGlobalStats(): array
    {
        return [
            'total' => Notification::count(),
            'unread' => Notification::unread()->count(),
            'expired' => Notification::where('expires_at', '<', now())->count(),
            'today' => Notification::whereDate('created_at', today())->count(),
            'this_week' => Notification::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];
    }
}
