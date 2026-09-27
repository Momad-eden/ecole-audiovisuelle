<?php

namespace App\Models;

use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use RevalidatesFrontend;

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
