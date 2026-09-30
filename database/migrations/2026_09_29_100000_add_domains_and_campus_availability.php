<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trois domaines (Maison Habib Faye, EMSI, Impact Live Studio) : domaine de chaque page,
 * sous-menus, formations proposées par campus, session rattachée à un campus,
 * organisme sur les messages de contact.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('domain', 20)->default('general');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
        });

        Schema::create('place_program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->unique(['place_id', 'program_id']);
        });

        Schema::table('cohorts', function (Blueprint $table) {
            $table->foreignId('place_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('organization', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn('organization');
        });

        Schema::table('cohorts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('place_id');
        });

        Schema::dropIfExists('place_program');

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('domain');
        });
    }
};
