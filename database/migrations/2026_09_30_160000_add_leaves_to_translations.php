<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * État par texte des champs structurés (blocs, listes, SEO) : `{clé: {h: empreinte du français, s: statut}}`.
 * La valeur anglaise reste au même format (lecteurs de l'API inchangés).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translations', function (Blueprint $table) {
            $table->json('leaves')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('translations', function (Blueprint $table) {
            $table->dropColumn('leaves');
        });
    }
};
