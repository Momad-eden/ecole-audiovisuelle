<?php

namespace App\Models;

use App\Enums\Activity;
use App\Enums\PriceUnit;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\RevalidatesFrontend;
use App\Support\Price;
use Illuminate\Database\Eloquent\Model;

/** Service proposé par le studio, l'événementiel ou l'Espace Habib Faye. */
class Service extends Model
{
    use HasPublication, HasTranslations, HasUniqueSlug, RevalidatesFrontend;

    protected array $translatable = ['name', 'summary', 'description', 'image_alt'];

    protected $fillable = ['activity', 'name', 'slug', 'summary', 'description', 'price_from', 'price_unit', 'icon', 'image', 'image_alt', 'position', 'status', 'published_at'];

    protected $casts = ['activity' => Activity::class, 'price_unit' => PriceUnit::class, 'price_from' => 'integer'];

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
