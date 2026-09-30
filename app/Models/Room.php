<?php

namespace App\Models;

use App\Models\Concerns\HasPublication;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Univers de l'école (Son, Image, Infographie & design, Scène, Cinéma…), anciennement « salle » du musée. */
class Room extends Model
{
    use HasFactory, HasPublication, HasTranslations, HasUniqueSlug, RevalidatesFrontend;

    /** Signature visuelle animée de chaque univers sur le site. */
    public const VISUALS = [
        'sound' => 'Son (spectre sonore)',
        'image' => 'Image (viseur de caméra)',
        'design' => 'Design (tracé vectoriel)',
        'stage' => 'Scène (faisceaux de lumière)',
        'cinema' => 'Cinéma (pellicule)',
    ];

    protected array $translatable = ['name', 'tagline', 'intro', 'cover_alt'];

    protected $fillable = ['name', 'slug', 'tagline', 'intro', 'accent_color', 'visual', 'is_upcoming', 'cover_image', 'cover_alt', 'position', 'status', 'published_at'];

    protected $casts = [
        'is_upcoming' => 'boolean',
    ];

    protected static function slugSource(): string
    {
        return 'name';
    }

    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class)->orderBy('position');
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class)->orderBy('position');
    }
}
