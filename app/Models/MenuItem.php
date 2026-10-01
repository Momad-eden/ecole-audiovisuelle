<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use HasTranslations, RevalidatesFrontend;

    public const LOCATIONS = [
        'main' => 'Menu principal',
        'footer' => 'Pied de page',
        'legal' => 'Liens légaux',
    ];

    protected array $translatable = ['label', 'description'];

    protected $fillable = ['location', 'parent_id', 'label', 'description', 'image', 'url', 'is_button', 'position', 'is_visible'];

    protected $casts = [
        'is_button' => 'boolean',
        'is_visible' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Seul le menu principal a des menus déroulants : un lien passé au pied de page
        // ou aux liens légaux quitte son ancien sous-menu.
        static::saving(function (MenuItem $item) {
            if ($item->location !== 'main') {
                $item->parent_id = null;
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }
}
