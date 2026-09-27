<?php

namespace Tests\Feature\Admin;

use App\Enums\ApplicationStatus;
use App\Enums\CohortStatus;
use App\Enums\PublicationStatus;
use App\Filament\Resources\Applications\Pages\ViewApplication;
use App\Filament\Resources\Artworks\Pages\CreateArtwork;
use App\Filament\Resources\CashTransactions\Pages\CreateCashTransaction;
use App\Filament\Resources\Cohorts\Pages\ListCohorts;
use App\Filament\Resources\News\Pages\CreateNews;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Models\Application;
use App\Models\Artwork;
use App\Models\CashTransaction;
use App\Models\Cohort;
use App\Models\Enrollment;
use App\Models\News;
use App\Models\Page;
use App\Models\Room;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** Les six tâches qu'une personne de l'école doit pouvoir réaliser seule. */
class RecetteTasksTest extends TestCase
{
    use RefreshDatabase;

    private function as(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);

        return $user;
    }

    public function test_1_publish_a_news_article(): void
    {
        $this->as('communication');

        Livewire::test(CreateNews::class)
            ->fillForm(['title' => 'Portes ouvertes le 12 octobre', 'content' => '<p>Venez visiter nos studios.</p>', 'is_published' => true, 'published_at' => now()->subMinute()])
            ->call('create')
            ->assertHasNoFormErrors();

        $news = News::firstOrFail();
        $this->assertSame('portes-ouvertes-le-12-octobre', $news->slug);
        $this->assertTrue(News::published()->whereKey($news->id)->exists());
    }

    public function test_2_add_an_artwork_to_the_museum(): void
    {
        $this->as('communication');
        $room = Room::create(['name' => 'Salle du Son', 'status' => PublicationStatus::PUBLISHED]);

        Livewire::test(CreateArtwork::class)
            ->fillForm([
                'kind' => 'audio', 'title' => 'Paysage sonore de Dakar', 'summary' => 'Captation binaurale du marché Sandaga.',
                'room_id' => $room->id, 'equipment' => ['Zoom H6'], 'status' => PublicationStatus::PUBLISHED->value,
                'credits' => [['person_name' => 'Awa Ndiaye', 'role' => 'Prise de son']],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $artwork = Artwork::with('credits')->firstOrFail();
        $this->assertTrue($artwork->isPublished());
        $this->assertSame('Awa Ndiaye', $artwork->credits->first()->person_name);
    }

    public function test_3_edit_and_publish_the_home_page(): void
    {
        $user = $this->as('communication');
        $page = Page::create(['title' => 'Accueil', 'slug' => 'accueil', 'type' => 'home', 'is_locked' => true]);

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->fillForm(['draft_blocks' => [['type' => 'text', 'data' => ['title' => 'Bienvenue', 'body' => '<p>L\'école du son et de l\'image.</p>']]]])
            ->call('save')
            ->assertHasNoFormErrors()
            ->callAction('publish');

        $page->refresh();
        $this->assertSame(PublicationStatus::PUBLISHED, $page->status);
        $this->assertSame('Bienvenue', $page->blocks[0]['data']['title']);
        $this->assertFalse($page->hasUnpublishedChanges());
        $this->assertSame($user->id, $page->revisions()->first()->user_id);
    }

    public function test_4_open_applications_for_a_session(): void
    {
        $this->as('gestionnaire');
        $cohort = Cohort::factory()->create(['status' => CohortStatus::PLANNED]);

        Livewire::test(ListCohorts::class)->callAction(TestAction::make('toggle')->table($cohort));

        $this->assertSame(CohortStatus::OPEN, $cohort->fresh()->status);
    }

    public function test_5_process_an_application_until_enrollment(): void
    {
        $this->as('gestionnaire');
        $application = Application::factory()->create(['documents' => []]);

        Livewire::test(ViewApplication::class, ['record' => $application->getRouteKey()])
            ->callAction('transition', ['status' => ApplicationStatus::UNDER_REVIEW->value, 'comment' => 'Dossier complet'])
            ->callAction('interview', ['interview_at' => now()->addDays(2)->setTime(10, 0)->toDateTimeString(), 'interview_location' => 'EMSI']);

        Livewire::test(ViewApplication::class, ['record' => $application->getRouteKey()])
            ->callAction('transition', ['status' => ApplicationStatus::INTERVIEWED->value]);

        Livewire::test(ViewApplication::class, ['record' => $application->getRouteKey()])
            ->callAction('transition', ['status' => ApplicationStatus::ACCEPTED->value]);

        Livewire::test(ViewApplication::class, ['record' => $application->getRouteKey()])
            ->callAction('enroll');

        $application->refresh();
        $this->assertSame(ApplicationStatus::ENROLLED, $application->status);
        $this->assertNotNull($application->student_id);
        $this->assertSame(5, $application->events()->count());
    }

    public function test_6_record_a_tuition_payment(): void
    {
        $this->as('secretaire');
        $enrollment = Enrollment::factory()->create(['fee_amount_due' => 500000]);

        Livewire::test(CreateCashTransaction::class)
            ->fillForm(['direction' => 'in', 'category' => 'scolarite', 'enrollment_id' => $enrollment->id, 'amount' => 150000, 'method' => 'wave', 'occurred_on' => now()->toDateString()])
            ->call('create')
            ->assertHasNoFormErrors();

        $transaction = CashTransaction::firstOrFail();
        $this->assertSame(150000, $transaction->amount);
        $this->assertSame(350000, $enrollment->balance());
        $this->get(route('admin.cash.receipt', $transaction))->assertOk()->assertSee('150 000 FCFA');
    }
}
