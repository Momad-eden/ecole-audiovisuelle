<?php

namespace App\Http\Resources\Public;

use App\Models\RentalPack;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin RentalPack */
class RentalPackResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'capacity' => $this->capacity,
            'contents' => $this->contents ?? [],
            'priceFrom' => $this->price_from,
            'priceLabel' => $this->priceLabel(),
            'image' => Media::image($this->image, $this->image_alt ?: $this->name),
        ];
    }
}
