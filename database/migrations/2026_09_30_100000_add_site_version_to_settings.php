<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repère de version du contenu du site : emsi:site-v4 note qu'elle est passée (4), pour ne plus
 * recréer ensuite les pages, menus et campus des formations que l'équipe a pu retoucher.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedSmallInteger('site_version')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('site_version');
        });
    }
};
