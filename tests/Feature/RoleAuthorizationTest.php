<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_directeur_can_access_all_admin_sections(): void
    {
        $directeur = User::factory()->create([
            'role' => UserRole::DIRECTEUR->value,
        ]);

        $this->actingAs($directeur)->get(route('users.index'))->assertStatus(200);
        $this->actingAs($directeur)->get(route('settings.index'))->assertStatus(200);
        $this->actingAs($directeur)->get(route('courses.index'))->assertStatus(200);
        $this->actingAs($directeur)->get(route('students.index'))->assertStatus(200);
        $this->actingAs($directeur)->get(route('admissions.index'))->assertStatus(200);
        $this->actingAs($directeur)->get(route('payments.index'))->assertStatus(200);
        $this->actingAs($directeur)->get(route('news.index'))->assertStatus(200);
        $this->actingAs($directeur)->get(route('galleries.index'))->assertStatus(200);
        $this->actingAs($directeur)->get(route('partners.index'))->assertStatus(200);
    }

    public function test_gestionnaire_has_restricted_access(): void
    {
        $gestionnaire = User::factory()->create([
            'role' => UserRole::GESTIONNAIRE->value,
        ]);

        // Authorized sections
        $this->actingAs($gestionnaire)->get(route('courses.index'))->assertStatus(200);
        $this->actingAs($gestionnaire)->get(route('students.index'))->assertStatus(200);
        $this->actingAs($gestionnaire)->get(route('admissions.index'))->assertStatus(200);
        $this->actingAs($gestionnaire)->get(route('payments.index'))->assertStatus(200);

        // Forbidden sections (403)
        $this->actingAs($gestionnaire)->get(route('users.index'))->assertStatus(403);
        $this->actingAs($gestionnaire)->get(route('settings.index'))->assertStatus(403);
        $this->actingAs($gestionnaire)->get(route('news.index'))->assertStatus(403);
        $this->actingAs($gestionnaire)->get(route('galleries.index'))->assertStatus(403);
        $this->actingAs($gestionnaire)->get(route('partners.index'))->assertStatus(403);
    }

    public function test_communication_has_restricted_access(): void
    {
        $communication = User::factory()->create([
            'role' => UserRole::COMMUNICATION->value,
        ]);

        // Authorized sections
        $this->actingAs($communication)->get(route('news.index'))->assertStatus(200);
        $this->actingAs($communication)->get(route('galleries.index'))->assertStatus(200);
        $this->actingAs($communication)->get(route('partners.index'))->assertStatus(200);

        // Forbidden sections (403)
        $this->actingAs($communication)->get(route('courses.index'))->assertStatus(403);
        $this->actingAs($communication)->get(route('students.index'))->assertStatus(403);
        $this->actingAs($communication)->get(route('admissions.index'))->assertStatus(403);
        $this->actingAs($communication)->get(route('payments.index'))->assertStatus(403);
        $this->actingAs($communication)->get(route('users.index'))->assertStatus(403);
        $this->actingAs($communication)->get(route('settings.index'))->assertStatus(403);
    }
}
