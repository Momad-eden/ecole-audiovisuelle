<?php

namespace App\Services;

use App\Http\Resources\Public\ArtworkResource;
use App\Http\Resources\Public\NewsResource;
use App\Http\Resources\Public\ProgramResource;
use App\Http\Resources\Public\RoomResource;
use App\Models\Artwork;
use App\Models\Faq;
use App\Models\News;
use App\Models\Partner;
use App\Models\Program;
use App\Models\Room;
use App\Models\Setting;
use App\Support\Media;
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
            'hero' => [...$data, 'video_loop' => Media::url($data['video_loop'] ?? null)],
            'gallery' => [...$data, 'images' => collect($data['images'] ?? [])->map(fn ($item) => [
                ...$this->withImages($item),
            ])->values()->all()],
            'audio' => [...$data, 'tracks' => collect($data['tracks'] ?? [])->map(fn ($track) => [
                'title' => $track['title'] ?? '', 'credits' => $track['credits'] ?? null, 'url' => Media::url($track['file'] ?? null),
            ])->values()->all()],
            'faq' => [...$data, 'items' => Faq::where('group', $data['group'] ?? 'general')->where('is_visible', true)
                ->orderBy('position')->get(['question', 'answer'])->toArray()],
            'programs' => [...$data, 'items' => ProgramResource::collection(Program::published()
                ->where('audience', $data['audience'] ?? 'school')->orderBy('position')
                ->limit((int) ($data['limit'] ?? 6))->get())->resolve()],
            'artworks' => [...$data, 'items' => ArtworkResource::collection($this->artworks($data))->resolve()],
            'rooms' => [...$data, 'items' => RoomResource::collection(Room::published()->withCount(['artworks' => fn ($q) => $q->published()])
                ->with(['tracks' => fn ($q) => $q->where('is_active', true)])->orderBy('position')->get())->resolve()],
            'equipment' => [...$data, 'groups' => collect($data['groups'] ?? [])->map(fn ($group) => $this->withImages($group))->values()->all()],
            'news' => [...$data, 'items' => NewsResource::collection(News::published()->latest('published_at')
                ->limit((int) ($data['limit'] ?? 3))->get())->resolve()],
            'partners' => [...$data, 'items' => Partner::where('is_active', true)
                ->when($data['categories'] ?? null, fn ($q, $categories) => $q->whereIn('category', $categories))
                ->orderBy('position')->get()
                ->map(fn (Partner $p) => ['name' => $p->name, 'category' => $p->category, 'website' => $p->website, 'logo' => Media::image($p->logo, $p->name)])
                ->all()],
            'contact' => [...$data, 'settings' => collect(Setting::current()->only(['phone', 'whatsapp', 'email', 'address', 'opening_hours', 'map_url']))->all()],
            default => $data,
        };

        return $this->camelKeys($data);
    }

    private function artworks(array $data)
    {
        $query = Artwork::published()->with(['room', 'track'])->limit((int) ($data['limit'] ?? 6));

        return match ($data['source'] ?? 'featured') {
            'room' => $query->where('room_id', $data['room_id'] ?? 0)->orderBy('position')->get(),
            'latest' => $query->latest('published_at')->get(),
            default => $query->where('is_featured', true)->latest('published_at')->get(),
        };
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
