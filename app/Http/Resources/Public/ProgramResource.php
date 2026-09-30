<?php

namespace App\Http\Resources\Public;

use App\Http\Resources\Public\Concerns\TranslatesFields;
use App\Models\Program;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Program */
class ProgramResource extends JsonResource
{
    use TranslatesFields;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->t('title'),
            'slug' => $this->slug,
            'audience' => $this->audience?->value,
            'kind' => $this->kind?->value,
            'kindLabel' => $this->kind?->getLabel(),
            'levelLabel' => $this->t('level_label'),
            'durationLabel' => $this->t('duration_label'),
            'summary' => $this->t('summary'),
            'cover' => Media::image($this->cover_image, $this->cover_alt),
            'description' => $this->whenLoaded('cohorts', fn () => $this->t('description')),
            'skills' => $this->whenLoaded('cohorts', fn () => $this->t('skills') ?? []),
            'outcomes' => $this->whenLoaded('cohorts', fn () => $this->t('outcomes') ?? []),
            'prerequisites' => $this->whenLoaded('cohorts', fn () => $this->t('prerequisites') ?? []),
            'equipment' => $this->whenLoaded('cohorts', fn () => $this->t('equipment') ?? []),
            'seo' => $this->whenLoaded('cohorts', fn () => $this->t('seo')),
            'acceptsApplications' => $this->whenLoaded('cohorts', fn () => $this->cohorts->contains(fn ($c) => $c->acceptsApplications() && $c->offerings->contains('is_open', true))),
            'cohorts' => CohortResource::collection($this->whenLoaded('cohorts')),
        ];
    }
}
