<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::where('email', 'admin@emsi.sn')->doesntExist()) {
            User::create([
                'name'     => 'Directeur EMSI',
                'email'    => 'admin@emsi.sn',
                'password' => Hash::make('password'),
                'role'     => UserRole::DIRECTEUR->value,
            ]);
        }
    }
}
