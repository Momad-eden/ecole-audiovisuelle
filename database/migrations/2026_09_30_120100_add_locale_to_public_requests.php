<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Langue dans laquelle la demande a été faite (fr par défaut). */
return new class extends Migration
{
    private array $tables = ['applications', 'contact_messages', 'booking_requests'];

    public function up(): void
    {
        foreach ($this->tables as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->string('locale', 5)->default('fr');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropColumn('locale');
            });
        }
    }
};
