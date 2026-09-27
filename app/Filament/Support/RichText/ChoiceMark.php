<?php

namespace App\Filament\Support\RichText;

use Tiptap\Core\Mark;

/**
 * Marque de texte dont la valeur est choisie dans une liste fermée (police, taille).
 * Enregistrée en <span data-…="valeur"> : toute autre valeur est ignorée à la relecture,
 * si bien qu'aucun style libre ne peut entrer dans le contenu.
 */
abstract class ChoiceMark extends Mark
{
    /** Attribut HTML porteur de la valeur (ex. data-font). */
    abstract protected static function attribute(): string;

    /** @return array<string, string> valeur => libellé */
    abstract public static function choices(): array;

    public function parseHTML(): array
    {
        return [[
            'tag' => 'span['.static::attribute().']',
            'getAttrs' => fn ($DOMNode): bool => array_key_exists((string) $DOMNode->getAttribute(static::attribute()), static::choices()),
        ]];
    }

    public function addAttributes(): array
    {
        return [
            'value' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute(static::attribute()) ?: null,
                'renderHTML' => fn ($attributes) => [static::attribute() => is_array($attributes) ? ($attributes['value'] ?? null) : ($attributes->value ?? null)],
            ],
        ];
    }

    public function renderHTML($mark, $HTMLAttributes = []): array
    {
        return ['span', $HTMLAttributes, 0];
    }
}
