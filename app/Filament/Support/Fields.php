<?php

namespace App\Filament\Support;

use App\Enums\PublicationStatus;
use App\Models\Place;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

/** Champs réutilisés dans toute l'administration (publication, images, SEO). */
final class Fields
{
    public static function publication(): Section
    {
        return Section::make('Publication')->columns(2)->schema([
            Select::make('status')->label('État')->options(PublicationStatus::class)
                ->default(PublicationStatus::DRAFT)->required()
                ->helperText('« Brouillon » : invisible sur le site. « Publié » : visible.'),
            DateTimePicker::make('published_at')->label('Publier à partir du')->seconds(false)
                ->helperText('Laisser vide pour publier immédiatement.'),
        ]);
    }

    /** Image recadrable + texte alternatif obligatoire dès qu'une image est présente. */
    public static function image(string $field = 'cover_image', string $directory = 'images', string $label = 'Image', string $ratio = '16:9'): array
    {
        return [
            FileUpload::make($field)->label($label)
                ->image()->disk('public')->directory($directory)->visibility('public')
                ->imageEditor()->imageEditorAspectRatioOptions([$ratio, '1:1', null])
                ->maxSize(8192)
                ->helperText('JPG, PNG ou WebP, 8 Mo maximum. Le site génère automatiquement les tailles adaptées.'),
            TextInput::make(str_replace('_image', '', $field).'_alt')->label('Description de l\'image (accessibilité)')
                ->maxLength(200)
                ->required(fn ($get) => filled($get($field)))
                ->helperText('Décrivez ce que montre l\'image, pour les personnes malvoyantes.'),
        ];
    }

    public static function seo(): Section
    {
        return Section::make('Référencement (Google, réseaux sociaux)')->collapsed()->columns(1)->schema([
            TextInput::make('seo.title')->label('Titre affiché dans Google')->maxLength(70),
            Textarea::make('seo.description')->label('Description affichée dans Google')->maxLength(160)->rows(2),
        ]);
    }

    /**
     * Choix du campus (caisse, étudiant). Masqué pour le personnel rattaché à un campus
     * (le sien s'applique) et quand l'école n'a qu'un campus.
     */
    public static function campus(string $field = 'place_id', string $label = 'Campus'): Select
    {
        $campuses = fn () => Place::campuses()->orderBy('position')->pluck('name', 'id')->all();

        return Select::make($field)->label($label)
            ->options($campuses)
            ->default(fn () => auth()->user()?->place_id ?? (count($campuses()) === 1 ? array_key_first($campuses()) : null))
            ->visible(fn () => ! auth()->user()?->place_id && count($campuses()) > 1)
            ->required(fn () => ! auth()->user()?->place_id && count($campuses()) > 1);
    }
}
