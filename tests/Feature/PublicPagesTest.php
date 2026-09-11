<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_accessible(): void
    {
        $response = $this->get(route('public.home'));

        $response->assertStatus(200);
        $response->assertViewIs('public.home');
    }

    public function test_project_page_is_accessible(): void
    {
        $response = $this->get(route('public.project'));

        $response->assertStatus(200);
        $response->assertViewIs('public.project');
        $response->assertSee('Grand Théâtre National');
    }

    public function test_vae_page_is_accessible(): void
    {
        $response = $this->get(route('public.vae'));

        $response->assertStatus(200);
        $response->assertViewIs('public.vae');
        $response->assertSee('Dispositif VAE');
    }

    public function test_about_page_is_accessible(): void
    {
        Partner::create([
            'name' => 'Grand Théâtre National',
            'is_active' => true,
        ]);

        $response = $this->get(route('public.about'));

        $response->assertStatus(200);
        $response->assertViewIs('public.about');
        $response->assertSee('Grand Théâtre National');
    }

    public function test_courses_index_page_is_accessible(): void
    {
        $course = Course::create([
            'title' => 'Cadre & Caméra Cinéma',
            'slug' => 'cadre-camera-cinema',
            'description' => 'Formation pratique au cadrage cinéma.',
            'category' => 'Image & Caméra',
            'level' => 'Intermédiaire',
            'duration' => '6 mois',
            'price' => 350000,
            'is_active' => true,
        ]);

        $response = $this->get(route('public.courses.index'));

        $response->assertStatus(200);
        $response->assertViewIs('public.courses.index');
        $response->assertSee('Cadre & Caméra Cinéma');
    }

    public function test_courses_show_page_is_accessible(): void
    {
        $course = Course::create([
            'title' => 'Montage & Étalonnage',
            'slug' => 'montage-etalonnage',
            'description' => 'Maîtrisez Premiere Pro et DaVinci Resolve.',
            'category' => 'Post-production',
            'duration' => '6 mois',
            'price' => 450000,
            'is_active' => true,
        ]);

        $relatedCourse = Course::create([
            'title' => 'Effets Spéciaux & VFX',
            'slug' => 'effets-speciaux-vfx',
            'description' => 'Formation After Effects et 3D.',
            'category' => 'Post-production',
            'is_active' => true,
        ]);

        $response = $this->get(route('public.courses.show', $course));

        $response->assertStatus(200);
        $response->assertViewIs('public.formation-show');
        $response->assertSee('Montage & Étalonnage');
        $response->assertSee('Maîtrisez Premiere Pro et DaVinci Resolve.');
        $response->assertSee('Effets Spéciaux & VFX');
    }

    public function test_gallery_page_is_accessible(): void
    {
        Gallery::create([
            'title' => 'Tournage Plateau A',
            'type' => 'image',
            'file_path' => 'galleries/test.jpg',
            'is_active' => true,
        ]);

        $response = $this->get(route('public.gallery.index'));

        $response->assertStatus(200);
        $response->assertViewIs('public.gallery');
        $response->assertSee('Tournage Plateau A');
    }

    public function test_news_index_and_show_pages_are_accessible(): void
    {
        $news = News::create([
            'title' => 'Masterclass avec Alain Gomis',
            'slug' => 'masterclass-avec-alain-gomis',
            'excerpt' => 'Une rencontre exceptionnelle avec le réalisateur.',
            'content' => 'Le réalisateur primé Alain Gomis a partagé son expérience avec nos étudiants.',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $indexResponse = $this->get(route('public.news.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertViewIs('public.news.index');
        $indexResponse->assertSee('Masterclass avec Alain Gomis');

        $showResponse = $this->get(route('public.news.show', $news));
        $showResponse->assertStatus(200);
        $showResponse->assertViewIs('public.news.show');
        $showResponse->assertSee('Alain Gomis');
    }

    public function test_unpublished_news_returns_404(): void
    {
        $news = News::create([
            'title' => 'Article Brouillon',
            'slug' => 'article-brouillon',
            'content' => 'Contenu non publié.',
            'is_published' => false,
        ]);

        $response = $this->get(route('public.news.show', $news));
        $response->assertStatus(404);
    }
}
