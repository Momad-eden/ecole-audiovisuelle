<?php

namespace App\Console\Commands;

use Database\Seeders\ContentSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImpactLiveCommand extends Command
{
    protected $signature = 'emsi:impact-live';

    protected $description = 'Ajoute Impact Live (studio, événementiel, Centre culturel Habib Faye) et le campus de Saint-Louis à un site existant (sans perte, relançable)';

    public function handle(ContentSeeder $content): int
    {
        DB::transaction(fn () => $content->refreshImpactLive());

        $this->info('Impact Live ajouté : lieux, services, pages Studio, Events et Centre culturel Habib Faye, menus. Complétez adresses, prix et matériel dans l\'admin.');

        return self::SUCCESS;
    }
}
