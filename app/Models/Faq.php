<?php

namespace App\Models;

use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use RevalidatesFrontend;

    protected $fillable = ['group', 'question', 'answer', 'position', 'is_visible'];

    protected $casts = ['is_visible' => 'boolean'];

    public const GROUPS = [
        'general' => 'Général',
        'applications' => 'Candidatures',
        'professional' => 'Espace Professionnels',
        'vae' => 'VAE',
    ];
}
