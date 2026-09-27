<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Plus aucune suppression en cascade : l'historique (étudiants, paiements,
     * candidatures) ne peut plus disparaître par effet de bord.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->foreign('course_id')->references('id')->on('courses')->restrictOnDelete();
            $table->softDeletes();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('id')->on('students')->restrictOnDelete();
            $table->softDeletes();
        });

        Schema::table('admissions', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->foreign('course_id')->references('id')->on('courses')->restrictOnDelete();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('admissions', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->dropSoftDeletes();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->dropSoftDeletes();
        });
    }
};
