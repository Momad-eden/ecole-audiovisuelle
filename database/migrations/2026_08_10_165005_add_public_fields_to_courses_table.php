<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {

            $table->string('category')
                ->nullable()
                ->after('title');

            $table->string('level')
                ->nullable()
                ->after('duration');

            $table->unsignedInteger('students_count')
                ->default(0)
                ->after('level');

        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {

            $table->dropColumn([
                'category',
                'level',
                'students_count',
            ]);

        });
    }
};