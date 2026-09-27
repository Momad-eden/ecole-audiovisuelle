<?php

namespace App\Models;

use App\Models\Concerns\HasPublication;
use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Salle permanente du musée (Son, Lumière, Image, Visuel…). */
class Room extends Model
{
    use HasFactory, HasPublication, HasUniqueSlug;

    protected $fillable = ['name', 'slug', 'tagline', 'intro', 'accent_color', 'cover_image', 'cover_alt', 'position', 'status', 'published_at'];

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
        return $this->hasMany(Track::class);
    }
}
