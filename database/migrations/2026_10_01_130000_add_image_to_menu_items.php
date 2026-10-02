<?php

use Database\Seeders\MenuDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Méga-menu : chaque rubrique du menu principal (Maison, EMSI, À propos) peut avoir une photo et une phrase
 * d'accroche, affichées dans son panneau. Remplies une fois depuis le triptyque de l'accueil ; rien n'est écrasé.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });

        MenuDefaults::fillParents();
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
