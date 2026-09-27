<?php

namespace App\Http\Resources\Public;

use App\Models\News;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin News */
class NewsResource extends JsonResource
{
    public bool $full = false;

    public function full(): static
    {
        $this->full = true;

        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'image' => Media::image($this->image, $this->title),
            'publishedAt' => $this->published_at?->toIso8601String(),
            'content' => $this->when($this->full, fn () => $this->content),
        ];
    }
}
