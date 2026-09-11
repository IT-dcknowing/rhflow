<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
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

        // if (config('app.env') === 'production' || str_contains(config('app.url'), 'https')) {
        //     \Illuminate\Support\Facades\URL::forceScheme('https');
        // }
    }
}
