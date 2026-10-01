<?php

namespace Tests\Unit;

use App\Support\CentreCulturelRename;
use PHPUnit\Framework\TestCase;

class CentreCulturelRenameTest extends TestCase
{
    public function test_the_name_and_its_forms_are_renamed(): void
    {
        $this->assertSame('Centre culturel Habib Faye', CentreCulturelRename::text('Maison Habib Faye'));
        $this->assertSame('Le Centre culturel', CentreCulturelRename::text('La Maison'));
        $this->assertSame('Découvrir le Centre culturel', CentreCulturelRename::text('Découvrir la Maison'));
        $this->assertSame('Les dernières nouvelles du Centre culturel et de l\'EMSI', CentreCulturelRename::text('Les dernières nouvelles de la Maison et de l\'EMSI'));
        $this->assertSame('Nos trois lieux', CentreCulturelRename::text('Nos trois maisons'));
    }

    public function test_the_site_no_longer_claims_the_heritage_of_habib_faye(): void
    {
        $this->assertSame('Concerts, résidences et transmission, à Saint-Louis.', CentreCulturelRename::text('Concerts, résidences et transmission, dans la maison de Habib Faye.'));
        $this->assertSame('Le lieu, son projet et ses activités', CentreCulturelRename::text('Le lieu, son histoire et l\'héritage de Habib Faye'));
        $this->assertSame('La culture comme *héritage*, l\'art comme *métier*', CentreCulturelRename::text('La culture comme *héritage*, l\'art comme *métier*'), 'La devise reste.');
    }

    public function test_addresses_and_links_move_to_centre_culturel(): void
    {
        $this->assertSame('/centre-culturel/studio#reserver', CentreCulturelRename::text('/maison-habib-faye/studio#reserver'));
        $this->assertSame('<p><a href="/centre-culturel/agenda">Agenda</a></p>', CentreCulturelRename::text('<p><a href="/maison-habib-faye/agenda">Agenda</a></p>'));
        $this->assertSame('centre-culturel/espaces', CentreCulturelRename::slug('maison-habib-faye/espaces'));
        $this->assertSame('maison-habib-fayette', CentreCulturelRename::slug('maison-habib-fayette'));
    }

    public function test_block_data_is_walked_and_the_domain_key_kept(): void
    {
        $blocks = [['type' => 'domains', 'data' => ['panels' => [
            ['domain' => 'maison', 'title' => 'Maison Habib Faye', 'url' => '/maison-habib-faye', 'label' => 'Découvrir la Maison'],
        ]]]];

        $this->assertSame([['type' => 'domains', 'data' => ['panels' => [
            ['domain' => 'maison', 'title' => 'Centre culturel Habib Faye', 'url' => '/centre-culturel', 'label' => 'Découvrir le Centre culturel'],
        ]]]], CentreCulturelRename::deep($blocks));
    }
}
