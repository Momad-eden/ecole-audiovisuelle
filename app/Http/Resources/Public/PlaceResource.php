<?php

namespace App\Http\Resources\Public;

use App\Http\Resources\Public\Concerns\TranslatesFields;
use App\Models\Place;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Place */
class PlaceResource extends JsonResource
{
    use TranslatesFields;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'kind' => $this->kind->value,
            'city' => $this->city,
            'address' => $this->address,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'mapUrl' => $this->map_url,
            'openingHours' => $this->t('opening_hours'),
            'tagline' => $this->t('tagline'),
            'description' => $this->t('description'),
            'highlights' => $this->t('highlights') ?? [],
            'image' => Media::image($this->image, $this->t('image_alt') ?: $this->name),
        ];
    }
}
