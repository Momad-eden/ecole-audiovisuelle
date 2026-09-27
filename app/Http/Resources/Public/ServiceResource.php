<?php

namespace App\Http\Resources\Public;

use App\Models\Service;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Service */
class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'activity' => $this->activity->value,
            'summary' => $this->summary,
            'description' => $this->description,
            'priceFrom' => $this->price_from,
            'priceUnit' => $this->price_unit?->value,
            'priceLabel' => $this->priceLabel(),
            'icon' => $this->icon,
            'image' => Media::image($this->image, $this->image_alt),
        ];
    }
}
