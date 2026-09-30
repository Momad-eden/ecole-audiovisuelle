<?php

namespace App\Models;

use App\Enums\EquipmentUsage;
use App\Enums\PriceUnit;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\RevalidatesFrontend;
use App\Support\Price;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Matériel : à louer (Impact Live Events) ou équipement du studio. */
class EquipmentItem extends Model
{
    use HasPublication, HasUniqueSlug, RevalidatesFrontend;

    protected $fillable = ['equipment_category_id', 'name', 'slug', 'brand', 'usage', 'summary', 'description', 'specs', 'image', 'image_alt', 'gallery', 'quantity', 'price_from', 'price_unit', 'is_featured', 'position', 'status', 'published_at'];

    protected $attributes = ['usage' => 'rental', 'price_unit' => 'day'];

    protected $casts = [
        'usage' => EquipmentUsage::class,
        'price_unit' => PriceUnit::class,
        'specs' => 'array',
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'price_from' => 'integer',
        'quantity' => 'integer',
    ];

    protected static function slugSource(): string
    {
        return 'name';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }

    /** Prix affiché ; français par défaut (admin), langue de la requête pour l'API publique. */
    public function priceLabel(string $locale = 'fr'): string
    {
        return Price::label($this->price_from, $this->price_unit, $locale);
    }
}
