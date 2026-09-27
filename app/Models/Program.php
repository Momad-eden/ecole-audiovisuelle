<?php

namespace App\Models;

use App\Enums\Audience;
use App\Enums\ProgramKind;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/** Formation de l'école ou programme professionnel (Volet 1, BTS par la VAE…). */
class Program extends Model
{
    use HasFactory, HasPublication, HasUniqueSlug, RevalidatesFrontend, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'audience', 'kind', 'level_label', 'duration_label', 'summary', 'description',
        'skills', 'outcomes', 'prerequisites', 'equipment', 'cover_image', 'cover_alt', 'seo', 'position', 'status', 'published_at',
    ];

    protected $casts = [
        'audience' => Audience::class,
        'kind' => ProgramKind::class,
        'skills' => 'array',
        'outcomes' => 'array',
        'prerequisites' => 'array',
        'equipment' => 'array',
        'seo' => 'array',
    ];

    public function cohorts(): HasMany
    {
        return $this->hasMany(Cohort::class)->orderByDesc('starts_on');
    }

    public function offerings(): HasManyThrough
    {
        return $this->hasManyThrough(Offering::class, Cohort::class);
    }
}
