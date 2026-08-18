<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les utilisateurs existants
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('Aucun utilisateur trouvé. Création de notifications ignorée.');
            return;
        }

        // Notifications de test pour chaque utilisateur
        foreach ($users as $user) {
            // Créer 3-5 notifications par utilisateur
            $notificationCount = rand(3, 5);

            for ($i = 0; $i < $notificationCount; $i++) {
                Notification::create([
                    'type' => $this->getRandomType(),
                    'title' => $this->getRandomTitle(),
                    'message' => $this->getRandomMessage(),
                    'icon' => $this->getRandomIcon(),
                    'color' => $this->getRandomColor(),
                    'is_read' => rand(0, 1) ? true : false,
                    'read_at' => rand(0, 1) ? Carbon::now()->subDays(rand(0, 7)) : null,
                    'user_id' => $user->id,
                    'sender_id' => $this->getRandomSender($user),
                    'source_type' => rand(0, 1) ? User::class : null,
                    'source_id' => rand(0, 1) ? $this->getRandomSource($user) : null,
                    'action_url' => rand(0, 1) ? $this->getRandomActionUrl($user) : null,
                    'expires_at' => rand(0, 3) ? Carbon::now()->addDays(rand(1, 30)) : null,
                    'created_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 24)),
                    'updated_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 24)),
                ]);
            }
        }

        $this->command->info('Notifications de test créées avec succès!');
    }

    private function getRandomType(): string
    {
        $types = ['info', 'success', 'warning', 'error', 'user', 'company', 'order', 'payment'];
        return $types[array_rand($types)];
    }

    private function getRandomTitle(): string
    {
        $titles = [
            'Nouveau message reçu',
            'Mise à jour du système',
            'Rapport généré',
            'Utilisateur ajouté',
            'Commande traitée',
            'Paiement reçu',
            'Maintenance programmée',
            'Nouveau commentaire',
            'Document partagé',
            'Réunion planifiée',
        ];
        return $titles[array_rand($titles)];
    }

    private function getRandomMessage(): string
    {
        $messages = [
            'Vous avez reçu un nouveau message concernant votre compte.',
            'Le système a été mis à jour avec de nouvelles fonctionnalités.',
            'Votre rapport mensuel est maintenant disponible.',
            'Un nouvel utilisateur a été ajouté à votre équipe.',
            'Votre commande a été traitée avec succès.',
            'Un paiement a été reçu sur votre compte.',
            'Une maintenance est programmée pour ce soir.',
            'Vous avez reçu un commentaire sur votre publication.',
            'Un document a été partagé avec vous.',
            'Une réunion a été planifiée pour demain.',
        ];
        return $messages[array_rand($messages)];
    }

    private function getRandomIcon(): string
    {
        $icons = [
            'ti-bell',
            'ti-mail',
            'ti-user',
            'ti-file',
            'ti-calendar',
            'ti-credit-card',
            'ti-settings',
            'ti-info-circle',
            'ti-check-circle',
            'ti-alert-triangle',
        ];
        return $icons[array_rand($icons)];
    }

    private function getRandomColor(): string
    {
        $colors = ['primary', 'success', 'warning', 'danger', 'info'];
        return $colors[array_rand($colors)];
    }

    private function getRandomSender($excludeUser): ?int
    {
        $otherUsers = User::where('id', '!=', $excludeUser->id)->pluck('id')->toArray();

        if (empty($otherUsers)) {
            return null;
        }

        return rand(0, 1) ? $otherUsers[array_rand($otherUsers)] : null;
    }

    private function getRandomSource($user): ?int
    {
        $otherUsers = User::where('id', '!=', $user->id)->pluck('id')->toArray();

        if (empty($otherUsers)) {
            return null;
        }

        return rand(0, 1) ? $otherUsers[array_rand($otherUsers)] : null;
    }

    private function getRandomActionUrl($user): ?string
    {
        $urls = [
            "/users/{$user->id}",
            "/notifications",
            "/settings",
            "/reports",
        ];

        return rand(0, 1) ? $urls[array_rand($urls)] : null;
    }
}
