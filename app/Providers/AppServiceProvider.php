<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Dates en francais : jours et mois traduits par translatedFormat() / isoFormat()
        Carbon::setLocale('fr');
        CarbonImmutable::setLocale('fr');

        // Chaque email remis au serveur SMTP est journalisé avec l'identifiant attribué par le serveur :
        // permet de prouver l'envoi et de retrouver le message dans les journaux de l'hébergeur.
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Mail\Events\MessageSent::class, function ($evenement) {
            \Illuminate\Support\Facades\Log::info('Email remis au serveur', [
                'destinataires' => collect($evenement->message->getTo())->map(fn ($a) => $a->getAddress())->all(),
                'objet' => $evenement->message->getSubject(),
                'message_id' => $evenement->sent->getMessageId(),
            ]);
        });

        // Mot de passe oublié : lien vers /reset-password/{jeton}?email=… et email en français
        $lienReinitialisation = fn ($user, string $token) => route('password.reset', [
            'code' => $token,
            'email' => $user->getEmailForPasswordReset(),
        ]);
        ResetPassword::createUrlUsing($lienReinitialisation);
        ResetPassword::toMailUsing(fn ($user, string $token) => (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe RH Flow')
            ->greeting('Bonjour ' . ($user->name ?? '') . ',')
            ->line('Vous avez demandé à réinitialiser le mot de passe de votre compte RH Flow.')
            ->action('Choisir un nouveau mot de passe', $lienReinitialisation($user, $token))
            ->line('Ce lien est valable ' . config('auth.passwords.users.expire') . ' minutes.')
            ->line("Si vous n'êtes pas à l'origine de cette demande, ignorez cet email : votre mot de passe reste inchangé.")
            ->salutation("L'équipe RH Flow"));

        // if (config('app.env') === 'production' || str_contains(config('app.url'), 'https')) {
        //     \Illuminate\Support\Facades\URL::forceScheme('https');
        // }
    }
}
