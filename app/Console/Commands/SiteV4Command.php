<?php

namespace App\Console\Commands;

use App\Services\FrontendRevalidator;
use Database\Seeders\ContentSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SiteV4Command extends Command
{
    protected $signature = 'emsi:site-v4
        {--home : Remplacer aussi l\'accueil par le triptyque des trois maisons (l\'ancien reste dans l\'historique)}
        {--force : Reprendre toute la mise à niveau même si elle est déjà faite (recrée les pages, menus et campus manquants)}';

    protected $description = 'Met à niveau le contenu vers le site des trois domaines : pages déplacées et créées, menus, liens (sans perte ; relancée, ne refait que les liens et la page EMSI)';

    public function handle(ContentSeeder $content, FrontendRevalidator $site): int
    {
        // Une seule régénération du site, après la validation de la transaction (pas une par enregistrement).
        $report = $site->withoutRefreshing(fn () => DB::transaction(fn () => $content->refreshSiteV4((bool) $this->option('home'), (bool) $this->option('force'))));

        foreach ($report as $line) {
            $this->line("• {$line}");
        }
        $this->info('Site des trois domaines en place. Les versions précédentes des pages restent dans leur historique.');
        if (! $this->option('home')) {
            $this->line('L\'accueil n\'a été touché que pour ses liens et surtitres ; relancez avec --home pour le triptyque.');
        }

        if ($site->refreshNow(['content']) === false) {
            $this->warn('Le site public n\'a pas pu être rafraîchi (site Next injoignable) : il affichera les nouveautés à sa prochaine régénération, ou relancez la commande.');
        }

        return self::SUCCESS;
    }
}
