<?php

namespace App\Models;

use App\Enums\PlaceKind;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Lieu : campus de l'EMSI, studio ou centre culturel (coordonnées de chaque activité). */
class Place extends Model
{
    use HasPublication, HasUniqueSlug, RevalidatesFrontend;

    protected $fillable = ['name', 'slug', 'kind', 'city', 'address', 'phone', 'whatsapp', 'email', 'map_url', 'opening_hours', 'description', 'image', 'image_alt', 'position', 'status', 'published_at'];

    protected $casts = ['kind' => PlaceKind::class];

    protected static function slugSource(): string
    {
        return 'name';
    }

    public function scopeCampuses(Builder $query): Builder
    {
        return $query->where('kind', PlaceKind::CAMPUS);
    }
}
