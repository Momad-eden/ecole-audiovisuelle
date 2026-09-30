<?php

namespace Tests\Feature\Translation;

use App\Enums\TranslationStatus;
use App\Models\Concerns\HasTranslations;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Program;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/** Table des traductions, trait des fiches traduites et langue des demandes publiques. */
class TranslationStorageTest extends TestCase
{
    use RefreshDatabase;

    private function faq(): Faq
    {
        return Faq::create(['group' => 'admission', 'question' => 'Comment postuler ?', 'answer' => 'En ligne.', 'position' => 1]);
    }

    private function store(object $model, string $field, string $value, TranslationStatus $status = TranslationStatus::AUTO, ?string $hash = null): Translation
    {
        return $model->translations()->create([
            'field' => $field, 'locale' => 'en', 'value' => $value, 'status' => $status,
            'source_hash' => $hash ?? $model->sourceHash($field),
        ]);
    }

    public function test_translations_migration_rolls_back_cleanly(): void
    {
        $path = 'database/migrations/2026_09_30_120000_create_translations_table.php';
        $this->artisan('migrate:rollback', ['--path' => $path])->assertSuccessful();
        $this->assertFalse(Schema::hasTable('translations'));

        $this->artisan('migrate', ['--path' => $path])->assertSuccessful();
        $this->assertTrue(Schema::hasTable('translations'));
    }

    public function test_locale_migration_rolls_back_cleanly(): void
    {
        $path = 'database/migrations/2026_09_30_120100_add_locale_to_public_requests.php';
        $this->artisan('migrate:rollback', ['--path' => $path])->assertSuccessful();
        foreach (['applications', 'contact_messages', 'booking_requests'] as $table) {
            $this->assertFalse(Schema::hasColumn($table, 'locale'));
        }

        $this->artisan('migrate', ['--path' => $path])->assertSuccessful();
        foreach (['applications', 'contact_messages', 'booking_requests'] as $table) {
            $this->assertTrue(Schema::hasColumn($table, 'locale'));
        }
    }

    public function test_requests_default_to_french(): void
    {
        $message = ContactMessage::create(['subject' => 'Info', 'name' => 'A', 'email' => 'a@b.sn', 'message' => 'Bonjour'])->fresh();
        $this->assertSame('fr', $message->locale);

        $english = ContactMessage::create(['subject' => 'Info', 'name' => 'A', 'email' => 'a@b.sn', 'message' => 'Hello', 'locale' => 'en'])->fresh();
        $this->assertSame('en', $english->locale);
    }

    public function test_translated_returns_english_when_present_and_french_otherwise(): void
    {
        $faq = $this->faq();
        $this->assertSame('Comment postuler ?', $faq->translated('question', 'en'));

        $this->store($faq, 'question', 'How do I apply?');
        $faq->load('translations');

        $this->assertSame('How do I apply?', $faq->translated('question', 'en'));
        $this->assertSame('Comment postuler ?', $faq->translated('question', 'fr'));
        $this->assertSame('En ligne.', $faq->translated('answer', 'en'));
    }

    public function test_translated_decodes_json_for_blocks_and_array_fields(): void
    {
        $page = Page::create(['title' => 'Accueil', 'slug' => 'accueil', 'type' => 'standard',
            'blocks' => [['type' => 'text', 'data' => ['text' => 'Bonjour']]],
            'draft_blocks' => [['type' => 'text', 'data' => ['text' => 'Brouillon']]]]);
        $this->store($page, 'blocks', json_encode([['type' => 'text', 'data' => ['text' => 'Hello']]]));

        $program = Program::create(['title' => 'Son', 'slug' => 'son', 'audience' => 'school', 'kind' => 'certificate', 'skills' => ['Mixage']]);
        $this->store($program, 'skills', json_encode(['Mixing']));

        $this->assertSame([['type' => 'text', 'data' => ['text' => 'Hello']]], $page->fresh()->translated('blocks', 'en'));
        $this->assertSame(['Mixing'], $program->fresh()->translated('skills', 'en'));
        $this->assertSame(['Mixage'], $program->fresh()->translated('skills', 'fr'));
    }

    public function test_source_hash_of_page_blocks_uses_published_blocks_only(): void
    {
        $blocks = [['type' => 'text', 'data' => ['text' => 'Bonjour é/']]];
        $page = Page::create(['title' => 'A', 'slug' => 'a', 'type' => 'standard', 'blocks' => $blocks, 'draft_blocks' => [['x' => 1]]]);

        $this->assertSame(hash('sha256', json_encode([['data' => ['text' => 'Bonjour é/'], 'type' => 'text']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), $page->sourceHash('blocks'));
        $this->assertSame(hash('sha256', 'A'), $page->sourceHash('title'));
        $this->assertSame(hash('sha256', ''), $page->sourceHash('seo'));
    }

    public function test_outdated_fields_lists_changed_missing_and_failed_but_not_empty(): void
    {
        $faq = $this->faq();
        $this->store($faq, 'question', 'How do I apply?');
        $faq->load('translations');
        $this->assertSame(['answer'], $faq->outdatedFields());

        $this->store($faq, 'answer', 'Online.');
        $faq->load('translations');
        $this->assertSame([], $faq->outdatedFields());

        $faq->update(['question' => 'Comment s\'inscrire ?']);
        $this->assertSame(['question'], $faq->fresh()->outdatedFields());

        $faq->translation('question')->update(['source_hash' => $faq->sourceHash('question'), 'status' => TranslationStatus::FAILED]);
        $this->assertSame(['question'], $faq->fresh()->outdatedFields());

        // Français vide : jamais à traduire.
        $page = Page::create(['title' => 'A', 'slug' => 'a', 'type' => 'standard']);
        $this->assertSame(['title'], $page->outdatedFields());
    }

    public function test_deleting_a_record_deletes_its_translations(): void
    {
        $faq = $this->faq();
        $other = $this->faq();
        $this->store($faq, 'question', 'x');
        $this->store($other, 'question', 'y');

        $faq->delete();

        $this->assertSame(0, Translation::where('translatable_id', $faq->id)->where('translatable_type', 'faq')->count());
        $this->assertSame(1, $other->translations()->count());
    }

    public function test_soft_delete_keeps_translations_and_force_delete_removes_them(): void
    {
        $program = Program::create(['title' => 'Son', 'slug' => 'son', 'audience' => 'school', 'kind' => 'certificate']);
        $this->store($program, 'title', 'Sound');

        $program->delete();
        $this->assertSame(1, Translation::count());

        $program->forceDelete();
        $this->assertSame(0, Translation::count());
    }

    public function test_translatable_fields_and_untranslated_models(): void
    {
        $this->assertSame(['question', 'answer'], (new Faq)->translatableFields());
        $this->assertSame(['title', 'blocks', 'seo'], (new Page)->translatableFields());
        $this->assertNotContains(HasTranslations::class, class_uses_recursive(Partner::class));
    }

    public function test_status_labels(): void
    {
        $this->assertSame('Traduction automatique', TranslationStatus::AUTO->label());
        $this->assertSame('Relue', TranslationStatus::REVIEWED->label());
        $this->assertSame('Échec', TranslationStatus::FAILED->label());
    }

    public function test_morph_type_is_a_short_alias(): void
    {
        $faq = $this->faq();
        $this->store($faq, 'question', 'x');

        $this->assertSame('faq', Translation::first()->translatable_type);
        $this->assertTrue(Translation::first()->translatable->is($faq));
        $this->assertSame('page', (new Page)->getMorphClass());
    }

    public function test_invalid_stored_json_falls_back_to_french(): void
    {
        $program = Program::create(['title' => 'Son', 'slug' => 'son', 'audience' => 'school', 'kind' => 'certificate', 'skills' => ['Mixage']]);
        $this->store($program, 'skills', '["Mixing"');
        $this->assertSame(['Mixage'], $program->fresh()->translated('skills', 'en'));

        $program->translation('skills')->update(['value' => '"just a string"']);
        $this->assertSame(['Mixage'], $program->fresh()->translated('skills', 'en'));
    }

    public function test_source_hash_ignores_key_order_but_not_list_order(): void
    {
        $a = [['type' => 'text', 'data' => ['text' => 'A', 'align' => 'left']], ['type' => 'cta', 'data' => ['x' => 1]]];
        $b = [['data' => ['align' => 'left', 'text' => 'A'], 'type' => 'text'], ['data' => ['x' => 1], 'type' => 'cta']];
        $c = [$a[1], $a[0]];

        $p = new Page(['blocks' => $a]);
        $this->assertSame($p->sourceHash('blocks'), (new Page(['blocks' => $b]))->sourceHash('blocks'));
        $this->assertNotSame($p->sourceHash('blocks'), (new Page(['blocks' => $c]))->sourceHash('blocks'));
    }
}
