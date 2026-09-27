<?php

namespace App\Http\Resources\Public;

use App\Models\Artwork;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Artwork */
class ArtworkResource extends JsonResource
{
    /** Version complète (fiche œuvre) ou résumée (listes). */
    public bool $full = false;

    public function full(): static
    {
        $this->full = true;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $summary = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'year' => $this->year,
            'kind' => $this->kind?->value,
            'kindLabel' => $this->kind?->getLabel(),
            'summary' => $this->summary,
            'cover' => Media::image($this->cover_image, $this->cover_alt),
            'isFeatured' => $this->is_featured,
            'hasAudio' => filled($this->audio_file),
            'hasVideo' => filled($this->video_url),
            'room' => $this->whenLoaded('room', fn () => $this->room ? ['name' => $this->room->name, 'slug' => $this->room->slug, 'accentColor' => $this->room->accent_color] : null),
            'track' => $this->whenLoaded('track', fn () => $this->track ? ['name' => $this->track->name, 'slug' => $this->track->slug] : null),
        ];

        if (! $this->full) {
            return $summary;
        }

        return $summary + [
            'creationStory' => $this->creation_story,
            'transcript' => $this->transcript,
            'equipment' => $this->equipment ?? [],
            'audio' => $this->audio_file ? ['url' => Media::url($this->audio_file), 'peaks' => $this->audio_peaks, 'durationSeconds' => $this->duration_seconds] : null,
            'videoUrl' => $this->video_url,
            'durationSeconds' => $this->duration_seconds,
            'gallery' => collect($this->gallery ?? [])->map(fn (string $path) => Media::image($path, $this->title))->values(),
            'cohort' => $this->whenLoaded('cohort', fn () => $this->cohort?->name),
            'credits' => $this->whenLoaded('credits', fn () => $this->credits->map(fn ($credit) => ['name' => $credit->person_name, 'role' => $credit->role])->values()),
            'exhibitions' => $this->whenLoaded('exhibitions', fn () => $this->exhibitions->map(fn ($e) => ['title' => $e->title, 'slug' => $e->slug])->values()),
            'publishedAt' => $this->published_at?->toIso8601String(),
        ];
    }
}
