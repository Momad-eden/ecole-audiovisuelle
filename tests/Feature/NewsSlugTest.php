<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsSlugTest extends TestCase
{
    use RefreshDatabase;

    private User $communication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->communication = User::factory()->create(['role' => 'communication']);
    }

    public function test_two_articles_with_the_same_title_get_distinct_slugs(): void
    {
        foreach ([1, 2] as $i) {
            $this->actingAs($this->communication)
                ->post(route('news.store'), ['title' => 'Lancement du Volet 1', 'content' => "Texte {$i}"])
                ->assertRedirect(route('news.index'));
        }

        $this->assertEqualsCanonicalizing(
            ['lancement-du-volet-1', 'lancement-du-volet-1-2'],
            News::pluck('slug')->all()
        );
    }

    public function test_changing_the_title_keeps_the_slug(): void
    {
        $news = News::create(['title' => 'Titre initial', 'slug' => 'titre-initial', 'content' => 'x']);

        $this->actingAs($this->communication)
            ->put(route('news.update', $news), ['title' => 'Nouveau titre', 'content' => 'x'])
            ->assertRedirect(route('news.index'));

        $this->assertSame('titre-initial', $news->fresh()->slug);
        $this->assertSame('Nouveau titre', $news->fresh()->title);
    }
}
