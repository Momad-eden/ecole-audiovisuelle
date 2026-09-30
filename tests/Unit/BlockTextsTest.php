<?php

namespace Tests\Unit;

use App\Support\Translation\BlockTexts;
use PHPUnit\Framework\TestCase;

/** Textes des blocs de page : extraction des seules feuilles texte et réapplication de l'anglais. */
class BlockTextsTest extends TestCase
{
    /** Page réaliste (noms de champs de PageBlocks). */
    private function blocks(): array
    {
        return [
            ['type' => 'hero', 'data' => [
                'layout' => 'cinema',
                'eyebrow' => 'École des métiers du spectacle',
                'title' => 'Faites vivre la scène',
                'subtitle' => 'Son, lumière, vidéo : apprenez en conditions réelles.',
                'image' => 'pages/hero.jpg',
                'image_alt' => 'Une console de mixage éclairée',
                'video_loop' => null,
                'accent' => '#ff8800',
                'slides' => [
                    ['image' => 'pages/s1.jpg', 'image_alt' => 'Scène du Grand Théâtre', 'eyebrow' => 'Dakar', 'title' => 'Le Grand Théâtre', 'link_label' => 'Découvrir', 'link_url' => '/emsi/lieux'],
                    ['image' => 'pages/s2.jpg', 'image_alt' => 'Studio', 'eyebrow' => '', 'title' => 'Le studio', 'link_label' => null, 'link_url' => null],
                ],
                'facts' => [
                    ['value' => '5', 'label' => 'filières'],
                    ['value' => '154 m²', 'label' => 'de studio'],
                ],
                'buttons' => [
                    ['label' => 'Candidater', 'url' => '/candidater', 'style' => 'primary'],
                ],
            ]],
            ['type' => 'domains', 'data' => [
                'panels' => [
                    ['domain' => 'school', 'eyebrow' => 'École · Dakar', 'title' => 'EMSI', 'text' => 'Former aux métiers.', 'image' => 'pages/p1.jpg', 'image_alt' => 'Étudiants', 'url' => '/emsi', 'label' => 'Découvrir'],
                    ['domain' => 'studio', 'eyebrow' => 'Studio', 'title' => 'Impact Live Studio', 'text' => 'Enregistrer.', 'image' => null, 'image_alt' => null, 'url' => '/studio', 'label' => 'Découvrir'],
                    ['domain' => 'culture', 'eyebrow' => 'Centre culturel · Saint-Louis', 'title' => 'Maison de la culture', 'text' => 'Transmettre.', 'image' => 'pages/p3.jpg', 'image_alt' => 'Façade', 'url' => '/culture', 'label' => 'Visiter'],
                ],
                'intro' => 'La culture comme héritage, l\'art comme métier',
            ]],
            ['type' => 'text', 'data' => [
                'title' => 'Notre histoire',
                'body' => '<p>Fondée en 2010, <a href="/emsi/histoire">lire la suite</a>.</p>',
            ]],
            ['type' => 'hero', 'data' => [
                'layout' => 'studio',
                'title' => 'Le son qui vous ressemble',
                'highlight' => 'son',
                'image' => 'pages/studio.jpg',
                'image_alt' => 'La régie du studio',
                'hotspots' => [
                    ['x' => 12.5, 'y' => 40, 'label' => 'Console SSL'],
                    ['x' => 70, 'y' => 22.3, 'label' => 'Enceintes Genelec'],
                ],
                'tracks' => [
                    ['file' => 'pages/audio/a.mp3', 'title' => 'Nuit de Dakar', 'credits' => 'Mixé et masterisé ici'],
                ],
                'words' => ['le son', 'la lumière'],
                'buttons' => [],
            ]],
            ['type' => 'faq', 'data' => ['title' => 'Questions fréquentes', 'group' => 'admissions']],
            ['type' => 'cards', 'data' => [
                'title' => 'Nos piliers',
                'items' => [
                    ['icon' => 'star', 'title' => 'Pratique', 'text' => 'Sur scène dès la 1re année.', 'url' => '/emsi'],
                    ['icon' => 'bolt', 'title' => 'Réseau', 'text' => '', 'url' => null],
                ],
            ]],
            ['type' => 'stats', 'data' => [
                'title' => 'En chiffres',
                'items' => [
                    ['value' => '300', 'label' => 'diplômés', 'detail' => 'depuis 2010'],
                    ['value' => '98 %', 'label' => 'insertion', 'detail' => null],
                ],
            ]],
        ];
    }

    public function test_extract_keeps_only_text_leaves_with_dotted_paths(): void
    {
        $texts = BlockTexts::extract($this->blocks());

        $this->assertSame([
            '0.data.eyebrow' => 'École des métiers du spectacle',
            '0.data.title' => 'Faites vivre la scène',
            '0.data.subtitle' => 'Son, lumière, vidéo : apprenez en conditions réelles.',
            '0.data.image_alt' => 'Une console de mixage éclairée',
            '0.data.slides.0.image_alt' => 'Scène du Grand Théâtre',
            '0.data.slides.0.eyebrow' => 'Dakar',
            '0.data.slides.0.title' => 'Le Grand Théâtre',
            '0.data.slides.0.link_label' => 'Découvrir',
            '0.data.slides.1.image_alt' => 'Studio',
            '0.data.slides.1.title' => 'Le studio',
            '0.data.facts.0.label' => 'filières',
            '0.data.facts.1.value' => '154 m²',
            '0.data.facts.1.label' => 'de studio',
            '0.data.buttons.0.label' => 'Candidater',
            '1.data.panels.0.eyebrow' => 'École · Dakar',
            '1.data.panels.0.title' => 'EMSI',
            '1.data.panels.0.text' => 'Former aux métiers.',
            '1.data.panels.0.image_alt' => 'Étudiants',
            '1.data.panels.0.label' => 'Découvrir',
            '1.data.panels.1.eyebrow' => 'Studio',
            '1.data.panels.1.title' => 'Impact Live Studio',
            '1.data.panels.1.text' => 'Enregistrer.',
            '1.data.panels.1.label' => 'Découvrir',
            '1.data.panels.2.eyebrow' => 'Centre culturel · Saint-Louis',
            '1.data.panels.2.title' => 'Maison de la culture',
            '1.data.panels.2.text' => 'Transmettre.',
            '1.data.panels.2.image_alt' => 'Façade',
            '1.data.panels.2.label' => 'Visiter',
            '1.data.intro' => 'La culture comme héritage, l\'art comme métier',
            '2.data.title' => 'Notre histoire',
            '2.data.body' => '<p>Fondée en 2010, <a href="/emsi/histoire">lire la suite</a>.</p>',
            '3.data.title' => 'Le son qui vous ressemble',
            '3.data.highlight' => 'son',
            '3.data.image_alt' => 'La régie du studio',
            '3.data.hotspots.0.label' => 'Console SSL',
            '3.data.hotspots.1.label' => 'Enceintes Genelec',
            '3.data.tracks.0.title' => 'Nuit de Dakar',
            '3.data.tracks.0.credits' => 'Mixé et masterisé ici',
            '3.data.words.0' => 'le son',
            '3.data.words.1' => 'la lumière',
            '4.data.title' => 'Questions fréquentes',
            '5.data.title' => 'Nos piliers',
            '5.data.items.0.title' => 'Pratique',
            '5.data.items.0.text' => 'Sur scène dès la 1re année.',
            '5.data.items.1.title' => 'Réseau',
            '6.data.title' => 'En chiffres',
            '6.data.items.0.label' => 'diplômés',
            '6.data.items.1.value' => '98 %',
            '6.data.items.1.label' => 'insertion',
        ], $texts);
    }

    public function test_links_images_colours_and_settings_are_never_extracted(): void
    {
        $texts = BlockTexts::extract($this->blocks());
        $values = array_values($texts);

        foreach (['/emsi/lieux', '/candidater', 'pages/hero.jpg', 'pages/s1.jpg', '#ff8800', 'cinema', 'studio', 'school', 'primary', 'star', 'pages/audio/a.mp3', 'admissions', '5', '300'] as $never) {
            $this->assertNotContains($never, $values, "« {$never} » ne doit pas être extrait");
        }
        foreach (array_keys($texts) as $path) {
            $this->assertDoesNotMatchRegularExpression('/\.(url|link_url|image|images|file|sound|video_loop|accent|layout|type|domain|style|icon|x|y|group)$/', $path);
        }
    }

    public function test_unknown_string_keys_are_ignored_even_nested(): void
    {
        $texts = BlockTexts::extract([
            ['type' => 'weird', 'data' => ['slug' => 'a-b', 'nested' => ['name' => 'Nom', 'question' => 'Pourquoi ?', 'answer' => '<p>Parce que.</p>'], 'title' => 12]],
        ]);

        $this->assertSame([
            '0.data.nested.question' => 'Pourquoi ?',
            '0.data.nested.answer' => '<p>Parce que.</p>',
        ], $texts);
    }

    public function test_apply_replaces_existing_text_leaves_only(): void
    {
        $blocks = $this->blocks();
        $en = BlockTexts::apply($blocks, [
            '0.data.title' => 'Bring the stage to life',
            '0.data.slides.0.title' => 'The Grand Theatre',
            '0.data.buttons.0.label' => 'Apply',
            '0.data.buttons.0.url' => '/apply',          // non-texte : ignoré
            '0.data.accent' => '#000000',                // non-texte : ignoré
            '0.data.slides.4.title' => 'Does not exist',  // absent : ignoré
            '9.data.title' => 'No block',                 // absent : ignoré
            '0.data.caption' => 'New key',                // clé absente : jamais créée
            '3.data.words.1' => 'light',
        ]);

        $this->assertSame('Bring the stage to life', $en[0]['data']['title']);
        $this->assertSame('The Grand Theatre', $en[0]['data']['slides'][0]['title']);
        $this->assertSame('Apply', $en[0]['data']['buttons'][0]['label']);
        $this->assertSame('/candidater', $en[0]['data']['buttons'][0]['url']);
        $this->assertSame('#ff8800', $en[0]['data']['accent']);
        $this->assertCount(2, $en[0]['data']['slides']);
        $this->assertCount(7, $en);
        $this->assertArrayNotHasKey('caption', $en[0]['data']);
        $this->assertSame(['le son', 'light'], $en[3]['data']['words']);
        // le reste est intact
        $this->assertSame($blocks[1], $en[1]);
    }

    public function test_apply_is_idempotent_and_empty_map_is_noop(): void
    {
        $blocks = $this->blocks();
        $this->assertSame($blocks, BlockTexts::apply($blocks, []));
        $this->assertSame($blocks, BlockTexts::applyKeyed($blocks, []));

        $map = ['0.data.title' => 'Bring the stage to life', '2.data.body' => '<p>Founded in 2010, <a href="/emsi/histoire">read more</a>.</p>'];
        $once = BlockTexts::apply($blocks, $map);
        $this->assertSame($once, BlockTexts::apply($once, $map));

        // réappliquer le français extrait redonne la structure d'origine
        $this->assertSame($blocks, BlockTexts::apply($blocks, BlockTexts::extract($blocks)));
    }

    public function test_keyed_uses_type_and_rank_among_same_type(): void
    {
        $keyed = BlockTexts::keyed($this->blocks());

        $this->assertSame('Faites vivre la scène', $keyed['hero#0:title']);
        $this->assertSame('Le Grand Théâtre', $keyed['hero#0:slides.0.title']);
        $this->assertSame('Le son qui vous ressemble', $keyed['hero#1:title']);
        $this->assertSame('la lumière', $keyed['hero#1:words.1']);
        $this->assertSame('La culture comme héritage, l\'art comme métier', $keyed['domains#0:intro']);
        $this->assertSame('Questions fréquentes', $keyed['faq#0:title']);
        $this->assertSame('98 %', $keyed['stats#0:items.1.value']);
        $this->assertCount(count(BlockTexts::extract($this->blocks())), $keyed);
    }

    public function test_apply_keyed_survives_insertion_of_another_block_type_at_top(): void
    {
        $fr = $this->blocks();
        $keyedEn = [
            'hero#0:title' => 'Bring the stage to life',
            'hero#1:title' => 'Sound that suits you',
            'hero#1:hotspots.0.label' => 'SSL console',
            'text#0:body' => '<p>Founded in 2010, <a href="/emsi/histoire">read more</a>.</p>',
            'cards#0:items.0.text' => 'On stage from year one.',
        ];

        array_unshift($fr, ['type' => 'marquee', 'data' => ['words' => ['Son', 'Lumière']]]);
        $en = BlockTexts::applyKeyed($fr, $keyedEn);

        $this->assertSame(['Son', 'Lumière'], $en[0]['data']['words']); // nouveau bloc : reste en français
        $this->assertSame('Bring the stage to life', $en[1]['data']['title']);
        $this->assertSame('Sound that suits you', $en[4]['data']['title']);
        $this->assertSame('SSL console', $en[4]['data']['hotspots'][0]['label']);
        $this->assertSame(12.5, $en[4]['data']['hotspots'][0]['x']);
        $this->assertSame('<p>Founded in 2010, <a href="/emsi/histoire">read more</a>.</p>', $en[3]['data']['body']);
        $this->assertSame('On stage from year one.', $en[6]['data']['items'][0]['text']);
        $this->assertSame('Le Grand Théâtre', $en[1]['data']['slides'][0]['title']); // pas de traduction : français
    }

    public function test_apply_keyed_ignores_texts_of_removed_blocks(): void
    {
        $fr = $this->blocks();
        $keyedEn = ['text#0:title' => 'Our story', 'faq#0:title' => 'FAQ', 'hero#1:title' => 'Sound that suits you'];

        unset($fr[2]); // bloc texte supprimé
        $fr = array_values($fr);
        $en = BlockTexts::applyKeyed($fr, $keyedEn);

        $this->assertSame('FAQ', $en[3]['data']['title']);
        $this->assertSame('Sound that suits you', $en[2]['data']['title']);
        $this->assertNotContains('Our story', array_values(BlockTexts::extract($en)));
    }

    public function test_apply_keyed_ignores_malformed_keys_and_non_string_values(): void
    {
        $blocks = $this->blocks();
        $this->assertSame($blocks, BlockTexts::applyKeyed($blocks, ['nonsense' => 'x', 'hero#x:title' => 'y', 'hero#0:title' => ['array'], 'hero#0:accent' => 'red']));
    }

    public function test_is_html(): void
    {
        $this->assertTrue(BlockTexts::isHtml('text#0:body', 'Texte simple'));
        $this->assertTrue(BlockTexts::isHtml('2.data.body', ''));
        $this->assertTrue(BlockTexts::isHtml('hero#0:subtitle', 'Un <strong>mot</strong>'));
        $this->assertFalse(BlockTexts::isHtml('hero#0:title', 'Son < lumière > vidéo'));
        $this->assertFalse(BlockTexts::isHtml('hero#0:title', 'Faites vivre la scène'));
    }

    public function test_describe_gives_a_human_label(): void
    {
        $blocks = $this->blocks();

        $this->assertSame('Bloc 1 · Grand titre (héros) · Titre', BlockTexts::describe('hero#0:title', $blocks));
        $this->assertSame('Bloc 1 · Grand titre (héros) · Diapositive 2 · Titre', BlockTexts::describe('hero#0:slides.1.title', $blocks));
        $this->assertSame('Bloc 4 · Grand titre (héros) · Point sur la photo 1 · Texte', BlockTexts::describe('hero#1:hotspots.0.label', $blocks));
        $this->assertSame('Bloc 4 · Grand titre (héros) · Mot qui défile 2', BlockTexts::describe('hero#1:words.1', $blocks));
        $this->assertSame('Bloc 2 · Nos trois maisons (triptyque) · Panneau 3 · Texte du lien', BlockTexts::describe('domains#0:panels.2.label', $blocks));
        $this->assertSame('Bloc 3 · Texte · Texte', BlockTexts::describe('text#0:body', $blocks));
        $this->assertSame('Bloc 1 · Grand titre (héros) · Description de l\'image', BlockTexts::describe('hero#0:image_alt', $blocks));
        $this->assertSame('Bloc supprimé · Chronologie / étapes · Étape 1 · Titre', BlockTexts::describe('timeline#0:steps.0.title', $blocks));
        $this->assertSame('Bloc supprimé · mystery · foo', BlockTexts::describe('mystery#0:foo', $blocks));
    }
}
