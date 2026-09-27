<?php

namespace App\Providers;

use App\Services\FrontendRevalidator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FrontendRevalidator::class);
    }

    public function boot(): void
    {
        // Détecte en développement les chargements paresseux (N+1) et les attributs inconnus.
        Model::shouldBeStrict(! $this->app->isProduction());

        // Formulaires publics : 5 envois par tranche de 10 minutes et par adresse IP.
        RateLimiter::for('applications', fn (Request $request) => Limit::perMinutes(10, 5)->by($request->ip()));
        RateLimiter::for('contact', fn (Request $request) => Limit::perMinutes(10, 5)->by($request->ip()));
        RateLimiter::for('public-api', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));
    }
}
