<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

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

    /** Paramètres uniques du site (créés à la première lecture). */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], ['school_name' => 'EMSI — École des Métiers du Son et de l\'Image']);
    }
}
