<?php

namespace App\Http\Controllers\Api\Public;

use App\Enums\SiteDomain;
use App\Http\Controllers\Controller;
use App\Http\Resources\Public\ArtworkResource;
use App\Http\Resources\Public\ExhibitionResource;
use App\Http\Resources\Public\NewsResource;
use App\Http\Resources\Public\OfferingResource;
use App\Http\Resources\Public\PlaceResource;
use App\Http\Resources\Public\ProgramResource;
use App\Http\Resources\Public\RoomResource;
use App\Models\AgendaEvent;
use App\Models\Artwork;
use App\Models\Exhibition;
use App\Models\Faq;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\Offering;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Place;
use App\Models\Program;
use App\Models\Redirect;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Track;
use App\Services\BlockResolver;
use App\Support\Media;
use App\Support\PreviewToken;
use App\Support\Translation\BlockTexts;
use App\Support\Translation\Localized;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * API publique en lecture seule : uniquement les contenus publiés.
 */
class ContentController extends Controller
{
    public function __construct(private BlockResolver $blocks) {}

    public function site(): JsonResponse
    {
        $published = Page::published()->pluck('slug')->all();
        $unpublished = Page::whereNotIn('slug', $published)->pluck('slug')->all();

        $settings = Setting::current();
        $settings->loadMissing(Localized::eager());
        $t = fn (string $field) => Localized::value($settings, $field);

        return response()->json(['data' => [
            'settings' => [
                'schoolName' => $settings->school_name,
                'description' => $t('description'),
                'logo' => Media::image($settings->logo, $settings->school_name),
                'phone' => $settings->phone,
                'whatsapp' => $settings->whatsapp,
                'email' => $settings->email,
                'address' => $settings->address,
                'openingHours' => $t('opening_hours'),
                'mapUrl' => $settings->map_url,
                'seoTitle' => $t('seo_title'),
                'seoDescription' => $t('seo_description'),
                'social' => collect($settings->only(['facebook', 'instagram', 'youtube', 'tiktok', 'linkedin', 'twitter']))->filter()->all(),
            ],
            'menus' => $this->menus($unpublished),
            'domains' => collect(SiteDomain::cases())->mapWithKeys(fn (SiteDomain $d) => [$d->value => ['label' => $d->labelFor(Localized::locale()), 'color' => $d->color()]])->all(),
            'rooms' => RoomResource::collection(Room::published()->with(Localized::eager())->orderBy('position')->get())->resolve(),
            'places' => PlaceResource::collection(Place::published()->with(Localized::eager())->orderBy('position')->get())->resolve(),
            'hasSchoolPrograms' => Program::published()->where('audience', 'school')->exists(),
        ]]);
    }

    /** Menus à un seul niveau de sous-menus ; jamais de lien vers une page encore en brouillon. */
    private function menus(array $unpublished): array
    {
        $visible = MenuItem::where('is_visible', true)->with(Localized::eager())->orderBy('position')->orderBy('id')->get()
            ->reject(fn (MenuItem $i) => in_array(ltrim($i->url, '/'), $unpublished, true));

        $children = $visible->whereNotNull('parent_id')->groupBy('parent_id');

        return $visible->whereNull('parent_id')
            ->groupBy('location')
            ->map(fn ($items) => $items->map(fn (MenuItem $i) => [
                'label' => Localized::value($i, 'label'),
                'url' => $i->url,
                'isButton' => $i->is_button,
                'children' => ($children[$i->id] ?? collect())->map(fn (MenuItem $c) => [
                    'label' => Localized::value($c, 'label'),
                    'url' => $c->url,
                    'description' => Localized::value($c, 'description') ?: null,
                ])->values()->all(),
            ])->values())
            ->all() + ['main' => [], 'footer' => [], 'legal' => []];
    }

    public function page(string $slug): JsonResponse
    {
        $page = Page::published()->where('slug', $slug)->with(Localized::eager())->firstOrFail();

        return $this->pageResponse($page, $page->blocks, Localized::locale());
    }

    public function preview(Request $request): JsonResponse
    {
        $target = PreviewToken::verify((string) $request->query('token'));
        abort_unless($target && $target['type'] === 'page', 403, 'Lien d\'aperçu invalide ou expiré.');

        $page = Page::findOrFail($target['id']);

        // L'aperçu du brouillon reste en français : seuls les blocs publiés sont traduits.
        return $this->pageResponse($page, $page->draft_blocks, 'fr');
    }

    /**
     * Page dans la langue demandée : textes anglais appliqués d'abord sur les blocs français (clés stables),
     * puis blocs résolus, pour que les données incluses (formations, actualités…) suivent la même langue.
     */
    private function pageResponse(Page $page, ?array $blocks, string $locale): JsonResponse
    {
        $translated = $locale !== 'fr' && filled($page->translation('title', $locale)?->value);

        if ($locale !== 'fr' && $blocks !== null && $page->translation('blocks', $locale)) {
            $keyed = $page->translated('blocks', $locale);
            $blocks = BlockTexts::applyKeyed($blocks, array_is_list($keyed) ? [] : $keyed);
        }

        $isHome = $page->type === 'home' || $page->slug === 'accueil';

        return response()->json(['data' => [
            'title' => $page->translated('title', $locale),
            'slug' => $page->slug,
            'type' => $page->type,
            'domain' => ($page->domain ?? SiteDomain::GENERAL)->value,
            'seo' => $page->translated('seo', $locale),
            'blocks' => $this->blocks->resolve($blocks),
            'updatedAt' => $page->updated_at?->toIso8601String(),
            'locale' => Localized::locale(),
            'contentLocale' => $translated ? $locale : 'fr',
            'alternates' => $isHome ? ['fr' => '/', 'en' => '/en'] : ['fr' => '/'.$page->slug, 'en' => '/en/'.$page->slug],
        ]]);
    }

    public function rooms(): AnonymousResourceCollection
    {
        return RoomResource::collection(Room::published()->withCount(['artworks' => fn ($q) => $q->published()])
            ->with(['tracks' => fn ($q) => $q->where('is_active', true), ...Localized::eager('', 'tracks')])->orderBy('position')->get());
    }

    public function room(string $slug): RoomResource
    {
        $room = Room::published()->where('slug', $slug)
            ->with([
                'artworks' => fn ($q) => $q->published()->with(['room', 'track', ...Localized::eager('', 'room', 'track')]),
                'tracks' => fn ($q) => $q->where('is_active', true),
                ...Localized::eager('', 'tracks'),
            ])
            ->firstOrFail();

        // Les formations publiées qui enseignent au moins une filière de l'univers.
        $room->setRelation('programs', Program::published()
            ->whereHas('cohorts.offerings', fn (Builder $q) => $q->whereIn('track_id', $room->tracks->pluck('id')))
            ->with(Localized::eager())->orderBy('position')->get());

        return new RoomResource($room);
    }

    public function artworks(Request $request): AnonymousResourceCollection
    {
        $artworks = Artwork::published()->with(['room', 'track', ...Localized::eager('', 'room', 'track')])
            // Réalisations des étudiants par défaut ; ?origin=studio pour les productions d'Impact Live Studio.
            ->where('origin', $request->query('origin') === 'studio' ? 'studio' : 'school')
            ->when($request->query('room'), fn (Builder $q, $slug) => $q->whereHas('room', fn ($r) => $r->where('slug', $slug)))
            ->when($request->query('track'), fn (Builder $q, $slug) => $q->whereHas('track', fn ($t) => $t->where('slug', $slug)))
            ->when($request->query('kind'), fn (Builder $q, $kind) => $q->where('kind', $kind))
            ->when($request->boolean('featured'), fn (Builder $q) => $q->where('is_featured', true))
            ->latest('published_at')->latest('id')
            ->paginate(min((int) $request->query('perPage', 24), 60));

        return ArtworkResource::collection($artworks);
    }

    public function artwork(string $slug): ArtworkResource
    {
        $artwork = Artwork::published()->where('slug', $slug)
            ->with(['room', 'track', 'cohort', 'credits', 'exhibitions' => fn ($q) => $q->published(), ...Localized::eager('', 'room', 'track')])
            ->firstOrFail();

        return (new ArtworkResource($artwork))->full();
    }

    public function exhibitions(Request $request): AnonymousResourceCollection
    {
        $exhibitions = Exhibition::published()->orderByDesc('starts_on')->get();

        if ($state = $request->query('state')) {
            $exhibitions = $exhibitions->filter(fn (Exhibition $e) => $e->state() === $state)->values();
        }

        return ExhibitionResource::collection($exhibitions);
    }

    public function exhibition(string $slug): ExhibitionResource
    {
        return new ExhibitionResource(Exhibition::published()->where('slug', $slug)
            ->with(['artworks' => fn ($q) => $q->published()->with(['room', 'track', ...Localized::eager('', 'room', 'track')])])->firstOrFail());
    }

    public function programs(Request $request): AnonymousResourceCollection
    {
        return ProgramResource::collection(Program::published()
            ->when($request->query('audience'), fn (Builder $q, $audience) => $q->where('audience', $audience))
            ->with(Localized::eager())->orderBy('position')->get());
    }

    public function program(string $slug): ProgramResource
    {
        $program = Program::published()->where('slug', $slug)
            ->with(['cohorts' => fn ($q) => $q->whereNotIn('status', ['cancelled'])->with(['offerings.track', ...Localized::eager('offerings.track')]), ...Localized::eager()])
            ->firstOrFail();

        return new ProgramResource($program);
    }

    public function tracks(): JsonResponse
    {
        return response()->json(['data' => Track::where('is_active', true)->with(['room', ...Localized::eager('', 'room')])->orderBy('position')->get()
            ->map(fn (Track $t) => [
                'name' => Localized::value($t, 'name'), 'slug' => $t->slug, 'shortName' => Localized::value($t, 'short_name'),
                'summary' => Localized::value($t, 'summary'), 'description' => Localized::value($t, 'description'),
                'skills' => Localized::value($t, 'skills') ?? [], 'outcomes' => Localized::value($t, 'outcomes') ?? [],
                'room' => $t->room ? ['name' => Localized::value($t->room, 'name'), 'slug' => $t->room->slug, 'accentColor' => $t->room->accent_color] : null,
            ])]);
    }

    public function offerings(Request $request): AnonymousResourceCollection
    {
        $campus = $request->query('campus');
        $campuses = Place::published()->campuses()->get();

        $query = Offering::openForApplications();
        if ($campus) {
            $place = $campuses->firstWhere('slug', $campus);
            if (! $place) {
                return OfferingResource::collection(collect());
            }
            $query = Offering::availableAt($place);
        }

        $offerings = $query
            ->with(['cohort.program.campuses', 'track', ...Localized::eager('cohort.program', 'track')])
            ->when($request->query('audience'), fn (Builder $q, $audience) => $q->whereHas('cohort.program', fn ($p) => $p->where('audience', $audience)))
            ->get();

        // Campus publiés où l'offre est proposée : formation cochée pour le campus et session du campus (ou de tous).
        // Même règle que Offering::isAvailableAt(null) : avec un seul campus publié, toute offre ouverte y est proposée.
        $offerings->each(fn (Offering $o) => $o->setAttribute('campus_ids', $campuses->count() === 1
            ? [$campuses->first()->id]
            : $campuses
                ->filter(fn (Place $c) => $o->cohort->program->campuses->contains('id', $c->id)
                    && ($o->cohort->place_id === null || $o->cohort->place_id === $c->id))
                ->pluck('id')->values()->all()));

        return OfferingResource::collection($offerings);
    }

    public function news(Request $request): AnonymousResourceCollection
    {
        return NewsResource::collection(News::published()->with(Localized::eager())->latest('published_at')->paginate(min((int) $request->query('perPage', 12), 48)));
    }

    public function newsItem(string $slug): NewsResource
    {
        return (new NewsResource(News::published()->where('slug', $slug)->firstOrFail()))->full();
    }

    public function faqs(Request $request): JsonResponse
    {
        return response()->json(['data' => Faq::where('is_visible', true)
            ->when($request->query('group'), fn (Builder $q, $group) => $q->where('group', $group))
            ->with(Localized::eager())->orderBy('position')->get()
            ->map(fn (Faq $f) => ['group' => $f->group, 'question' => Localized::value($f, 'question'), 'answer' => Localized::value($f, 'answer')])]);
    }

    public function partners(): JsonResponse
    {
        return response()->json(['data' => Partner::where('is_active', true)->orderBy('position')->get()
            ->map(fn (Partner $p) => ['name' => $p->name, 'category' => $p->category, 'website' => $p->website, 'description' => $p->description, 'logo' => Media::image($p->logo, $p->name)])]);
    }

    public function redirects(): JsonResponse
    {
        return response()->json(['data' => Redirect::get(['from_path', 'to_path', 'status_code'])
            ->map(fn (Redirect $r) => ['from' => $r->from_path, 'to' => $r->to_path, 'status' => $r->status_code])]);
    }

    public function sitemap(): JsonResponse
    {
        $entry = fn (string $path, $updatedAt) => ['path' => $path, 'updatedAt' => $updatedAt?->toIso8601String()];

        return response()->json(['data' => collect()
            ->merge(Page::published()->get(['slug', 'type', 'updated_at'])->map(fn (Page $p) => $entry($p->type === 'home' ? '/' : '/'.$p->slug, $p->updated_at)))
            ->merge(Room::published()->get(['slug', 'updated_at'])->map(fn (Room $r) => $entry('/emsi/univers/'.$r->slug, $r->updated_at)))
            ->merge(Artwork::published()->get(['slug', 'updated_at'])->map(fn (Artwork $a) => $entry('/emsi/realisations/'.$a->slug, $a->updated_at)))
            ->merge(Program::published()->get(['slug', 'audience', 'updated_at'])->map(fn (Program $p) => $entry(
                ($p->audience?->value === 'professional' ? '/emsi/professionnels/' : '/emsi/formations/').$p->slug, $p->updated_at)))
            ->merge(News::published()->get(['slug', 'updated_at'])->map(fn (News $n) => $entry('/actualites/'.$n->slug, $n->updated_at)))
            ->merge(AgendaEvent::published()->get(['slug', 'updated_at'])->map(fn (AgendaEvent $e) => $entry('/maison-habib-faye/agenda/'.$e->slug, $e->updated_at)))
            ->values()]);
    }
}
