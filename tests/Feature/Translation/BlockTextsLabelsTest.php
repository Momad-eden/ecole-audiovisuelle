<?php

namespace Tests\Feature\Translation;

use App\Filament\Support\PageBlocks;
use App\Support\Translation\BlockTexts;
use Filament\Forms\Components\Builder\Block;
use Tests\TestCase;

/** Les libellés de blocs utilisés par BlockTexts::describe() restent alignés sur le catalogue PageBlocks. */
class BlockTextsLabelsTest extends TestCase
{
    public function test_block_labels_match_page_blocks(): void
    {
        $catalogue = collect(PageBlocks::all())
            ->mapWithKeys(fn (Block $block) => [$block->getName() => $block->getLabel()])
            ->sortKeys()->all();

        $labels = BlockTexts::BLOCK_LABELS;
        ksort($labels);

        $this->assertSame($catalogue, $labels);
    }
}
