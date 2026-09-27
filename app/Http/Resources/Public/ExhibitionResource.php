<?php

namespace App\Http\Resources\Public;

use App\Models\Exhibition;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Exhibition */
class ExhibitionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'subtitle' => $this->subtitle,
            'startsOn' => $this->starts_on?->toDateString(),
            'endsOn' => $this->ends_on?->toDateString(),
            'state' => $this->state(),
            'venue' => $this->venue,
            'curatorialText' => $this->curatorial_text,
            'cover' => Media::image($this->cover_image, $this->cover_alt),
            'artworks' => ArtworkResource::collection($this->whenLoaded('artworks')),
        ];
    }
}
