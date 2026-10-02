<?php

namespace App\Http\Resources\Public;

use App\Http\Resources\Public\Concerns\TranslatesFields;
use App\Models\AgendaEvent;
use App\Support\Media;
use App\Support\Translation\Localized;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AgendaEvent */
class AgendaEventResource extends JsonResource
{
    use TranslatesFields;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->t('title'),
            'slug' => $this->slug,
            'activity' => $this->activity->value,
            'activityLabel' => $this->activity->labelFor(Localized::locale()),
            'venue' => $this->t('venue') ?: $this->place?->name,
            'city' => $this->city ?: $this->place?->city,
            'startsAt' => $this->starts_at?->toIso8601String(),
            'endsAt' => $this->ends_at?->toIso8601String(),
            'summary' => $this->t('summary'),
            'content' => $this->t('content'),
            'image' => Media::image($this->image, $this->t('image_alt') ?: $this->t('title')),
            'ticketUrl' => $this->ticket_url,
            'isReference' => $this->is_reference,
        ];
    }
}
