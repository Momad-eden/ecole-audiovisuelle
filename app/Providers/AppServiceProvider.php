<?php

namespace App\Providers;

use App\Filament\Support\TranslationTab;
use App\Models\AgendaEvent;
use App\Models\Artwork;
use App\Models\Faq;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\Page;
use App\Models\Place;
use App\Models\Program;
use App\Models\Room;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Track;
use App\Services\FrontendRevalidator;
use App\Services\Translation\DeepLTranslator;
use App\Services\Translation\NullTranslator;
use App\Services\Translation\Translator;
use Filament\Resources\Events\RecordSaved;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FrontendRevalidator::class);
        $this->app->bind(Translator::class, fn ($app) => filled(config('services.deepl.key'))
            ? $app->make(DeepLTranslator::class)
            : new NullTranslator);
    }

    public function boot(): void
    {
        // Détecte en développement les chargements paresseux (N+1) et les attributs inconnus.
        Model::shouldBeStrict(! $this->app->isProduction());

        // Types courts et stables des fiches traduites (table translations, relation polymorphe).
        Relation::morphMap([
            'page' => Page::class,
            'program' => Program::class,
            'track' => Track::class,
            'room' => Room::class,
            'artwork' => Artwork::class,
            'news' => News::class,
            'faq' => Faq::class,
            'agenda_event' => AgendaEvent::class,
            'service' => Service::class,
            'place' => Place::class,
            'menu_item' => MenuItem::class,
            'setting' => Setting::class,
        ]);

        // Onglet « Anglais » : les textes relus s'écrivent après la fiche (empreinte du français enregistré).
        Event::listen(RecordSaved::class, fn (Model $record, array $data, object $page) => TranslationTab::flush($record, $page));

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
