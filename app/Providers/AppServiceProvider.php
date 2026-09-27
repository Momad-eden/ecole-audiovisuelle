<?php

namespace App\Providers;

use App\Models\User;
use App\View\Composers\SettingComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        // Périmètres d'accès par rôle (remplacés par des Policies en Phase 3).
        Gate::define('view-finances', fn (User $user) => in_array($user->role, ['directeur', 'gestionnaire'], true));
        Gate::define('manage-admissions', fn (User $user) => in_array($user->role, ['directeur', 'gestionnaire'], true));
        Gate::define('manage-content', fn (User $user) => in_array($user->role, ['directeur', 'communication'], true));

        // Candidatures publiques : 5 envois par tranche de 10 minutes et par adresse IP.
        RateLimiter::for('admissions', fn (Request $request) => Limit::perMinutes(10, 5)->by($request->ip()));

        View::composer(
            ['components.public.*', 'layouts.public', 'layouts.admin', 'public.*'],
            SettingComposer::class
        );
    }
}
