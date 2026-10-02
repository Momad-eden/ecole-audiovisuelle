<?php

namespace App\Models;

use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory, RevalidatesFrontend;

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

    /** Titre d'un groupe de partenaires sur le site (pluriel), par langue. */
    public const GROUPS = [
        'fr' => ['co_organizer' => 'Porteurs du programme', 'institutional' => 'Partenaires institutionnels', 'technical' => 'Partenaires techniques', 'media' => 'Médias', 'venue' => 'Lieux partenaires'],
        'en' => ['co_organizer' => 'Programme leads', 'institutional' => 'Institutional partners', 'technical' => 'Technical partners', 'media' => 'Media', 'venue' => 'Partner venues'],
    ];

    public static function groupLabel(?string $category, string $locale): ?string
    {
        return $category === null ? null : (self::GROUPS[$locale][$category] ?? self::GROUPS['fr'][$category] ?? null);
    }
}
