<?php

namespace App\Http\Resources\Public;

use App\Models\Cohort;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Cohort */
class CohortResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'startsOn' => $this->starts_on?->toDateString(),
            'endsOn' => $this->ends_on?->toDateString(),
            'status' => $this->status?->value,
            'statusLabel' => $this->status?->getLabel(),
            'applicationsOpenAt' => $this->applications_open_at?->toIso8601String(),
            'applicationsCloseAt' => $this->applications_close_at?->toIso8601String(),
            'acceptsApplications' => $this->acceptsApplications(),
            'offerings' => OfferingResource::collection($this->whenLoaded('offerings')),
        ];
    }
}
