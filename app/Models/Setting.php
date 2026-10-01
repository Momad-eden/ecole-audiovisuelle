<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory, HasTranslations, RevalidatesFrontend;

    protected array $translatable = ['description', 'opening_hours', 'seo_title', 'seo_description'];

    protected $fillable = [
        'school_name',
        'description',
        'phone',
        'email',
        'address',
        'opening_hours',
        'map_url',
        'seo_title',
        'seo_description',
        'website',
        'logo',
        'facebook',
        'instagram',
        'youtube',
        'tiktok',
        'linkedin',
        'twitter',
        'whatsapp',
        'auto_translate',
        'translation_glossary',
    ];

    /** Noms à ne jamais traduire et termes imposés (spec R2 §3.2) ; même liste que la migration. */
    public const DEFAULT_GLOSSARY = [
        ['fr' => 'EMSI', 'en' => 'EMSI'],
        ['fr' => 'Impact Live Studio', 'en' => 'Impact Live Studio'],
        ['fr' => 'Centre culturel Habib Faye', 'en' => 'Habib Faye Cultural Centre'],
        ['fr' => 'Grand Théâtre National Doudou Ndiaye Coumba Rose', 'en' => 'Grand Théâtre National Doudou Ndiaye Coumba Rose'],
        ['fr' => 'Habib Faye', 'en' => 'Habib Faye'],
        ['fr' => 'Boubacar Tall', 'en' => 'Boubacar Tall'],
        ['fr' => 'Dakar', 'en' => 'Dakar'],
        ['fr' => 'Saint-Louis', 'en' => 'Saint-Louis'],
        ['fr' => 'VAE', 'en' => 'Recognition of Prior Learning (VAE)'],
    ];

    protected $attributes = ['auto_translate' => true];

    /** site_version : repère posé par les commandes de mise à niveau (emsi:site-v4 → 4), jamais par le formulaire. */
    protected $casts = ['site_version' => 'integer', 'auto_translate' => 'boolean', 'translation_glossary' => 'array'];

    /** Paramètres uniques du site (créés à la première lecture). */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'school_name' => 'EMSI — École des Métiers du Son et de l\'Image',
            'translation_glossary' => self::DEFAULT_GLOSSARY,
        ]);
    }
}
