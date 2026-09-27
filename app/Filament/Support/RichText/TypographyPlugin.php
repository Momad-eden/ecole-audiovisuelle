<?php

namespace App\Filament\Support\RichText;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;

/**
 * Mise en forme « comme un traitement de texte », mais guidée : polices, tailles et couleurs
 * choisies dans la charte du site, pour que le texte reste beau et lisible sur mobile.
 */
class TypographyPlugin implements RichContentPlugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    /** Éditeur de texte riche de l'administration, avec la barre d'outils complète. */
    public static function editor(string $name, string $label = 'Texte', bool $images = false): RichEditor
    {
        return RichEditor::make($name)->label($label)
            ->plugins([static::make()])
            ->textColors(static::colors())
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'strike', 'link'],
                [
                    ToolbarButtonGroup::make('Police', ['fontDefault', ...array_map(fn ($key) => 'font_'.$key, array_keys(FontMark::choices()))])
                        ->textualButtons()->icon(Heroicon::Language),
                    ToolbarButtonGroup::make('Taille', ['sizeNormal', ...array_map(fn ($key) => 'size_'.$key, array_keys(SizeMark::choices()))])
                        ->textualButtons()->icon(Heroicon::ArrowsPointingOut),
                    'textColor',
                ],
                ['h2', 'h3'],
                ['alignStart', 'alignCenter', 'alignEnd'],
                ['bulletList', 'orderedList', 'blockquote'],
                ...($images ? [['attachFiles']] : []),
                ['clearFormatting', 'undo', 'redo'],
            ]);
    }

    /** @return array<string, TextColor> Couleurs de la charte (clé enregistrée dans data-color). */
    public static function colors(): array
    {
        return [
            'brand' => TextColor::make('Orange EMSI', '#c2410c', darkColor: '#ff7a1a'),
            'violet' => TextColor::make('Violet', '#6d4fe0', darkColor: '#8b6cff'),
            'cyan' => TextColor::make('Bleu lumière', '#0e7490', darkColor: '#3fd0ff'),
            'magenta' => TextColor::make('Magenta', '#be185d', darkColor: '#ff4fa3'),
            'gold' => TextColor::make('Or', '#a16207', darkColor: '#ffb020'),
            'red' => TextColor::make('Rouge', '#b91c1c', darkColor: '#ff3b30'),
        ];
    }

    public function getTipTapPhpExtensions(): array
    {
        return [app(FontMark::class), app(SizeMark::class)];
    }

    public function getTipTapJsExtensions(): array
    {
        return [FilamentAsset::getScriptSrc('rich-content-plugins/typography')];
    }

    public function getEditorTools(): array
    {
        $tools = [
            RichEditorTool::make('fontDefault')->label('Texte (par défaut)')->icon(Heroicon::Bars3BottomLeft)
                ->jsHandler("\$getEditor()?.chain().focus().unsetMark('emsiFont').run()"),
            RichEditorTool::make('sizeNormal')->label('Normale')->icon(Heroicon::Minus)
                ->jsHandler("\$getEditor()?.chain().focus().unsetMark('emsiSize').run()"),
        ];

        foreach (['emsiFont' => ['font_', FontMark::choices(), Heroicon::Language], 'emsiSize' => ['size_', SizeMark::choices(), Heroicon::ArrowsPointingOut]] as $mark => [$prefix, $choices, $icon]) {
            foreach ($choices as $value => $label) {
                $tools[] = RichEditorTool::make($prefix.$value)->label($label)->icon($icon)
                    ->jsHandler("\$getEditor()?.chain().focus().setMark('{$mark}', { value: '{$value}' }).run()")
                    ->activeJsExpression("\$getEditor()?.isActive('{$mark}', { value: '{$value}' })");
            }
        }

        return $tools;
    }

    public function getEditorActions(): array
    {
        return [];
    }
}
