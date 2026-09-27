<?php

namespace App\Models;

use App\Enums\FundingMode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Offre à laquelle on candidate : programme × filière × session, avec places et frais. */
class Offering extends Model
{
    use HasFactory;

    protected $fillable = ['cohort_id', 'track_id', 'capacity', 'fee_amount', 'registration_fee_amount', 'funding_mode', 'funding_note', 'is_open'];

    protected $casts = [
        'funding_mode' => FundingMode::class,
        'fee_amount' => 'integer',
        'registration_fee_amount' => 'integer',
        'capacity' => 'integer',
        'is_open' => 'boolean',
    ];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /** Libellé lisible : « Volet 1 — 2026 · Son ». */
    public function getLabelAttribute(): string
    {
        return trim($this->cohort->program->title.' — '.$this->cohort->name.($this->track ? ' · '.$this->track->name : ''));
    }

    public function acceptsApplications(): bool
    {
        return $this->is_open && $this->cohort->acceptsApplications() && $this->cohort->program->isPublished();
    }

    /** Offres ouvertes à la candidature en ligne. */
    public function scopeOpenForApplications(Builder $query): Builder
    {
        return $query->where('is_open', true)
            ->whereHas('cohort', fn (Builder $q) => $q->where('status', 'open')
                ->where(fn (Builder $w) => $w->whereNull('applications_open_at')->orWhere('applications_open_at', '<=', now()))
                ->where(fn (Builder $w) => $w->whereNull('applications_close_at')->orWhere('applications_close_at', '>=', now()))
                ->whereHas('program', fn (Builder $p) => $p->published()));
    }
}
