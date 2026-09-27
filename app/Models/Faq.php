<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ['group', 'question', 'answer', 'position', 'is_visible'];

    protected $casts = ['is_visible' => 'boolean'];

    public const GROUPS = [
        'general' => 'Général',
        'applications' => 'Candidatures',
        'professional' => 'Espace Professionnels',
        'vae' => 'VAE',
    ];
}
