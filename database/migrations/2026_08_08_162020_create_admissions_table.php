<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {

            $table->id();

            // Informations du candidat
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone');

            // Formation demandée
            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();

            // État du dossier
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            // Informations complémentaires
            $table->text('message')->nullable();

            // Date de traitement
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};