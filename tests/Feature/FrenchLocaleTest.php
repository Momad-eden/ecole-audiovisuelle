<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrenchLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_messages_are_in_french(): void
    {
        $this->from(route('public.admissions.create'))
            ->followingRedirects()
            ->post(route('public.admissions.store'), [])
            ->assertSee('Le champ prénom est obligatoire.')
            ->assertSee('Le champ formation est obligatoire.')
            ->assertDontSee('field is required');
    }

    public function test_dates_are_in_french(): void
    {
        News::create([
            'title' => 'Rentrée', 'slug' => 'rentree', 'content' => 'x',
            'is_published' => true, 'published_at' => '2026-09-15 10:00:00',
        ]);

        $this->get(route('public.news.show', 'rentree'))
            ->assertOk()
            ->assertSee('15 septembre 2026')
            ->assertDontSee('September');
    }

    public function test_profile_page_is_french_and_readable(): void
    {
        $user = User::factory()->create(['role' => 'directeur']);

        $this->actingAs($user)->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Informations du profil')
            ->assertSee('text-gray-900', false)
            ->assertDontSee('Profile Information');
    }

    public function test_page_titles_do_not_mention_laravel(): void
    {
        $this->get(route('login'))->assertOk()->assertDontSee('<title>Laravel', false);

        $user = User::factory()->create(['role' => 'directeur']);
        $this->actingAs($user)->get(route('profile.edit'))->assertDontSee('<title>Laravel', false);
    }

    public function test_new_student_form_shows_validation_errors(): void
    {
        $user = User::factory()->create(['role' => 'directeur']);
        Course::factory()->create();

        $this->actingAs($user)
            ->from(route('students.create'))
            ->followingRedirects()
            ->post(route('students.store'), [])
            ->assertSee('Le champ prénom est obligatoire.');
    }
}
