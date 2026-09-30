<?php

namespace App\Models;

use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory, RevalidatesFrontend;

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
    ];

    /** site_version : repère posé par les commandes de mise à niveau (emsi:site-v4 → 4), jamais par le formulaire. */
    protected $casts = ['site_version' => 'integer'];

    /** Paramètres uniques du site (créés à la première lecture). */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], ['school_name' => 'EMSI — École des Métiers du Son et de l\'Image']);
    }
}
