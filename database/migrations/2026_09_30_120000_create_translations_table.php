<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Traductions (anglais) des contenus : table à part, les colonnes françaises ne bougent pas. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('translatable_type');
            $table->unsignedBigInteger('translatable_id');
            $table->string('field', 60);
            $table->string('locale', 5)->default('en');
            $table->longText('value')->nullable();
            $table->string('source_hash', 64)->nullable();
            $table->string('status', 20)->default('auto');
            $table->longText('previous_value')->nullable();
            $table->timestamp('translated_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['translatable_type', 'translatable_id', 'field', 'locale'], 'translations_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
