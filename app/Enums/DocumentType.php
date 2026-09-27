<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DocumentType: string implements HasLabel
{
    case ID_CARD = 'id_card';
    case DIPLOMA = 'diploma';
    case CV = 'cv';
    case EXPERIENCE_CERTIFICATE = 'experience_certificate';
    case PORTFOLIO = 'portfolio';
    case COVER_LETTER = 'cover_letter';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::ID_CARD => 'Pièce d\'identité',
            self::DIPLOMA => 'Diplôme (CPS, CS…)',
            self::CV => 'CV',
            self::EXPERIENCE_CERTIFICATE => 'Attestation d\'expérience',
            self::PORTFOLIO => 'Portfolio',
            self::COVER_LETTER => 'Lettre de motivation',
            self::OTHER => 'Autre pièce',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
