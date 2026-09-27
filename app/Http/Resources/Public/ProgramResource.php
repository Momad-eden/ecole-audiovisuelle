<?php

namespace App\Http\Resources\Public;

use App\Models\Program;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Program */
class ProgramResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'audience' => $this->audience?->value,
            'kind' => $this->kind?->value,
            'kindLabel' => $this->kind?->getLabel(),
            'levelLabel' => $this->level_label,
            'durationLabel' => $this->duration_label,
            'summary' => $this->summary,
            'cover' => Media::image($this->cover_image, $this->cover_alt),
            'description' => $this->whenLoaded('cohorts', fn () => $this->description),
            'skills' => $this->whenLoaded('cohorts', fn () => $this->skills ?? []),
            'outcomes' => $this->whenLoaded('cohorts', fn () => $this->outcomes ?? []),
            'prerequisites' => $this->whenLoaded('cohorts', fn () => $this->prerequisites ?? []),
            'equipment' => $this->whenLoaded('cohorts', fn () => $this->equipment ?? []),
            'seo' => $this->whenLoaded('cohorts', fn () => $this->seo),
            'acceptsApplications' => $this->whenLoaded('cohorts', fn () => $this->cohorts->contains(fn ($c) => $c->acceptsApplications() && $c->offerings->contains('is_open', true))),
            'cohorts' => CohortResource::collection($this->whenLoaded('cohorts')),
        ];
    }
}
