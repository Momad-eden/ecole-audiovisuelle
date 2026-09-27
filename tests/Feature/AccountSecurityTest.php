<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_created_without_role_has_no_role(): void
    {
        $user = User::create([
            'name' => 'Sans rôle',
            'email' => 'sans-role@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $this->assertNull($user->fresh()->role);
    }

    public function test_user_without_valid_role_cannot_open_administration(): void
    {
        foreach ([null, 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->get(route('dashboard'))->assertForbidden();
            $this->actingAs($user)->get(route('profile.edit'))->assertForbidden();
        }
    }

    public function test_database_seeder_creates_no_account(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_create_admin_command_creates_a_directeur(): void
    {
        $this->artisan('emsi:create-admin')
            ->expectsQuestion('Nom complet', 'Awa Diop')
            ->expectsQuestion('Adresse email', 'awa@emsi.sn')
            ->expectsChoice("Rôle d'accès", 'directeur', UserRole::values())
            ->expectsQuestion('Mot de passe (au moins 8 caractères)', 'Secret-2026!')
            ->expectsQuestion('Confirmez le mot de passe', 'Secret-2026!')
            ->assertSuccessful();

        $user = User::where('email', 'awa@emsi.sn')->firstOrFail();
        $this->assertSame('directeur', $user->role);
        $this->assertTrue(Hash::check('Secret-2026!', $user->password));
    }

    public function test_create_admin_command_refuses_password_option(): void
    {
        $this->expectException(InvalidOptionException::class);

        $this->artisan('emsi:create-admin', ['--password' => 'secret123'])->run();
    }

    public function test_directeur_cannot_demote_himself(): void
    {
        $directeur = User::factory()->create(['role' => 'directeur']);
        User::factory()->create(['role' => 'directeur']);

        $this->actingAs($directeur)
            ->put(route('users.update', $directeur), [
                'name' => $directeur->name,
                'email' => $directeur->email,
                'role' => 'communication',
            ])
            ->assertSessionHasErrors('role');

        $this->assertSame('directeur', $directeur->fresh()->role);
    }

    public function test_users_cannot_delete_their_own_account_from_profile(): void
    {
        $user = User::factory()->create(['role' => 'gestionnaire']);

        $this->actingAs($user)->delete('/profile', ['password' => 'password'])->assertStatus(405);
        $this->assertNotNull($user->fresh());
    }
}
