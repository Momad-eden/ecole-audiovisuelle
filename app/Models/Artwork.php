<?php

namespace App\Models;

use App\Enums\ArtworkKind;
use App\Jobs\ComputeArtworkAudioPeaks;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** Œuvre d'apprenant ou de l'école exposée dans le musée. */
class Artwork extends Model
{
    use HasFactory, HasPublication, HasUniqueSlug, RevalidatesFrontend, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'year', 'room_id', 'track_id', 'cohort_id', 'kind', 'origin', 'summary', 'creation_story', 'equipment',
        'cover_image', 'cover_alt', 'gallery', 'audio_file', 'audio_peaks', 'video_url', 'duration_seconds', 'transcript',
        'is_featured', 'position', 'status', 'published_at',
    ];

    protected $casts = [
        'kind' => ArtworkKind::class,
        'equipment' => 'array',
        'gallery' => 'array',
        'audio_peaks' => 'array',
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (Artwork $artwork) {
            if ($artwork->wasChanged('audio_file') || ($artwork->wasRecentlyCreated && $artwork->audio_file)) {
                ComputeArtworkAudioPeaks::dispatch($artwork);
            }
        });
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function credits(): HasMany
    {
        return $this->hasMany(ArtworkCredit::class)->orderBy('position');
    }

    public function exhibitions(): BelongsToMany
    {
        return $this->belongsToMany(Exhibition::class)->withPivot('position');
    }
}
