<?php

namespace App\Providers;

use App\View\Composers\SettingComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Candidatures publiques : 5 envois par tranche de 10 minutes et par adresse IP.
        RateLimiter::for('admissions', fn (Request $request) => Limit::perMinutes(10, 5)->by($request->ip()));

        View::composer(
            ['components.public.*', 'layouts.public', 'layouts.admin', 'public.*'],
            SettingComposer::class
        );
    }
}
