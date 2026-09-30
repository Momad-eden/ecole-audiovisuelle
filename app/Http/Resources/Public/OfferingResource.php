<?php

namespace App\Http\Resources\Public;

use App\Models\Offering;
use App\Support\Translation\Localized;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Offering */
class OfferingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->relationLoaded('cohort') && $this->cohort->relationLoaded('program') ? $this->localizedLabel() : null,
            'track' => $this->whenLoaded('track', fn () => $this->track ? ['name' => Localized::value($this->track, 'name'), 'slug' => $this->track->slug] : null),
            'capacity' => $this->capacity,
            'feeAmount' => $this->fee_amount,
            'registrationFeeAmount' => $this->registration_fee_amount,
            'fundingMode' => $this->funding_mode?->value,
            'fundingLabel' => $this->funding_note ?: $this->funding_mode?->getLabel(),
            'campusIds' => $this->resource->getAttributes()['campus_ids'] ?? [],
            'isOpen' => $this->is_open,
            'audience' => $this->relationLoaded('cohort') && $this->cohort->relationLoaded('program') ? $this->cohort->program->audience?->value : null,
        ];
    }

    /** Libellé de l'offre (même forme que Offering::label) avec formation et filière dans la langue de la requête. */
    private function localizedLabel(): string
    {
        $track = $this->track ? ' · '.Localized::value($this->track, 'name') : '';

        return trim(Localized::value($this->cohort->program, 'title').' — '.$this->cohort->name.$track);
    }
}
