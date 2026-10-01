<?php

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\Setting;
use App\Services\Translation\DeepLGlossary;
use App\Services\Translation\Translator;
use App\Support\CentreCulturelRename;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * « Maison Habib Faye » devient « Centre culturel Habib Faye » dans le contenu enregistré : textes et liens
 * des pages (version en ligne et brouillon), adresses des pages (/centre-culturel/…), menus, redirections de
 * l'admin et lexique de traduction. Les anciennes adresses redirigent (next.config.ts). Le français change :
 * la traduction anglaise des textes concernés repart en file d'elle-même.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (Page::all() as $page) {
            $page->forceFill([
                'slug' => CentreCulturelRename::slug($page->slug),
                'title' => CentreCulturelRename::text((string) $page->title),
                'seo' => CentreCulturelRename::deep($page->seo),
                'blocks' => CentreCulturelRename::deep($page->blocks),
                'draft_blocks' => CentreCulturelRename::deep($page->draft_blocks),
            ]);
            if ($page->isDirty()) {
                $page->save();
            }
        }

        foreach (MenuItem::all() as $item) {
            $item->fill([
                'label' => CentreCulturelRename::text((string) $item->label),
                'description' => $item->description === null ? null : CentreCulturelRename::text($item->description),
                'url' => CentreCulturelRename::text((string) $item->url),
            ]);
            if ($item->isDirty()) {
                $item->save();
            }
        }

        foreach (Redirect::all() as $redirect) {
            $redirect->fill(['to_path' => CentreCulturelRename::text((string) $redirect->to_path)]);
            if ($redirect->isDirty()) {
                $redirect->save();
            }
        }

        $setting = Setting::current();
        $glossary = array_map(fn (array $pair) => str_contains($pair['fr'] ?? '', 'Maison') || str_contains($pair['fr'] ?? '', 'Centre culturel Habib Faye')
            ? ['fr' => 'Centre culturel Habib Faye', 'en' => 'Habib Faye Cultural Centre']
            : $pair, $setting->translation_glossary ?? []);
        if ($glossary !== ($setting->translation_glossary ?? [])) {
            $setting->forceFill(['translation_glossary' => $glossary])->save();
            // Lexique envoyé à DeepL tout de suite si une clé est configurée ; sinon à la prochaine traduction.
            if (app(Translator::class)->isAvailable()) {
                try {
                    app(DeepLGlossary::class)->sync($glossary);
                } catch (Throwable $e) {
                    Log::warning('Lexique non envoyé à DeepL : '.$e->getMessage());
                }
            }
        }
    }

    public function down(): void
    {
        // Pas de retour automatique : l'équipe a pu retoucher les textes depuis.
    }
};
