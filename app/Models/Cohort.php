<?php

namespace App\Models;

use App\Enums\CohortStatus;
use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Session d'un programme (ex. « Volet 1 — 2026 »). */
class Cohort extends Model
{
    use HasFactory, RevalidatesFrontend;

    protected $fillable = ['program_id', 'place_id', 'name', 'starts_on', 'ends_on', 'applications_open_at', 'applications_close_at', 'status', 'notes'];

    protected $casts = [
        'status' => CohortStatus::class,
        'starts_on' => 'date',
        'ends_on' => 'date',
        'applications_open_at' => 'datetime',
        'applications_close_at' => 'datetime',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class)->withTrashed();
    }

    /** Campus de la session ; vide = tous les campus où la formation est proposée. */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class);
    }

    /** Les candidatures sont ouvertes : statut « ouvert » et date du jour dans la fenêtre prévue. */
    public function acceptsApplications(): bool
    {
        return $this->status === CohortStatus::OPEN
            && ($this->applications_open_at === null || $this->applications_open_at->lte(now()))
            && ($this->applications_close_at === null || $this->applications_close_at->gte(now()));
    }
}
