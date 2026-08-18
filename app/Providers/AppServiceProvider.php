<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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

        // if (config('app.env') === 'production' || str_contains(config('app.url'), 'https')) {
        //     \Illuminate\Support\Facades\URL::forceScheme('https');
        // }
    }
}
