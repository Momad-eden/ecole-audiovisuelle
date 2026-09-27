<?php

namespace App\Http\Resources\Public;

use App\Models\Room;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Room */
class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'tagline' => $this->tagline,
            'intro' => $this->intro,
            'accentColor' => $this->accent_color,
            'cover' => Media::image($this->cover_image, $this->cover_alt),
            'artworksCount' => $this->whenCounted('artworks'),
            'artworks' => ArtworkResource::collection($this->whenLoaded('artworks')),
        ];
    }
}
