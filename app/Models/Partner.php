<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'website',
        'logo',
        'description',
        'is_active',
        'position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const CATEGORIES = [
        'co_organizer' => 'Porteur du programme',
        'institutional' => 'Partenaire institutionnel',
        'technical' => 'Partenaire technique',
        'media' => 'Média',
        'venue' => 'Lieu',
    ];
}
