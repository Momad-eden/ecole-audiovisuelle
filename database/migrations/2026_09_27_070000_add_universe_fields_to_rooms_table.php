<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Les salles deviennent les « univers » de l'école : signature visuelle et univers annoncé (bientôt). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('visual', 20)->default('sound')->after('accent_color');
            $table->boolean('is_upcoming')->default(false)->after('visual');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['visual', 'is_upcoming']);
        });
    }
};
