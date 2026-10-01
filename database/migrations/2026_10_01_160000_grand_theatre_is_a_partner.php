<?php

use App\Models\Page;
use App\Models\Partner;
use Illuminate\Database\Migrations\Migration;

/**
 * Le Grand Théâtre National n'est pas porteur du projet : c'est un partenaire comme les autres (partenaire
 * institutionnel, partenaire du campus de Dakar). Seuls les textes encore d'origine sont modifiés.
 */
return new class extends Migration
{
    private const OLD_DESCRIPTION = 'Co-porteur du programme : met à disposition ses salles, plateaux et équipements techniques.';

    public function up(): void
    {
        Partner::where('category', 'co_organizer')->where('name', 'like', 'Grand Théâtre National%')->get()
            ->each(fn (Partner $partner) => $partner->update([
                'category' => 'institutional',
                'description' => $partner->description === self::OLD_DESCRIPTION
                    ? 'Partenaire du campus de Dakar : met à disposition ses salles, plateaux et équipements techniques.'
                    : $partner->description,
            ]));

        foreach (Page::all() as $page) {
            $page->forceFill(['blocks' => $this->blocks($page->blocks), 'draft_blocks' => $this->blocks($page->draft_blocks)]);
            if ($page->isDirty()) {
                $page->save();
            }
        }
    }

    /** Blocs « Partenaires » : plus de titre « porteurs », plus de filtre sur les seuls porteurs. */
    private function blocks(?array $blocks): ?array
    {
        if ($blocks === null) {
            return null;
        }

        return array_map(function (array $block) {
            if (($block['type'] ?? null) !== 'partners') {
                return $block;
            }
            $title = $block['data']['title'] ?? null;
            if (in_array($title, ['Porteurs et partenaires', 'Notre partenaire, le Grand Théâtre National'], true)) {
                $block['data']['title'] = 'Nos partenaires';
            }
            $categories = $block['data']['categories'] ?? [];
            if (in_array('co_organizer', $categories, true)) {
                $block['data']['categories'] = array_values(array_unique([...array_diff($categories, ['co_organizer']), 'institutional']));
            }

            return $block;
        }, $blocks);
    }

    public function down(): void
    {
        // Pas de retour automatique.
    }
};
