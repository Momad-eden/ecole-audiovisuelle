<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Comptabilité séparée par campus (Dakar, Saint-Louis) : chaque caisse a sa numérotation,
 * son solde et ses clôtures ; étudiants et personnel sont rattachés à un campus
 * (personnel sans campus = tous les campus). L'existant est rattaché au campus de Dakar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->string('code', 5)->nullable()->unique()->after('slug');
        });

        foreach (['cash_transactions', 'students', 'users'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->foreignId('place_id')->nullable()->constrained()->restrictOnDelete();
            });
        }

        Schema::table('cash_closings', function (Blueprint $table) {
            $table->dropUnique(['period_end']);
            $table->foreignId('place_id')->nullable()->after('id')->constrained()->restrictOnDelete();
            $table->unique(['place_id', 'period_end']);
        });

        DB::table('places')->where('slug', 'emsi-dakar')->whereNull('code')->update(['code' => 'DKR']);
        DB::table('places')->where('slug', 'emsi-saint-louis')->whereNull('code')->update(['code' => 'STL']);

        $dakar = DB::table('places')->where('slug', 'emsi-dakar')->value('id')
            ?? DB::table('places')->where('kind', 'campus')->orderBy('position')->value('id');
        if ($dakar) {
            foreach (['cash_transactions', 'cash_closings', 'students'] as $name) {
                DB::table($name)->whereNull('place_id')->update(['place_id' => $dakar]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('cash_closings', function (Blueprint $table) {
            $table->dropUnique(['place_id', 'period_end']);
            $table->dropConstrainedForeignId('place_id');
            $table->unique('period_end');
        });

        foreach (['users', 'students', 'cash_transactions'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropConstrainedForeignId('place_id');
            });
        }

        Schema::table('places', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};
