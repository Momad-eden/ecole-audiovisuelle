<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCourseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $directeur;
    protected User $gestionnaire;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directeur = User::factory()->create([
            'role' => 'directeur',
        ]);

        $this->gestionnaire = User::factory()->create([
            'role' => 'gestionnaire',
        ]);
    }

    public function test_admin_can_view_courses_index_and_kpis(): void
    {
        Course::factory()->create([
            'title' => 'Techniques du Son avancées',
            'category' => 'Ingénierie Son & Live',
            'students_count' => 20,
            'is_active' => true,
        ]);

        Course::factory()->create([
            'title' => 'Formation Inactive',
            'category' => 'Autre',
            'students_count' => 10,
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->directeur)
            ->get(route('courses.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.courses.index');
        $response->assertSee('Techniques du Son avancées');
        $response->assertSee('Ingénierie Son & Live');
        $response->assertSee('1 active(s)');
        $response->assertSee('1 masquée(s)');
    }

    public function test_admin_can_filter_courses_by_search_category_and_status(): void
    {
        Course::factory()->create([
            'title' => 'Cadrage Sportif',
            'category' => 'Broadcast',
            'is_active' => true,
        ]);

        Course::factory()->create([
            'title' => 'Lumière et Scénographie',
            'category' => 'Éclairage',
            'is_active' => false,
        ]);

        // Filter by category
        $responseCat = $this->actingAs($this->directeur)
            ->get(route('courses.index', ['category' => 'Broadcast']));
        $responseCat->assertStatus(200);
        $responseCat->assertSee('Cadrage Sportif');
        $responseCat->assertDontSee('Lumière et Scénographie');

        // Filter by status active
        $responseStatus = $this->actingAs($this->directeur)
            ->get(route('courses.index', ['status' => 'active']));
        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('Cadrage Sportif');
        $responseStatus->assertDontSee('Lumière et Scénographie');
    }

    public function test_admin_can_create_new_course(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->create('course.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->directeur)
            ->post(route('courses.store'), [
                'title'          => 'Régie Générale Spectacle',
                'category'       => 'Régie & Management Technique',
                'level'          => 'Perfectionnement intensif & BTS Bac+2 (VAE)',
                'duration'       => '3 à 9 mois',
                'students_count' => 25,
                'price'          => 450000,
                'description'    => 'Cursus de management technique de spectacles.',
                'image'          => $image,
                'is_active'      => '1',
            ]);

        $course = Course::where('title', 'Régie Générale Spectacle')->first();
        $this->assertNotNull($course);
        $this->assertEquals('regie-generale-spectacle', $course->slug);
        $this->assertEquals('Régie & Management Technique', $course->category);
        $this->assertEquals(25, $course->getRawOriginal('students_count'));
        $this->assertEquals(450000, $course->price);
        $this->assertTrue($course->is_active);

        $response->assertRedirect(route('courses.show', $course));
    }

    public function test_admin_can_view_course_360_show_page_with_students_and_admissions(): void
    {
        $course = Course::factory()->create([
            'title'          => 'Infographie et Création Numérique',
            'students_count' => 20,
            'price'          => 500000,
            'is_active'      => true,
        ]);

        $student = Student::factory()->create([
            'course_id'   => $course->id,
            'first_name'  => 'Mamadou',
            'last_name'   => 'Ba',
        ]);

        Payment::create([
            'reference'      => 'REC-TEST-001',
            'receipt_number' => 'REC-TEST-001',
            'student_id'     => $student->id,
            'amount'         => 200000,
            'type'           => 'inflow',
            'category'       => 'scolarite',
            'payment_method' => 'especes',
            'payment_date'   => now(),
        ]);

        $admission = Admission::factory()->create([
            'course_id'   => $course->id,
            'first_name'  => 'Amina',
            'last_name'   => 'Sarr',
            'volet'       => 'volet2',
            'status'      => 'pending',
        ]);

        $response = $this->actingAs($this->directeur)
            ->get(route('courses.show', $course));

        $response->assertStatus(200);
        $response->assertViewIs('admin.courses.show');
        $response->assertSee('Infographie et Création Numérique');
        $response->assertSee('Mamadou Ba');
        $response->assertSee('Amina Sarr');
        $response->assertSee('200 000 FCFA'); // Total paid
        $response->assertSee('Volet 2 — BTS-VAE');
    }

    public function test_admin_can_update_course(): void
    {
        $course = Course::factory()->create([
            'title' => 'Ancien Titre',
            'price' => 100000,
        ]);

        $response = $this->actingAs($this->directeur)
            ->put(route('courses.update', $course), [
                'title'          => 'Nouveau Titre Formation',
                'category'       => 'Nouveau Métier',
                'level'          => 'BTS',
                'duration'       => '6 mois',
                'students_count' => 30,
                'price'          => 250000,
                'description'    => 'Nouvelle description.',
                'is_active'      => '1',
            ]);

        $course->refresh();
        $this->assertEquals('Nouveau Titre Formation', $course->title);
        $this->assertEquals('nouveau-titre-formation', $course->slug);
        $this->assertEquals(250000, $course->price);
        $this->assertEquals(30, $course->getRawOriginal('students_count'));

        $response->assertRedirect(route('courses.show', $course));
    }

    public function test_cannot_delete_course_with_enrolled_students(): void
    {
        $course = Course::factory()->create([
            'title' => 'Formation Protégée',
        ]);

        Student::factory()->create([
            'course_id' => $course->id,
        ]);

        $response = $this->actingAs($this->directeur)
            ->delete(route('courses.destroy', $course));

        $response->assertRedirect(route('courses.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }

    public function test_can_delete_unused_course(): void
    {
        $course = Course::factory()->create([
            'title' => 'Formation Vide',
        ]);

        $response = $this->actingAs($this->directeur)
            ->delete(route('courses.destroy', $course));

        $response->assertRedirect(route('courses.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
}
