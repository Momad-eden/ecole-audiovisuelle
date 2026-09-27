<?php

namespace App\Http\Resources\Public;

use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Track */
class TrackResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'shortName' => $this->short_name ?: $this->name,
            'summary' => $this->summary,
            'skills' => $this->skills ?? [],
            'outcomes' => $this->outcomes ?? [],
        ];
    }
}
