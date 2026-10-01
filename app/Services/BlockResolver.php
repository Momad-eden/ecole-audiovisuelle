<?php

namespace App\Services;

use App\Enums\Audience;
use App\Enums\SiteDomain;
use App\Http\Resources\Public\AgendaEventResource;
use App\Http\Resources\Public\ArtworkResource;
use App\Http\Resources\Public\EquipmentItemResource;
use App\Http\Resources\Public\NewsResource;
use App\Http\Resources\Public\PlaceResource;
use App\Http\Resources\Public\ProgramResource;
use App\Http\Resources\Public\RentalPackResource;
use App\Http\Resources\Public\RoomResource;
use App\Http\Resources\Public\ServiceResource;
use App\Models\AgendaEvent;
use App\Models\Artwork;
use App\Models\EquipmentItem;
use App\Models\Faq;
use App\Models\News;
use App\Models\Offering;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Place;
use App\Models\Program;
use App\Models\RentalPack;
use App\Models\Room;
use App\Models\Service;
use App\Models\Setting;
use App\Support\Media;
use App\Support\Translation\Localized;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Prépare les blocs d'une page pour le site : URL des médias, clés en camelCase
 * et données des blocs dynamiques (formations, œuvres, actualités…) incluses,
 * pour qu'une page ne demande qu'un seul appel à l'API.
 */
class BlockResolver
{
    private const IMAGE_FIELDS = ['image', 'poster', 'photo'];

    /**
     * @param  array<int, array{type: string, data?: array<string, mixed>}>|null  $blocks
     * @return array<int, array{type: string, data: array<string, mixed>}>
     */
    public function resolve(?array $blocks): array
    {
        return collect($blocks ?? [])
            ->filter(fn ($block) => is_array($block) && isset($block['type']))
            ->values()
            ->map(fn (array $block, int $index) => [
                'id' => "{$block['type']}-{$index}",
                'type' => $block['type'],
                'data' => $this->resolveBlock($block['type'], $block['data'] ?? []),
            ])
            ->all();
    }

    private function resolveBlock(string $type, array $data): array
    {
        $data = $this->withImages($data);

        $data = match ($type) {
            'hero' => [...$data, 'video_loop' => Media::url($data['video_loop'] ?? null), 'sound' => Media::url($data['sound'] ?? null),
                'images' => collect($data['images'] ?? [])->map(fn ($path) => Media::image($path, $data['title'] ?? null))->filter()->values()->all(),
                'tracks' => $this->tracks($data['tracks'] ?? []), 'slides' => $this->heroSlides($data['slides'] ?? []),
                'hotspots' => $this->heroHotspots($data['hotspots'] ?? [])],
            'gallery' => [...$data, 'images' => collect($data['images'] ?? [])->map(fn ($item) => [
                ...$this->withImages($item),
            ])->values()->all()],
            'showcase' => [...$data, 'items' => collect($data['items'] ?? [])->map(fn ($item) => [
                ...$this->withImages($item), 'video' => Media::url($item['video'] ?? null),
            ])->filter(fn ($item) => $item['image'])->values()->all()],
            'audio' => [...$data, 'tracks' => $this->tracks($data['tracks'] ?? [])],
            'faq' => [...$data, 'items' => Faq::where('group', $data['group'] ?? 'general')->where('is_visible', true)
                ->with(Localized::eager())->orderBy('position')->get()
                ->map(fn (Faq $f) => ['question' => Localized::value($f, 'question'), 'answer' => Localized::value($f, 'answer')])->all()],
            'programs' => [...$data, 'items' => ProgramResource::collection(Program::published()
                ->where('audience', $data['audience'] ?? 'school')->with(Localized::eager())->orderBy('position')
                ->limit((int) ($data['limit'] ?? 6))->get())->resolve()],
            'artworks' => [...$data, 'items' => ArtworkResource::collection($this->artworks($data))->resolve()],
            'rooms' => [...$data, 'items' => RoomResource::collection(Room::published()->withCount(['artworks' => fn ($q) => $q->published()])
                ->with(['tracks' => fn ($q) => $q->where('is_active', true), ...Localized::eager('', 'tracks')])->orderBy('position')->get())->resolve()],
            'services' => [...$data, 'items' => ServiceResource::collection(Service::published()->where('activity', $data['activity'] ?? 'studio')
                ->with(Localized::eager())->orderBy('position')->get())->resolve()],
            'equipment_list' => [...$data, 'items' => EquipmentItemResource::collection(EquipmentItem::published()->with('category')
                ->where('usage', $data['usage'] ?? 'rental')
                ->when($data['featured_only'] ?? false, fn ($q) => $q->where('is_featured', true))
                ->orderBy('position')->limit((int) ($data['limit'] ?? 24))->get())->resolve()],
            'packs' => [...$data, 'items' => RentalPackResource::collection(RentalPack::published()->orderBy('position')->get())->resolve()],
            'agenda' => [...$data, 'items' => AgendaEventResource::collection($this->agenda($data))->resolve()],
            'productions' => [...$data, 'items' => ArtworkResource::collection(Artwork::published()->where('origin', 'studio')->with(['room', 'track', ...Localized::eager('', 'room', 'track')])
                ->latest('published_at')->limit((int) ($data['limit'] ?? 6))->get())->resolve()],
            'ecosystem' => [...$data, 'items' => collect($data['items'] ?? [])->map(fn ($item) => $this->withImages($item))->values()->all()],
            'campuses' => [...$data, 'items' => $this->campusCards()],
            'places' => [...$data, 'items' => PlaceResource::collection(Place::published()->with(Localized::eager())
                ->when($data['kind'] ?? null, fn ($q, $kind) => $q->where('kind', $kind))->orderBy('position')->get())->resolve()],
            'equipment' => [...$data, 'groups' => collect($data['groups'] ?? [])->map(fn ($group) => $this->withImages($group))->values()->all()],
            'news' => [...$data, 'items' => NewsResource::collection(News::published()->with(Localized::eager())->latest('published_at')
                ->limit((int) ($data['limit'] ?? 3))->get())->resolve()],
            'partners' => [...$data, 'items' => Partner::where('is_active', true)
                ->when($data['categories'] ?? null, fn ($q, $categories) => $q->whereIn('category', $categories))
                ->orderBy('position')->get()
                ->map(fn (Partner $p) => ['name' => $p->name, 'category' => $p->category, 'website' => $p->website, 'logo' => Media::image($p->logo, $p->name)])
                ->all()],
            'contact' => [...$data, 'settings' => $this->contactSettings()],
            'domains' => [...$data, 'panels' => $this->domainPanels($data['panels'] ?? [])],
            'campus_programs' => [...$data, ...$this->campusPrograms($data['campus_id'] ?? null)],
            'downloads' => [...$data, 'files' => $this->downloads($data['files'] ?? [])],
            default => $data,
        };

        return $this->camelKeys($data);
    }

    /**
     * Cartes des campus, avec l'adresse de la page du campus (« Découvrir le campus ») : la page publiée
     * qui porte son bloc « Formations de ce campus », sinon /emsi/{ville} si elle est publiée, sinon aucune.
     */
    private function campusCards(): array
    {
        $pages = Page::published()->get(['slug', 'blocks']);
        $byBlock = [];
        foreach ($pages as $page) {
            foreach ($page->blocks ?? [] as $block) {
                $campusId = ($block['type'] ?? null) === 'campus_programs' ? ($block['data']['campus_id'] ?? null) : null;
                if ($campusId !== null) {
                    $byBlock[(int) $campusId] ??= '/'.$page->slug;
                }
            }
        }
        $slugs = $pages->pluck('slug')->flip();

        return Place::published()->campuses()->with(Localized::eager())->orderBy('position')->get()->map(function (Place $place) use ($byBlock, $slugs) {
            $citySlug = 'emsi/'.Str::slug((string) ($place->city ?: $place->name));

            return [...(new PlaceResource($place))->resolve(),
                'pageUrl' => $byBlock[$place->id] ?? ($slugs->has($citySlug) ? '/'.$citySlug : null)];
        })->all();
    }

    /** Morceaux à écouter (bloc Audio, héros Studio) : fichier remplacé par son URL publique. */
    private function tracks(array $tracks): array
    {
        return collect($tracks)->map(fn ($track) => [
            'title' => $track['title'] ?? '', 'credits' => $track['credits'] ?? null, 'url' => Media::url($track['file'] ?? null),
        ])->values()->all();
    }

    /** Diapositives du héros Cinéma : une diapositive sans photo est écartée. */
    private function heroSlides(array $slides): array
    {
        return collect($slides)->map(fn ($slide) => $this->withImages($slide))
            ->filter(fn ($slide) => ($slide['image'] ?? null) !== null)
            ->map(fn ($slide) => [
                'eyebrow' => $slide['eyebrow'] ?? null,
                'title' => $slide['title'] ?? '',
                'image' => $slide['image'],
                'link' => filled($slide['link_url'] ?? null) ? ['label' => $slide['link_label'] ?? '', 'url' => $slide['link_url']] : null,
            ])->values()->all();
    }

    /** Points du héros Studio, en % de la photo : hors cadre ou sans libellé, ils sont écartés. */
    private function heroHotspots(array $points): array
    {
        return collect($points)
            ->filter(fn ($p) => is_numeric($p['x'] ?? null) && is_numeric($p['y'] ?? null)
                && $p['x'] >= 0 && $p['x'] <= 100 && $p['y'] >= 0 && $p['y'] <= 100
                && trim((string) ($p['label'] ?? '')) !== '')
            ->map(fn ($p) => ['x' => round((float) $p['x'], 1), 'y' => round((float) $p['y'], 1), 'label' => trim($p['label'])])
            ->values()->all();
    }

    /** Panneaux du triptyque : couleur du domaine ajoutée, photo remplacée par {url, alt}. */
    private function domainPanels(array $panels): array
    {
        return collect($panels)->map(fn ($panel) => $this->withImages($panel))->map(fn ($panel) => [
            'domain' => $panel['domain'] ?? SiteDomain::GENERAL->value,
            'color' => (SiteDomain::tryFrom((string) ($panel['domain'] ?? '')) ?? SiteDomain::GENERAL)->color(),
            'eyebrow' => $panel['eyebrow'] ?? null,
            'title' => $panel['title'] ?? '',
            'text' => $panel['text'] ?? null,
            'image' => $panel['image'] ?? null,
            'url' => $panel['url'] ?? '/',
            'label' => $panel['label'] ?? null,
        ])->values()->all();
    }

    /**
     * Formations de l'école ouvertes à la candidature dans ce campus (Offering::availableAt),
     * avec la prochaine rentrée du campus : la plus proche date de début à venir de leurs sessions.
     *
     * @return array{campus: array<string, mixed>|null, items: array<int, array<string, mixed>>}
     */
    private function campusPrograms(mixed $campusId): array
    {
        $campus = $campusId ? Place::published()->campuses()->find($campusId) : null;
        if (! $campus) {
            return ['campus' => null, 'items' => []];
        }

        $today = now()->startOfDay();
        $items = Offering::availableAt($campus)->with(['cohort.program', ...Localized::eager('cohort.program')])->get()
            ->groupBy(fn (Offering $offering) => $offering->cohort->program_id)
            ->map(function ($offerings) use ($campus, $today) {
                $program = $offerings->first()->cohort->program;
                $nextStart = $offerings->map(fn (Offering $o) => $o->cohort->starts_on)
                    ->filter(fn ($date) => $date !== null && $date->gte($today))->sort()->first();

                return $program->audience === Audience::SCHOOL ? [
                    'position' => $program->position,
                    'title' => Localized::value($program, 'title'),
                    'slug' => $program->slug,
                    'summary' => Localized::value($program, 'summary'),
                    'cover' => Media::image($program->cover_image, $program->cover_alt),
                    'next_start' => $nextStart?->toDateString(),
                    'apply_url' => '/candidater?'.http_build_query(['campus' => $campus->slug, 'formation' => $program->slug]),
                ] : null;
            })
            ->filter()->sortBy([['position', 'asc'], ['title', 'asc']])
            ->map(fn (array $item) => collect($item)->except('position')->all())
            ->values()->all();

        return ['campus' => ['id' => $campus->id, 'name' => $campus->name, 'slug' => $campus->slug, 'city' => $campus->city], 'items' => $items];
    }

    /** Documents à télécharger : un fichier absent du disque est écarté ; poids en octets et extension. */
    private function downloads(array $files): array
    {
        $disk = Storage::disk('public');

        return collect($files)
            ->filter(fn ($file) => filled($file['file'] ?? null) && $disk->exists($file['file']))
            ->map(fn ($file) => [
                'title' => $file['title'] ?? basename($file['file']),
                'description' => $file['description'] ?? null,
                'url' => Media::url($file['file']),
                'size' => $disk->size($file['file']),
                'extension' => strtolower(pathinfo($file['file'], PATHINFO_EXTENSION)),
            ])->values()->all();
    }

    private function agenda(array $data)
    {
        $query = AgendaEvent::published()->with(['place', ...Localized::eager()])
            ->when($data['activity'] ?? null, fn ($q, $activity) => $q->where('activity', $activity))
            ->limit((int) ($data['limit'] ?? 6));

        return ($data['scope'] ?? 'upcoming') === 'references'
            ? $query->where('is_reference', true)->orderBy('position')->orderByDesc('starts_at')->get()
            : $query->upcoming()->orderBy('starts_at')->get();
    }

    private function artworks(array $data)
    {
        $query = Artwork::published()->where('origin', 'school')->with(['room', 'track', ...Localized::eager('', 'room', 'track')])->limit((int) ($data['limit'] ?? 6));

        return match ($data['source'] ?? 'featured') {
            'room' => $query->where('room_id', $data['room_id'] ?? 0)->orderBy('position')->get(),
            'latest' => $query->latest('published_at')->get(),
            default => $query->where('is_featured', true)->latest('published_at')->get(),
        };
    }

    /** Coordonnées du bloc Contact ; les horaires suivent la langue de la requête. */
    private function contactSettings(): array
    {
        $settings = Setting::current();

        return [...$settings->only(['phone', 'whatsapp', 'email', 'address', 'opening_hours', 'map_url']),
            'opening_hours' => Localized::value($settings, 'opening_hours')];
    }

    /** Remplace chaque champ image (et son texte alternatif) par {url, alt}. */
    private function withImages(array $data): array
    {
        foreach (self::IMAGE_FIELDS as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = Media::image($data[$field], $data["{$field}_alt"] ?? null);
                unset($data["{$field}_alt"]);
            }
        }

        return $data;
    }

    private function camelKeys(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[is_string($key) ? Str::camel($key) : $key] = is_array($value) ? $this->camelKeys($value) : $value;
        }

        return $result;
    }
}
