<?php

namespace App\Http\Resources\Public;

use App\Http\Resources\Public\Concerns\TranslatesFields;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Track */
class TrackResource extends JsonResource
{
    use TranslatesFields;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->t('name'),
            'slug' => $this->slug,
            'shortName' => $this->t('short_name') ?: $this->t('name'),
            'summary' => $this->t('summary'),
            'skills' => $this->t('skills') ?? [],
            'outcomes' => $this->t('outcomes') ?? [],
        ];
    }
}
