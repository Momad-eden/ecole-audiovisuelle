<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    public const LOCATIONS = [
        'main' => 'Menu principal',
        'footer' => 'Pied de page',
        'legal' => 'Liens légaux',
    ];

    protected $fillable = ['location', 'label', 'url', 'is_button', 'position', 'is_visible'];

    protected $casts = [
        'is_button' => 'boolean',
        'is_visible' => 'boolean',
    ];
}
