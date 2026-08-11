<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admissions', function (Blueprint $table) {

            // Informations personnelles du candidat

            $table->date('birth_date')
                ->nullable()
                ->after('last_name');

            $table->string('birth_place')
                ->nullable()
                ->after('birth_date');

            $table->enum('gender', ['M', 'F'])
                ->nullable()
                ->after('birth_place');

            $table->string('nationality')
                ->nullable()
                ->after('gender');

            $table->string('address')
                ->nullable()
                ->after('nationality');


            // Parcours académique

            $table->string('last_diploma')
                ->nullable()
                ->after('address');

            $table->unsignedSmallInteger('graduation_year')
                ->nullable()
                ->after('last_diploma');

            $table->string('previous_school')
                ->nullable()
                ->after('graduation_year');

            $table->string('academic_field')
                ->nullable()
                ->after('previous_school');

        });
    }

    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {

            $table->dropColumn([
                'birth_date',
                'birth_place',
                'gender',
                'nationality',
                'address',
                'last_diploma',
                'graduation_year',
                'previous_school',
                'academic_field',
            ]);

        });
    }
};