<?php

namespace App\Providers;

use App\Services\FrontendRevalidator;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

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

        // Éditeur de texte : polices et tailles guidées (script chargé seulement avec l'éditeur).
        FilamentAsset::register([
            Js::make('rich-content-plugins/typography', resource_path('js/filament/rich-content-plugins/typography.js'))->loadedOnRequest(),
            Css::make('rich-text-typography', resource_path('css/filament/rich-text-typography.css')),
        ]);
        $this->app->extend(HtmlSanitizerConfig::class, fn (HtmlSanitizerConfig $config) => $config
            ->allowAttribute('data-font', allowedElements: 'span')
            ->allowAttribute('data-size', allowedElements: 'span'));

        // Formulaires publics : 5 envois par tranche de 10 minutes et par adresse IP.
        RateLimiter::for('bookings', fn (Request $request) => Limit::perMinutes(10, 5)->by($request->ip()));
        RateLimiter::for('applications', fn (Request $request) => Limit::perMinutes(10, 5)->by($request->ip()));
        RateLimiter::for('contact', fn (Request $request) => Limit::perMinutes(10, 5)->by($request->ip()));
        RateLimiter::for('public-api', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));
    }
}
