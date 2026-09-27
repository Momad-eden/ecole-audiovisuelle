<?php

namespace App\Http\Resources\Public;

use App\Models\Place;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Place */
class PlaceResource extends JsonResource
{
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
            'openingHours' => $this->opening_hours,
            'description' => $this->description,
            'image' => Media::image($this->image, $this->image_alt ?: $this->name),
        ];
    }
}
