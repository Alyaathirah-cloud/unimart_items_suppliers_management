<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS on Heroku and other production environments behind a proxy.
        // This ensures session cookies are generated with the correct scheme so
        // CSRF tokens are not invalidated after form submission.
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
