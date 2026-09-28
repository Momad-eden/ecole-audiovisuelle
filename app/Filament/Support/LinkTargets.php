<?php

namespace App\Filament\Support;

use App\Models\AgendaEvent;
use App\Models\Page;
use App\Models\Place;
use App\Models\Program;
use App\Models\Room;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;

/**
 * Destinations des boutons et des liens, présentées par leur nom (« Studio › Réserver une session »)
 * plutôt que par une adresse à taper. Une adresse libre reste possible (WhatsApp, YouTube…).
 */
final class LinkTargets
{
    /** Sections repérables dans les pages (identifiants posés par les blocs du site). */
    private const SECTIONS = [
        '/studio#reserver' => 'Studio › Réserver une session',
        '/studio#productions' => 'Studio › Écouter les productions',
        '/events#devis' => 'Events › Demander un devis (formulaire)',
        '/espace-habib-faye#programmation' => 'Espace Habib Faye › Programmation',
        '/espace-habib-faye#louer' => 'Espace Habib Faye › Louer la salle',
        '/#univers' => 'Accueil › Les univers',
    ];

    private const PAGES = [
        '/' => 'Accueil',
        '/univers' => 'Les univers',
        '/formations' => 'Formations',
        '/ecole' => 'L\'École',
        '/candidater' => 'Candidater',
        '/studio' => 'Impact Live Studio',
        '/events' => 'Impact Live Events',
        '/events/materiel' => 'Events › Le matériel à louer',
        '/demande' => 'Ma demande de devis',
        '/espace-habib-faye' => 'Espace Habib Faye',
        '/agenda' => 'Agenda',
        '/realisations' => 'Réalisations des étudiants',
        '/expositions' => 'Expositions',
        '/actualites' => 'Actualités',
        '/professionnels' => 'Espace Professionnels',
        '/professionnels/candidater' => 'Espace Professionnels › Candidater',
        '/contact' => 'Contact',
    ];

    /** @return array<string, array<string, string>> Destinations groupées pour la liste déroulante. */
    public static function grouped(): array
    {
        $groups = [
            'Pages du site' => self::PAGES,
            'Sections de page' => self::SECTIONS,
            'Candidater dans un campus' => Place::published()->campuses()->orderBy('position')->get()
                ->mapWithKeys(fn (Place $p) => ["/candidater?campus={$p->slug}" => 'Candidater à '.($p->city ?? $p->name)])->all(),
            'Univers' => Room::published()->orderBy('position')->get()
                ->mapWithKeys(fn (Room $r) => ["/univers/{$r->slug}" => "Univers › {$r->name}"])->all(),
            'Formations' => Program::published()->orderBy('position')->get()
                ->mapWithKeys(fn (Program $p) => [($p->audience?->value === 'professional' ? '/professionnels/' : '/formations/').$p->slug => $p->title])->all(),
            'Événements' => AgendaEvent::published()->where('is_reference', false)->latest('starts_at')->limit(30)->get()
                ->mapWithKeys(fn (AgendaEvent $e) => ["/agenda/{$e->slug}" => "Agenda › {$e->title}"])->all(),
            'Autres pages' => Page::where('type', 'free')->orderBy('title')->get()
                ->mapWithKeys(fn (Page $p) => ["/{$p->slug}" => $p->title])->all(),
        ];

        return array_filter($groups);
    }

    /** @return array<string, string> */
    public static function flat(): array
    {
        return array_merge(...array_values(self::grouped()));
    }

    /** @return array<string, string> Destinations dont le nom ou l'adresse contient la recherche, ou l'adresse saisie. */
    public static function search(string $query): array
    {
        $query = trim($query);
        if ($query === '') {
            return [];
        }
        if (preg_match('#^(/|https?://|mailto:|tel:)\S*$#i', $query)) {
            return [$query => "Utiliser l'adresse « {$query} »"];
        }

        $needle = Str::lower(Str::ascii($query));

        return collect(self::flat())
            ->filter(fn (string $label, string $url) => str_contains(Str::lower(Str::ascii($label.' '.$url)), $needle))
            ->take(50)->all();
    }

    public static function label(?string $url): ?string
    {
        return $url === null ? null : (self::flat()[$url] ?? $url);
    }

    /** Champ « Lien » : liste de destinations nommées, recherche, et adresse libre si besoin. */
    public static function field(string $name = 'url', string $label = 'Lien'): Select
    {
        return Select::make($name)->label($label)
            ->searchable()
            ->options(fn () => self::grouped())
            ->getSearchResultsUsing(fn (string $search) => self::search($search))
            ->getOptionLabelUsing(fn (?string $value) => self::label($value))
            ->rules(['regex:#^(/|https?://|mailto:|tel:)#i'])
            ->placeholder('Choisir une page ou une section')
            ->helperText('Choisissez dans la liste, ou collez une adresse complète (https://…, lien WhatsApp).');
    }
}
