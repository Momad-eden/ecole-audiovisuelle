<?php

namespace App\Models;

use App\Enums\TranslationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/** Traduction d'un champ d'une fiche ; `value` est du texte brut (JSON pour blocs et tableaux). */
class Translation extends Model
{
    protected $fillable = [
        'translatable_type', 'translatable_id', 'field', 'locale', 'value', 'source_hash', 'status',
        'previous_value', 'translated_at', 'reviewed_at', 'reviewed_by',
    ];

    protected $casts = [
        'status' => TranslationStatus::class,
        'translated_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
