<?php

namespace App\Models;

use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use RevalidatesFrontend;

    public const LOCATIONS = [
        'main' => 'Menu principal',
        'footer' => 'Pied de page',
        'legal' => 'Liens légaux',
    ];

    protected $fillable = ['location', 'parent_id', 'label', 'url', 'is_button', 'position', 'is_visible'];

    protected $casts = [
        'is_button' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }
}
