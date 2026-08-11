<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {

            $table->id();

            // Identité
            $table->string('student_number')->unique();
            $table->string('photo')->nullable();

            $table->string('first_name');
            $table->string('last_name');

            $table->enum('gender', ['Homme', 'Femme']);

            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('nationality')->default('Sénégalaise');

            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();

            // Formation
            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('registration_date');

            $table->enum('status', [
                'Inscrit',
                'Diplômé',
                'Suspendu',
                'Abandonné'
            ])->default('Inscrit');

            // Divers
            $table->text('notes')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};