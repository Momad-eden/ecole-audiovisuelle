<?php

namespace App\Models;

use App\Models\Concerns\HasPublication;
use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** Exposition temporaire (festival, promotion, atelier). */
class Exhibition extends Model
{
    use HasFactory, HasPublication, HasUniqueSlug;

    protected $fillable = ['title', 'slug', 'subtitle', 'starts_on', 'ends_on', 'venue', 'curatorial_text', 'cover_image', 'cover_alt', 'status', 'published_at'];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
    ];

    public function artworks(): BelongsToMany
    {
        return $this->belongsToMany(Artwork::class)->withPivot('position')->orderByPivot('position');
    }

    /** « upcoming », « current » ou « past » selon les dates. */
    public function state(): string
    {
        return match (true) {
            $this->starts_on && $this->starts_on->isFuture() => 'upcoming',
            $this->ends_on && $this->ends_on->endOfDay()->isPast() => 'past',
            default => 'current',
        };
    }
}
