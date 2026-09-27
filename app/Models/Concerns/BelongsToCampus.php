<?php

namespace App\Models\Concerns;

use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Donnée rattachée à un campus (caisse, étudiants, candidatures) : un membre du personnel
 * rattaché à un campus ne voit que celles de son campus ; sans campus, il voit tout.
 */
trait BelongsToCampus
{
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        return $user?->place_id ? $query->where($this->qualifyColumn('place_id'), $user->place_id) : $query;
    }

    public function isVisibleTo(User $user): bool
    {
        return ! $user->place_id || (int) $this->place_id === (int) $user->place_id;
    }
}
