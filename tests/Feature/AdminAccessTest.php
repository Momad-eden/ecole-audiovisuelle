<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_the_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_public_registration_does_not_exist(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/admin/register')->assertNotFound();
    }

    public function test_each_valid_role_can_open_the_admin(): void
    {
        foreach (UserRole::values() as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))->get('/admin')->assertOk();
        }
    }

    public function test_accounts_without_valid_role_or_inactive_are_refused(): void
    {
        foreach ([['role' => null], ['role' => 'admin'], ['role' => 'directeur', 'is_active' => false]] as $attributes) {
            $this->actingAs(User::factory()->create($attributes))->get('/admin')->assertForbidden();
        }
    }

    public function test_database_seeder_creates_no_account(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_create_admin_command_creates_an_account_with_confirmed_password(): void
    {
        $this->artisan('emsi:create-admin')
            ->expectsQuestion('Nom complet', 'Awa Diop')
            ->expectsQuestion('Adresse email', 'awa@emsi.sn')
            ->expectsChoice("Rôle d'accès", 'directeur', UserRole::values())
            ->expectsQuestion('Mot de passe (au moins 8 caractères)', 'Secret-2026!')
            ->expectsQuestion('Confirmez le mot de passe', 'Secret-2026!')
            ->assertSuccessful();

        $user = User::where('email', 'awa@emsi.sn')->firstOrFail();
        $this->assertTrue($user->isDirecteur());
        $this->assertTrue(Hash::check('Secret-2026!', $user->password));
    }

    public function test_create_admin_command_refuses_password_option(): void
    {
        $this->expectException(InvalidOptionException::class);

        $this->artisan('emsi:create-admin', ['--password' => 'secret123'])->run();
    }
}
