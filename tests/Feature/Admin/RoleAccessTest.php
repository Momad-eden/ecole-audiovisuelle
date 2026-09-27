<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\Applications\ApplicationResource;
use App\Filament\Resources\Artworks\ArtworkResource;
use App\Filament\Resources\CashClosings\CashClosingResource;
use App\Filament\Resources\CashTransactions\CashTransactionResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Programs\ProgramResource;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Matrice des droits : chaque rôle ouvre ses écrans et aucun autre. */
class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private const MATRIX = [
        //                        directeur gestionnaire secretaire communication
        ApplicationResource::class => [true, true, true, false],
        StudentResource::class => [true, true, true, false],
        CashTransactionResource::class => [true, true, true, false],
        CashClosingResource::class => [true, true, false, false],
        ProgramResource::class => [true, true, true, false],
        ArtworkResource::class => [true, false, false, true],
        PageResource::class => [true, false, false, true],
        UserResource::class => [true, false, false, false],
    ];

    private const ROLES = ['directeur', 'gestionnaire', 'secretaire', 'communication'];

    public function test_each_role_reaches_exactly_its_screens(): void
    {
        foreach (self::ROLES as $index => $role) {
            $user = User::factory()->create(['role' => $role]);

            foreach (self::MATRIX as $resource => $allowed) {
                $status = $this->actingAs($user)->get($resource::getUrl('index'))->getStatusCode();
                $this->assertSame($allowed[$index] ? 200 : 403, $status, "{$role} → {$resource}");
            }

            $settings = $this->actingAs($user)->get(SiteSettings::getUrl())->getStatusCode();
            $this->assertSame(in_array($role, ['directeur', 'communication'], true) ? 200 : 403, $settings, "{$role} → paramètres");
        }
    }

    public function test_secretaire_cannot_create_a_program_but_can_record_cash(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'secretaire']));

        $this->get(ProgramResource::getUrl('create'))->assertForbidden();
        $this->get(CashTransactionResource::getUrl('create'))->assertOk();
    }
}
