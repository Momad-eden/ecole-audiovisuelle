<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProgramKind: string implements HasLabel
{
    case INITIAL_TRAINING = 'initial_training';
    case CERTIFICATE = 'certificate';
    case SHORT_COURSE = 'short_course';
    case INTENSIVE_UPSKILLING = 'intensive_upskilling';
    case VAE_BTS = 'vae_bts';

    public function getLabel(): string
    {
        return match ($this) {
            self::INITIAL_TRAINING => 'Formation initiale',
            self::CERTIFICATE => 'Formation certifiante',
            self::SHORT_COURSE => 'Stage / atelier court',
            self::INTENSIVE_UPSKILLING => 'Perfectionnement intensif',
            self::VAE_BTS => 'Certification de niveau BTS par la VAE',
        };
    }

    /** Libellé dans la langue de l'API publique (`en` : anglais) ; en français, identique à getLabel() (admin). */
    public function labelFor(string $locale): string
    {
        if ($locale !== 'en') {
            return $this->getLabel();
        }

        return match ($this) {
            self::INITIAL_TRAINING => 'Initial training',
            self::CERTIFICATE => 'Certified programme',
            self::SHORT_COURSE => 'Short course / workshop',
            self::INTENSIVE_UPSKILLING => 'Intensive upskilling',
            self::VAE_BTS => 'BTS-level certification through recognition of prior learning (VAE)',
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
