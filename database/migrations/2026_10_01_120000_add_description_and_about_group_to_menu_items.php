<?php

use App\Models\MenuItem;
use Database\Seeders\MenuDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menu principal : une phrase courte sous chaque lien des sous-menus, et le groupe « À propos »
 * (mission, partenaires, soutien, actualités, presse, contact). Sur une base déjà remplie seulement
 * (une installation neuve reçoit le tout du ContentSeeder) ; rien n'est écrasé : seules les descriptions
 * vides sont remplies, et le groupe n'est créé que s'il n'existe pas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('description', 160)->nullable()->after('label');
        });

        if (! MenuItem::where('location', 'main')->whereNull('parent_id')->where('url', '/maison-habib-faye')->exists()) {
            return;
        }

        MenuDefaults::addAboutGroup();
        MenuDefaults::fillDescriptions();
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
