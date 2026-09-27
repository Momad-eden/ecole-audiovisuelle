<?php

namespace Tests\Feature\Admin;

use App\Filament\Support\RichText\TypographyPlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Tests\TestCase;

/** Mise en forme guidée de l'éditeur : seules les polices, tailles et couleurs de la charte survivent. */
class RichTextFormattingTest extends TestCase
{
    private function render(string $html): string
    {
        return RichContentRenderer::make($html)->plugins([TypographyPlugin::make()])->textColors(TypographyPlugin::colors())->toHtml();
    }

    public function test_listed_fonts_and_sizes_are_kept(): void
    {
        $html = $this->render('<p><span data-font="serif">Élégant</span> et <span data-size="xl">grand</span></p>');

        $this->assertStringContainsString('data-font="serif"', $html);
        $this->assertStringContainsString('data-size="xl"', $html);
    }

    public function test_unlisted_fonts_sizes_and_inline_styles_are_dropped(): void
    {
        $html = $this->render('<p><span data-font="comic-sans" style="font-size:90px">A</span><span data-size="999px">B</span></p>');

        $this->assertStringNotContainsString('comic-sans', $html);
        $this->assertStringNotContainsString('999px', $html);
        $this->assertStringNotContainsString('90px', $html);
        $this->assertStringContainsString('A', $html);
        $this->assertStringContainsString('B', $html);
    }

    public function test_brand_colors_are_stored_by_name(): void
    {
        $html = $this->render('<p><span class="color" data-color="brand">Orange</span></p>');

        $this->assertStringContainsString('data-color="brand"', $html);
        $this->assertArrayHasKey('violet', TypographyPlugin::colors());
    }
}
