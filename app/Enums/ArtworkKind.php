<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ArtworkKind: string implements HasLabel
{
    case AUDIO = 'audio';
    case VIDEO = 'video';
    case IMAGE = 'image';
    case SERIES = 'series';
    case LIVE = 'live';

    public function getLabel(): string
    {
        return match ($this) {
            self::AUDIO => 'Son',
            self::VIDEO => 'Vidéo',
            self::IMAGE => 'Image',
            self::SERIES => 'Série',
            self::LIVE => 'Spectacle / captation live',
        };
    }

    /** Libellé dans la langue de l'API publique (`en` : anglais) ; en français, identique à getLabel() (admin). */
    public function labelFor(string $locale): string
    {
        if ($locale !== 'en') {
            return $this->getLabel();
        }

        return match ($this) {
            self::AUDIO => 'Sound',
            self::VIDEO => 'Video',
            self::IMAGE => 'Image',
            self::SERIES => 'Series',
            self::LIVE => 'Live show / live recording',
        };
    }

    public function label(): string
    {
        return $this->getLabel();
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
