<?php

namespace Tests\Unit;

use App\Support\LegacyPaths;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/** Anciennes adresses du site EMSI → adresses des trois domaines (mêmes règles que les redirections 301 du site). */
class LegacyPathsTest extends TestCase
{
    public static function addresses(): array
    {
        return [
            'univers' => ['/univers', '/emsi#univers'],
            'univers query dropped' => ['/univers?x=1#haut', '/emsi#univers'],
            'univers slug' => ['/univers/son', '/emsi/univers/son'],
            'formations' => ['/formations', '/emsi/formations'],
            'formation' => ['/formations/technicien-lumiere', '/emsi/formations/technicien-lumiere'],
            'formations query' => ['/formations?univers=son', '/emsi/formations?univers=son'],
            'realisations' => ['/realisations/mon-film', '/emsi/realisations/mon-film'],
            'professionnels' => ['/professionnels', '/emsi/professionnels'],
            'professionnels candidater' => ['/professionnels/candidater', '/emsi/professionnels/candidater'],
            'expositions' => ['/expositions/2025', '/emsi/realisations'],
            'studio' => ['/studio', '/centre-culturel/studio'],
            'studio anchor kept' => ['/studio#reserver', '/centre-culturel/studio#reserver'],
            'ecole' => ['/ecole', '/emsi'],
            'espace anchor kept' => ['/espace-habib-faye#programmation', '/centre-culturel#programmation'],
            'agenda' => ['/agenda', '/centre-culturel/agenda'],
            'agenda event' => ['/agenda/festival', '/centre-culturel/agenda/festival'],
            'events' => ['/events', '/centre-culturel'],
            'events anchor dropped' => ['/events#devis', '/centre-culturel'],
            'events subpage' => ['/events/materiel', '/centre-culturel'],
            'musee' => ['/musee', '/emsi/realisations'],
            'musee oeuvre' => ['/musee/oeuvres/la-nuit', '/emsi/realisations/la-nuit'],
            'musee salle' => ['/musee/salle-du-son', '/emsi/univers/son'],
            'musee autre salle' => ['/musee/cinema', '/emsi/univers/cinema'],
            'demande de devis' => ['/demande', '/centre-culturel/studio#reserver'],
            'demande avec requête' => ['/demande?type=x', '/centre-culturel/studio#reserver'],
            'trailing slash' => ['/studio/', '/centre-culturel/studio'],
            'ancienne Maison' => ['/maison-habib-faye', '/centre-culturel'],
            'ancienne Maison, sous-page et ancre' => ['/maison-habib-faye/studio#reserver', '/centre-culturel/studio#reserver'],
            'ancienne Maison, événement' => ['/maison-habib-faye/agenda/festival', '/centre-culturel/agenda/festival'],
        ];
    }

    #[DataProvider('addresses')]
    public function test_old_addresses_are_rewritten(string $old, string $new): void
    {
        $this->assertSame($new, LegacyPaths::rewrite($old));
    }

    public static function unchanged(): array
    {
        return [
            ['/'], ['/#univers'], ['/candidater'], ['/candidater?campus=emsi-dakar'], ['/contact'], ['/actualites/une-nouvelle'],
            ['/emsi'], ['/emsi/formations/x'], ['/centre-culturel/studio#reserver'], ['/centre-culturel'],
            ['/studios'], ['/eventsx'], ['/formationsx'], ['/demandes'], ['#reserver'], [''],
            ['https://emsi.sn/formations'], ['mailto:contact@emsi.sn'], ['tel:+221776807062'],
        ];
    }

    #[DataProvider('unchanged')]
    public function test_other_addresses_are_left_alone(string $url): void
    {
        $this->assertSame($url, LegacyPaths::rewrite($url));
    }

    public function test_rewriting_twice_changes_nothing_more(): void
    {
        foreach (self::addresses() as [$old]) {
            $once = LegacyPaths::rewrite($old);
            $this->assertSame($once, LegacyPaths::rewrite($once), $old);
        }
    }

    public function test_links_inside_rich_text_are_rewritten(): void
    {
        $html = '<p>Voir <a href="/formations/son">la formation</a>, <a href=\'/events\'>nos prestations</a> et <a href="https://x.sn/studio">ailleurs</a>.</p>';

        $this->assertSame(
            '<p>Voir <a href="/emsi/formations/son">la formation</a>, <a href=\'/centre-culturel\'>nos prestations</a> et <a href="https://x.sn/studio">ailleurs</a>.</p>',
            LegacyPaths::rewriteHtml($html),
        );
    }
}
