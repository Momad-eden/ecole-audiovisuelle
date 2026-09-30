<?php

namespace App\Models;

use App\Enums\PriceUnit;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\RevalidatesFrontend;
use App\Support\Price;
use Illuminate\Database\Eloquent\Model;

/** Pack tout compris (ex. « Pack concert jusqu'à 500 personnes »). */
class RentalPack extends Model
{
    use HasPublication, HasUniqueSlug, RevalidatesFrontend;

    protected $fillable = ['name', 'slug', 'summary', 'capacity', 'contents', 'price_from', 'price_unit', 'image', 'image_alt', 'position', 'status', 'published_at'];

    protected $attributes = ['price_unit' => 'event'];

    protected $casts = ['contents' => 'array', 'price_unit' => PriceUnit::class, 'price_from' => 'integer'];

    protected static function slugSource(): string
    {
        return 'name';
    }

    /** Prix affiché ; français par défaut (admin), langue de la requête pour l'API publique. */
    public function priceLabel(string $locale = 'fr'): string
    {
        return Price::label($this->price_from, $this->price_unit, $locale);
    }
}
