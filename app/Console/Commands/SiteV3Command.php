<?php

namespace App\Console\Commands;

use Database\Seeders\ContentSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SiteV3Command extends Command
{
    protected $signature = 'emsi:site-v3';

    protected $description = 'Met à niveau le contenu : page L\'École à deux campus, chiffres clés, campus et agenda sur l\'accueil (sans perte, relançable)';

    public function handle(ContentSeeder $content): int
    {
        DB::transaction(fn () => $content->refreshSiteV3());

        $this->info('Site mis à niveau. Les versions précédentes des pages restent dans leur historique.');

        return self::SUCCESS;
    }
}
