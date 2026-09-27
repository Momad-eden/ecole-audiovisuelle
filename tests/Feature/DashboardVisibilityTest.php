<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $course = Course::factory()->create();
        Admission::create([
            'first_name' => 'Candidate', 'last_name' => 'Secrete', 'phone' => '+221771112233',
            'course_id' => $course->id, 'status' => 'pending',
        ]);
        Payment::create([
            'type' => 'outflow', 'category' => 'maintenance', 'title' => 'Réparation',
            'amount' => 50000, 'payment_method' => 'cash', 'payment_date' => now()->toDateString(),
        ]);
    }

    private function dashboardFor(string $role): string
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);

        return $this->get(route('dashboard'))->assertOk()->getContent();
    }

    public function test_communication_sees_neither_finances_nor_candidates(): void
    {
        $html = $this->dashboardFor('communication');

        $this->assertStringNotContainsString('Solde Réel de Caisse', $html);
        $this->assertStringNotContainsString('Recouvrement', $html);
        $this->assertStringNotContainsString('Secrete', $html);
        $this->assertStringNotContainsString('+221771112233', $html);
    }

    public function test_directeur_sees_finances_and_candidates(): void
    {
        $html = $this->dashboardFor('directeur');

        $this->assertStringContainsString('Solde Réel de Caisse', $html);
        $this->assertStringContainsString('Secrete', $html);
    }

    public function test_no_role_is_offered_a_link_it_cannot_open(): void
    {
        foreach (['directeur', 'gestionnaire', 'secretaire', 'communication'] as $role) {
            $html = $this->dashboardFor($role);

            preg_match_all('#href="'.preg_quote(url('/'), '#').'(/[^"?\#]*)#', $html, $matches);
            $paths = array_unique(array_filter($matches[1], fn ($p) => $p !== '/' && ! str_starts_with($p, '/storage')));

            foreach ($paths as $path) {
                $status = $this->get($path)->getStatusCode();
                $this->assertNotSame(403, $status, "Le rôle {$role} voit un lien vers {$path} qui lui est interdit.");
            }
        }
    }

    public function test_header_shows_the_role_label(): void
    {
        $html = $this->dashboardFor('secretaire');

        $this->assertStringContainsString('Secrétaire', $html);
    }
}
