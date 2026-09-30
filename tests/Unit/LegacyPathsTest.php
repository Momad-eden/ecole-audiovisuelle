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
            'studio' => ['/studio', '/maison-habib-faye/studio'],
            'studio anchor kept' => ['/studio#reserver', '/maison-habib-faye/studio#reserver'],
            'ecole' => ['/ecole', '/emsi'],
            'espace anchor kept' => ['/espace-habib-faye#programmation', '/maison-habib-faye#programmation'],
            'agenda' => ['/agenda', '/maison-habib-faye/agenda'],
            'agenda event' => ['/agenda/festival', '/maison-habib-faye/agenda/festival'],
            'events' => ['/events', '/maison-habib-faye'],
            'events anchor dropped' => ['/events#devis', '/maison-habib-faye'],
            'events subpage' => ['/events/materiel', '/maison-habib-faye'],
            'musee' => ['/musee', '/emsi/realisations'],
            'musee oeuvre' => ['/musee/oeuvres/la-nuit', '/emsi/realisations/la-nuit'],
            'musee salle' => ['/musee/salle-du-son', '/emsi/univers/son'],
            'musee autre salle' => ['/musee/cinema', '/emsi/univers/cinema'],
            'demande de devis' => ['/demande', '/maison-habib-faye/studio#reserver'],
            'demande avec requête' => ['/demande?type=x', '/maison-habib-faye/studio#reserver'],
            'trailing slash' => ['/studio/', '/maison-habib-faye/studio'],
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
            ['/emsi'], ['/emsi/formations/x'], ['/maison-habib-faye/studio#reserver'], ['/maison-habib-faye'],
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
            '<p>Voir <a href="/emsi/formations/son">la formation</a>, <a href=\'/maison-habib-faye\'>nos prestations</a> et <a href="https://x.sn/studio">ailleurs</a>.</p>',
            LegacyPaths::rewriteHtml($html),
        );
    }
}
