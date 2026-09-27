<?php

namespace App\Http\Resources\Public;

use App\Models\AgendaEvent;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AgendaEvent */
class AgendaEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'activity' => $this->activity->value,
            'activityLabel' => $this->activity->getLabel(),
            'venue' => $this->venue ?: $this->place?->name,
            'city' => $this->city ?: $this->place?->city,
            'startsAt' => $this->starts_at?->toIso8601String(),
            'endsAt' => $this->ends_at?->toIso8601String(),
            'summary' => $this->summary,
            'content' => $this->content,
            'image' => Media::image($this->image, $this->image_alt ?: $this->title),
            'ticketUrl' => $this->ticket_url,
            'isReference' => $this->is_reference,
        ];
    }
}
