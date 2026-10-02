<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Traduction anglaise : interrupteur et lexique (prérempli avec les noms à ne jamais traduire, spec R2 §3.2). */
return new class extends Migration
{
    private const GLOSSARY = [
        ['fr' => 'EMSI', 'en' => 'EMSI'],
        ['fr' => 'Impact Live Studio', 'en' => 'Impact Live Studio'],
        ['fr' => 'Maison de la culture Habib Faye', 'en' => 'Maison de la culture Habib Faye'],
        ['fr' => 'Grand Théâtre National Doudou Ndiaye Coumba Rose', 'en' => 'Grand Théâtre National Doudou Ndiaye Coumba Rose'],
        ['fr' => 'Habib Faye', 'en' => 'Habib Faye'],
        ['fr' => 'Boubacar Tall', 'en' => 'Boubacar Tall'],
        ['fr' => 'Dakar', 'en' => 'Dakar'],
        ['fr' => 'Saint-Louis', 'en' => 'Saint-Louis'],
        ['fr' => 'VAE', 'en' => 'Recognition of Prior Learning (VAE)'],
    ];

    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('auto_translate')->default(true);
            $table->json('translation_glossary')->nullable();
        });

        DB::table('settings')->whereNull('translation_glossary')->update([
            'translation_glossary' => json_encode(self::GLOSSARY, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['auto_translate', 'translation_glossary']);
        });
    }
};
