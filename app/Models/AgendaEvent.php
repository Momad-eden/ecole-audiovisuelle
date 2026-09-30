<?php

namespace App\Models;

use App\Enums\Activity;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Événement de l'agenda, ou référence (prestation réalisée, ex. Festival de Saint-Louis). */
class AgendaEvent extends Model
{
    use HasPublication, HasTranslations, HasUniqueSlug, RevalidatesFrontend;

    protected array $translatable = ['title', 'summary', 'content', 'venue', 'image_alt'];

    protected $fillable = ['title', 'slug', 'activity', 'place_id', 'venue', 'city', 'starts_at', 'ends_at', 'summary', 'content', 'image', 'image_alt', 'ticket_url', 'is_reference', 'position', 'status', 'published_at'];

    protected $casts = ['activity' => Activity::class, 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_reference' => 'boolean'];

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    /** À venir (ou en cours), hors références. */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('is_reference', false)
            ->where(fn (Builder $q) => $q->where('ends_at', '>=', now())->orWhere(fn (Builder $q) => $q->whereNull('ends_at')->where('starts_at', '>=', now()->startOfDay())));
    }
}
