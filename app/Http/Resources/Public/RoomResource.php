<?php

namespace App\Http\Resources\Public;

use App\Http\Resources\Public\Concerns\TranslatesFields;
use App\Models\Room;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Room */
class RoomResource extends JsonResource
{
    use TranslatesFields;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->t('name'),
            'slug' => $this->slug,
            'tagline' => $this->t('tagline'),
            'intro' => $this->t('intro'),
            'accentColor' => $this->accent_color,
            'visual' => $this->visual,
            'isUpcoming' => (bool) $this->is_upcoming,
            'cover' => Media::image($this->cover_image, $this->t('cover_alt')),
            'artworksCount' => $this->whenCounted('artworks'),
            'artworks' => ArtworkResource::collection($this->whenLoaded('artworks')),
            'tracks' => TrackResource::collection($this->whenLoaded('tracks')),
            'programs' => ProgramResource::collection($this->whenLoaded('programs')),
        ];
    }
}
