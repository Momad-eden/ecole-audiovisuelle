<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\News;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Le site ne doit afficher ni coordonnées fictives ni affirmations absentes
 * du document de projet (01-AUDIT §5.2 et §5.4, décision Q-4).
 */
class PublicContentIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private const FORBIDDEN = [
        "d'État", "D'ÉTAT", "d'état",
        '33 800 00 00', '338000000', '33 000 00 00',
        'Doudou Ndiaye Rose',
        'UEMOA', 'marchés publics', 'MARCHÉS PUBLICS',
        'double tutelle', 'Double tutelle', 'DOUBLE TUTELLE',
        'Livret 1', 'Livret 2', 'Livret 01', 'Livret 02', 'LIVRET 1', 'LIVRET 2', 'LIVRET 01', 'LIVRET 02',
        'FOPICA', 'Canal+', 'RTS Sénégal', 'RTS,',
        'Réponse sous 48h', '48 heures', 'Aucun prérequis',
        'registre national', 'grille indiciaire', 'Grille indiciaire', 'grilles indiciaires',
        'Sans frais de dossier', 'Inscriptions Ouvertes', 'Inscriptions ouvertes', 'INSCRIPTIONS OUVERTES',
        'strictement identique', 'Habilitation à signer',
    ];

    private function assertClean(string $label, string $html): void
    {
        $text = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        foreach (self::FORBIDDEN as $phrase) {
            $this->assertStringNotContainsString($phrase, $text, "« {$phrase} » apparaît sur {$label}.");
        }
    }

    public function test_public_pages_contain_no_unsourced_claims_or_fake_contacts(): void
    {
        $course = Course::factory()->create(['is_active' => true]);
        News::create(['title' => 'Info', 'slug' => 'info', 'content' => 'x', 'is_published' => true, 'published_at' => now()->subDay()]);

        $pages = ['/', '/projet', '/vae', '/ecole', '/formations', "/formations/{$course->slug}", '/galerie', '/actualites', '/actualites/info', '/admission', '/login'];

        foreach ($pages as $page) {
            $this->assertClean($page, $this->get($page)->assertOk()->getContent());
        }

        $success = $this->withSession(['candidate_name' => 'Awa'])->get('/admission/succes')->assertOk()->getContent();
        $this->assertClean('/admission/succes', $success);
    }

    public function test_receipt_uses_school_settings_and_payment_session(): void
    {
        Setting::create(['school_name' => 'EMSI', 'phone' => '+221 77 680 70 62', 'email' => 'contact@ecole.test']);
        $student = Student::factory()->create();
        $payment = Payment::create([
            'type' => 'inflow', 'category' => 'scolarite', 'student_id' => $student->id,
            'amount' => 1000, 'payment_method' => 'cash', 'payment_date' => '2025-11-10',
        ]);
        $user = User::factory()->create(['role' => 'gestionnaire']);

        $html = $this->actingAs($user)->get(route('payments.receipt', $payment))->assertOk()->getContent();

        $this->assertClean('reçu', $html);
        $this->assertStringContainsString('+221 77 680 70 62', $html);
        $this->assertStringContainsString('contact@ecole.test', $html);
        $this->assertStringContainsString('Session : 2025-2026', $html);
    }

    public function test_vae_eligibility_simulator_requires_a_cps_or_cs(): void
    {
        $this->get('/vae')->assertOk()->assertSee("if (this.diplome !== 'cps') return 'faible';", false);
    }
}
