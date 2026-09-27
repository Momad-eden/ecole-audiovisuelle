<?php

namespace App\Models\Concerns;

use App\Enums\PublicationStatus;
use Illuminate\Database\Eloquent\Builder;

/**
 * Contenu publiable : brouillon, publié (éventuellement programmé) ou archivé.
 */
trait HasPublication
{
    public function initializeHasPublication(): void
    {
        $this->mergeCasts([
            'status' => PublicationStatus::class,
            'published_at' => 'datetime',
        ]);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublicationStatus::PUBLISHED)
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function isPublished(): bool
    {
        return $this->status === PublicationStatus::PUBLISHED
            && ($this->published_at === null || $this->published_at->lte(now()));
    }
}
