<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Filière technique (Son, Technicien Lumière, Régie Générale…). */
class Track extends Model
{
    use HasFactory, HasTranslations, HasUniqueSlug, RevalidatesFrontend;

    protected array $translatable = ['name', 'short_name', 'summary', 'description', 'skills', 'outcomes'];

    protected $fillable = ['name', 'slug', 'short_name', 'summary', 'description', 'skills', 'outcomes', 'room_id', 'position', 'is_active'];

    protected $casts = [
        'skills' => 'array',
        'outcomes' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function slugSource(): string
    {
        return 'name';
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class);
    }
}
