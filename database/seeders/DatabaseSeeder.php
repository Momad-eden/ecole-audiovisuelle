<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Aucun compte n'est créé automatiquement : le premier directeur
     * est créé avec `php artisan emsi:create-admin`.
     */
    public function run(): void
    {
        $this->call(ContentSeeder::class);

        $this->command?->info('Aucun compte créé. Utilisez « php artisan emsi:create-admin » pour créer le premier directeur.');
    }
}
