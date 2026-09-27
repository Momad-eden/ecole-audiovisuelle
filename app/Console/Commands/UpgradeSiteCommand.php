<?php

namespace App\Console\Commands;

use Database\Seeders\ContentSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpgradeSiteCommand extends Command
{
    protected $signature = 'emsi:site-v2';

    protected $description = 'Met à niveau le contenu vers le site « Plein feux » : univers, Accueil, L\'École, menus (sans perte, relançable)';

    public function handle(ContentSeeder $content): int
    {
        DB::transaction(fn () => $content->refreshSite());

        $this->info('Site mis à niveau. Les versions précédentes des pages restent disponibles dans leur historique (Pages du site › ⋮ › Revenir à une version précédente).');

        return self::SUCCESS;
    }
}
