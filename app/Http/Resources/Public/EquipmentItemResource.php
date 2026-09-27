<?php

namespace App\Http\Resources\Public;

use App\Models\EquipmentItem;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin EquipmentItem */
class EquipmentItemResource extends JsonResource
{
    private bool $full = false;

    public function full(): static
    {
        $this->full = true;

        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'brand' => $this->brand,
            'usage' => $this->usage->value,
            'summary' => $this->summary,
            'image' => Media::image($this->image, $this->image_alt ?: $this->name),
            'priceFrom' => $this->price_from,
            'priceLabel' => $this->priceLabel(),
            'isFeatured' => $this->is_featured,
            'category' => $this->whenLoaded('category', fn () => ['name' => $this->category->name, 'slug' => $this->category->slug]),
            $this->mergeWhen($this->full, fn () => [
                'description' => $this->description,
                'specs' => collect($this->specs ?? [])->filter(fn ($spec) => filled($spec['label'] ?? null))->values()->all(),
                'quantity' => $this->quantity,
                'gallery' => collect($this->gallery ?? [])->map(fn ($path) => Media::image($path, $this->name))->filter()->values()->all(),
            ]),
        ];
    }
}
