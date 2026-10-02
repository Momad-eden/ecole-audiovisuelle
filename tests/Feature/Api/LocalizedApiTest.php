<?php

namespace Tests\Feature\Api;

use App\Enums\Activity;
use App\Enums\ArtworkKind;
use App\Enums\Audience;
use App\Enums\CohortStatus;
use App\Enums\FundingMode;
use App\Enums\PriceUnit;
use App\Enums\ProgramKind;
use App\Enums\PublicationStatus;
use App\Enums\TranslationStatus;
use App\Models\AgendaEvent;
use App\Models\Application;
use App\Models\Artwork;
use App\Models\BookingRequest;
use App\Models\ContactMessage;
use App\Models\EquipmentCategory;
use App\Models\EquipmentItem;
use App\Models\Faq;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\Offering;
use App\Models\Page;
use App\Models\Place;
use App\Models\Program;
use App\Models\RentalPack;
use App\Models\Room;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Track;
use App\Support\PreviewToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/** API publique en anglais (?locale=en), repli champ par champ sur le français (spec R2 §4). */
class LocalizedApiTest extends TestCase
{
    use RefreshDatabase;

    private function translate(object $model, string $field, mixed $value): void
    {
        // Champ structuré : état par texte, comme l'écrivent la tâche de traduction et l'admin.
        $leaves = null;
        if ($model->hasStructuredTranslation($field) && is_array($value)) {
            $french = $model->frenchLeaves($field);
            $leaves = [];
            foreach (array_keys($value) as $key) {
                if (isset($french[(string) $key])) {
                    $leaves[(string) $key] = ['h' => $model::leafHash($french[(string) $key]), 's' => TranslationStatus::AUTO->value];
                }
            }
        }
        $model->translations()->create([
            'field' => $field, 'locale' => 'en', 'status' => TranslationStatus::AUTO,
            'value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value,
            'source_hash' => $model->sourceHash($field), 'leaves' => $leaves,
        ]);
    }

    private function emsiPage(): Page
    {
        $page = Page::create(['title' => 'L\'école', 'slug' => 'emsi', 'domain' => 'emsi', 'seo' => ['title' => 'École de cinéma'], 'draft_blocks' => [
            ['type' => 'hero', 'data' => ['title' => 'Apprendre le son', 'image' => 'pages/hero.jpg', 'image_alt' => 'Une console', 'link_url' => '/candidater', 'link_label' => 'Candidater']],
            ['type' => 'text', 'data' => ['body' => '<p>Bienvenue à l\'école</p>']],
        ]]);
        $page->publish();
        $page->refresh();

        $this->translate($page, 'title', 'The school');
        $this->translate($page, 'seo', ['title' => 'Film school']);
        $this->translate($page, 'blocks', [
            'hero#0:title' => 'Learning sound', 'hero#0:image_alt' => 'A mixing desk', 'hero#0:link_label' => 'Apply',
            'text#0:body' => '<p>Welcome to the school</p>',
        ]);

        return $page;
    }

    public function test_page_in_english_translates_title_seo_and_block_texts_keeping_links(): void
    {
        $this->emsiPage();

        $this->getJson('/api/v1/public/pages/emsi?locale=en')
            ->assertOk()
            ->assertJsonPath('data.title', 'The school')
            ->assertJsonPath('data.seo.title', 'Film school')
            ->assertJsonPath('data.locale', 'en')
            ->assertJsonPath('data.contentLocale', 'en')
            ->assertJsonPath('data.alternates', ['fr' => '/emsi', 'en' => '/en/emsi'])
            ->assertJsonPath('data.blocks.0.data.title', 'Learning sound')
            ->assertJsonPath('data.blocks.0.data.image.alt', 'A mixing desk')
            ->assertJsonPath('data.blocks.0.data.image.url', url('/storage/pages/hero.jpg'))
            ->assertJsonPath('data.blocks.0.data.linkUrl', '/candidater')
            ->assertJsonPath('data.blocks.0.data.linkLabel', 'Apply')
            ->assertJsonPath('data.blocks.1.data.body', '<p>Welcome to the school</p>');

        $this->getJson('/api/v1/public/pages/emsi')
            ->assertJsonPath('data.title', 'L\'école')
            ->assertJsonPath('data.locale', 'fr')
            ->assertJsonPath('data.contentLocale', 'fr')
            ->assertJsonPath('data.blocks.0.data.title', 'Apprendre le son');
    }

    public function test_block_added_after_translation_stays_french_while_others_are_english(): void
    {
        $page = $this->emsiPage();
        $page->update(['draft_blocks' => [
            ['type' => 'cta', 'data' => ['title' => 'Nouveau bloc', 'url' => '/contact']],
            ...$page->blocks,
        ]]);
        $page->publish();

        $this->getJson('/api/v1/public/pages/emsi?locale=en')
            ->assertJsonPath('data.blocks.0.data.title', 'Nouveau bloc')
            ->assertJsonPath('data.blocks.1.data.title', 'Learning sound')
            ->assertJsonPath('data.blocks.2.data.body', '<p>Welcome to the school</p>');
    }

    public function test_untranslated_page_falls_back_to_french(): void
    {
        $page = Page::create(['title' => 'Accueil', 'slug' => 'accueil', 'type' => 'home', 'draft_blocks' => [
            ['type' => 'hero', 'data' => ['title' => 'Bienvenue']],
        ]]);
        $page->publish();

        $this->getJson('/api/v1/public/pages/accueil?locale=en')
            ->assertOk()
            ->assertJsonPath('data.title', 'Accueil')
            ->assertJsonPath('data.locale', 'en')
            ->assertJsonPath('data.contentLocale', 'fr')
            ->assertJsonPath('data.alternates', ['fr' => '/', 'en' => '/en'])
            ->assertJsonPath('data.blocks.0.data.title', 'Bienvenue');
    }

    public function test_unsupported_locale_falls_back_to_french(): void
    {
        $this->emsiPage();

        $this->getJson('/api/v1/public/pages/emsi?locale=de')
            ->assertJsonPath('data.title', 'L\'école')
            ->assertJsonPath('data.locale', 'fr')
            ->assertJsonPath('data.blocks.0.data.title', 'Apprendre le son');
    }

    public function test_draft_preview_stays_french(): void
    {
        $page = $this->emsiPage();
        $token = PreviewToken::make('page', $page->id);

        $this->getJson('/api/v1/public/preview?locale=en&token='.$token)
            ->assertOk()
            ->assertJsonPath('data.title', 'L\'école')
            ->assertJsonPath('data.blocks.0.data.title', 'Apprendre le son');
    }

    public function test_site_in_english_translates_menus_settings_and_domains(): void
    {
        $settings = Setting::current();
        $settings->update(['description' => 'Une école', 'opening_hours' => 'Lundi–vendredi', 'seo_title' => 'EMSI Dakar', 'seo_description' => 'Former aux métiers']);
        $this->translate($settings, 'description', 'A school');
        $this->translate($settings, 'opening_hours', 'Monday–Friday');
        $this->translate($settings, 'seo_description', 'Training for careers');

        $parent = MenuItem::create(['label' => 'L\'école', 'url' => '/emsi', 'location' => 'main']);
        $child = MenuItem::create(['label' => 'Formations', 'description' => 'Toutes les formations', 'url' => '/emsi/formations', 'location' => 'main', 'parent_id' => $parent->id]);
        MenuItem::create(['label' => 'Contact', 'url' => '/contact', 'location' => 'main']);
        $this->translate($parent, 'label', 'The school');
        $this->translate($child, 'label', 'Programmes');
        $this->translate($child, 'description', 'All our programmes');

        $this->getJson('/api/v1/public/site?locale=en')
            ->assertOk()
            ->assertJsonPath('data.settings.description', 'A school')
            ->assertJsonPath('data.settings.openingHours', 'Monday–Friday')
            ->assertJsonPath('data.settings.seoTitle', 'EMSI Dakar')
            ->assertJsonPath('data.settings.seoDescription', 'Training for careers')
            ->assertJsonPath('data.menus.main.0.label', 'The school')
            ->assertJsonPath('data.menus.main.0.children.0.label', 'Programmes')
            ->assertJsonPath('data.menus.main.0.children.0.url', '/emsi/formations')
            ->assertJsonPath('data.menus.main.0.children.0.description', 'All our programmes')
            ->assertJsonPath('data.menus.main.1.label', 'Contact')
            ->assertJsonPath('data.domains.general.label', 'General');

        $this->getJson('/api/v1/public/site')
            ->assertJsonPath('data.menus.main.0.label', 'L\'école')
            ->assertJsonPath('data.domains.general.label', 'Général');
    }

    private function content(): void
    {
        $program = Program::factory()->create(['title' => 'Technicien son', 'summary' => 'Le son live', 'audience' => Audience::SCHOOL]);
        $this->translate($program, 'title', 'Sound technician');
        $this->translate($program, 'summary', 'Live sound');

        $news = News::create(['title' => 'Rentrée', 'excerpt' => 'Elle arrive', 'content' => '<p>x</p>', 'is_published' => true, 'published_at' => now()->subDay()]);
        $this->translate($news, 'title', 'Back to school');

        $place = Place::create(['name' => 'Dakar', 'kind' => 'campus', 'city' => 'Dakar', 'tagline' => 'Au cœur de la ville', 'highlights' => ['Studio'], 'status' => PublicationStatus::PUBLISHED]);
        $this->translate($place, 'tagline', 'In the heart of the city');
        $this->translate($place, 'highlights', ['Recording studio']);

        $faq = Faq::create(['group' => 'general', 'question' => 'Comment postuler ?', 'answer' => 'En ligne.', 'position' => 1]);
        $this->translate($faq, 'question', 'How do I apply?');
    }

    public function test_listing_endpoints_are_translated(): void
    {
        $this->content();

        $this->getJson('/api/v1/public/programs?locale=en')
            ->assertJsonPath('data.0.title', 'Sound technician')
            ->assertJsonPath('data.0.summary', 'Live sound');
        $this->getJson('/api/v1/public/news?locale=en')->assertJsonPath('data.0.title', 'Back to school')->assertJsonPath('data.0.excerpt', 'Elle arrive');
        $this->getJson('/api/v1/public/places?locale=en')
            ->assertJsonPath('data.0.name', 'Dakar')
            ->assertJsonPath('data.0.tagline', 'In the heart of the city')
            ->assertJsonPath('data.0.highlights', ['Recording studio']);
        $this->getJson('/api/v1/public/faqs?locale=en')
            ->assertJsonPath('data.0.question', 'How do I apply?')
            ->assertJsonPath('data.0.answer', 'En ligne.');

        $this->getJson('/api/v1/public/programs')->assertJsonPath('data.0.title', 'Technicien son');
        $this->getJson('/api/v1/public/faqs')->assertJsonPath('data.0', ['group' => 'general', 'question' => 'Comment postuler ?', 'answer' => 'En ligne.']);
    }

    public function test_dynamic_blocks_are_translated(): void
    {
        $this->content();
        $page = Page::create(['title' => 'Accueil', 'slug' => 'accueil', 'type' => 'home', 'draft_blocks' => [
            ['type' => 'programs', 'data' => ['audience' => 'school']],
            ['type' => 'news', 'data' => ['limit' => 3]],
            ['type' => 'places', 'data' => []],
            ['type' => 'faq', 'data' => ['group' => 'general']],
        ]]);
        $page->publish();

        $this->getJson('/api/v1/public/pages/accueil?locale=en')
            ->assertJsonPath('data.blocks.0.data.items.0.title', 'Sound technician')
            ->assertJsonPath('data.blocks.1.data.items.0.title', 'Back to school')
            ->assertJsonPath('data.blocks.2.data.items.0.tagline', 'In the heart of the city')
            ->assertJsonPath('data.blocks.3.data.items.0.question', 'How do I apply?');

        $this->getJson('/api/v1/public/pages/accueil')
            ->assertJsonPath('data.blocks.0.data.items.0.title', 'Technicien son')
            ->assertJsonPath('data.blocks.3.data.items.0', ['question' => 'Comment postuler ?', 'answer' => 'En ligne.']);
    }

    public function test_nested_names_are_translated_in_lists_without_lazy_loading(): void
    {
        foreach (['Salle du Son' => 'Sound room', 'Salle Image' => 'Image room'] as $fr => $en) {
            $room = Room::create(['name' => $fr, 'accent_color' => '#F5B83D', 'status' => PublicationStatus::PUBLISHED]);
            $this->translate($room, 'name', $en);
            $track = Track::factory()->create(['name' => "Filière {$fr}", 'room_id' => $room->id]);
            $this->translate($track, 'name', "Track {$en}");
            Artwork::create(['title' => "Œuvre {$fr}", 'kind' => 'audio', 'room_id' => $room->id, 'track_id' => $track->id, 'status' => PublicationStatus::PUBLISHED]);
            $service = Service::create(['name' => "Service {$fr}", 'activity' => 'studio', 'status' => PublicationStatus::PUBLISHED]);
            $this->translate($service, 'name', "Service {$en}");
            $event = AgendaEvent::create(['title' => "Concert {$fr}", 'slug' => Str::slug($fr), 'activity' => 'space', 'starts_at' => now()->addWeek(), 'status' => PublicationStatus::PUBLISHED]);
            $this->translate($event, 'title', "Concert {$en}");
        }
        $offering = Offering::factory()->create();
        $offering->update(['track_id' => Track::first()->id]);
        Offering::factory()->create();
        $this->translate($offering->cohort->program, 'title', 'Sound programme');

        $this->getJson('/api/v1/public/rooms?locale=en')->assertOk()
            ->assertJsonPath('data.0.name', 'Sound room')->assertJsonPath('data.0.tracks.0.name', 'Track Sound room');
        $this->getJson('/api/v1/public/artworks?locale=en')->assertOk()
            ->assertJsonFragment(['room' => ['name' => 'Image room', 'slug' => Room::find(2)->slug, 'accentColor' => '#F5B83D']]);
        $this->getJson('/api/v1/public/tracks?locale=en')->assertOk()->assertJsonPath('data.0.room.name', 'Sound room');
        $this->getJson('/api/v1/public/services?locale=en')->assertOk()->assertJsonPath('data.0.name', 'Service Sound room');
        $this->getJson('/api/v1/public/agenda?locale=en')->assertOk()->assertJsonPath('data.0.title', 'Concert Sound room');
        $this->getJson('/api/v1/public/offerings?locale=en')->assertOk()
            ->assertJsonFragment(['label' => 'Sound programme — '.$offering->cohort->name.' · Track Sound room']);
    }

    public function test_enum_labels_follow_the_request_locale(): void
    {
        $category = EquipmentCategory::create(['name' => 'Sonorisation', 'position' => 1]);
        EquipmentItem::create(['name' => 'Line array K2', 'equipment_category_id' => $category->id, 'usage' => 'rental', 'price_from' => 150000, 'price_unit' => 'day', 'status' => PublicationStatus::PUBLISHED]);
        Service::create(['name' => 'Mixage', 'activity' => 'studio', 'price_from' => 25000, 'price_unit' => 'track', 'position' => 1, 'status' => PublicationStatus::PUBLISHED]);
        Service::create(['name' => 'Mastering', 'activity' => 'studio', 'position' => 2, 'status' => PublicationStatus::PUBLISHED]);
        RentalPack::create(['name' => 'Pack concert', 'price_from' => 1250000, 'price_unit' => 'event', 'status' => PublicationStatus::PUBLISHED]);
        AgendaEvent::create(['title' => 'Concert', 'slug' => 'concert', 'activity' => 'events', 'starts_at' => now()->addWeek(), 'status' => PublicationStatus::PUBLISHED]);
        Artwork::create(['title' => 'Paysage sonore', 'kind' => 'live', 'status' => PublicationStatus::PUBLISHED]);
        $offering = Offering::factory()->create(['funding_mode' => FundingMode::SPONSORED]);
        $offering->cohort->program->update(['kind' => ProgramKind::SHORT_COURSE]);
        $slug = $offering->cohort->program->slug;

        $this->getJson('/api/v1/public/equipment?locale=en')->assertJsonPath('data.0.priceLabel', 'From 150,000 FCFA per day');
        $this->getJson('/api/v1/public/services?activity=studio&locale=en')
            ->assertJsonPath('data.0.priceLabel', 'From 25,000 FCFA per track')
            ->assertJsonPath('data.1.priceLabel', 'On request');
        $this->getJson('/api/v1/public/packs?locale=en')->assertJsonPath('data.0.priceLabel', 'From 1,250,000 FCFA per event');
        $this->getJson('/api/v1/public/agenda?locale=en')->assertJsonPath('data.0.activityLabel', 'Impact Live Events');
        $this->getJson('/api/v1/public/artworks?locale=en')->assertJsonPath('data.0.kindLabel', 'Live show / live recording');
        $this->getJson("/api/v1/public/programs/{$slug}?locale=en")
            ->assertJsonPath('data.kindLabel', 'Short course / workshop')
            ->assertJsonPath('data.cohorts.0.statusLabel', 'Applications open')
            ->assertJsonPath('data.cohorts.0.offerings.0.fundingLabel', 'Funded');
        $this->getJson('/api/v1/public/offerings?locale=en')->assertJsonPath('data.0.fundingLabel', 'Funded');

        $this->getJson('/api/v1/public/equipment')->assertJsonPath('data.0.priceLabel', 'À partir de 150 000 FCFA / jour');
        $this->getJson('/api/v1/public/services?activity=studio')
            ->assertJsonPath('data.0.priceLabel', 'À partir de 25 000 FCFA / titre')
            ->assertJsonPath('data.1.priceLabel', 'Sur devis');
        $this->getJson('/api/v1/public/packs')->assertJsonPath('data.0.priceLabel', 'À partir de 1 250 000 FCFA / événement');
        $this->getJson('/api/v1/public/artworks')->assertJsonPath('data.0.kindLabel', 'Spectacle / captation live');
        $this->getJson("/api/v1/public/programs/{$slug}")
            ->assertJsonPath('data.kindLabel', 'Stage / atelier court')
            ->assertJsonPath('data.cohorts.0.statusLabel', 'Candidatures ouvertes')
            ->assertJsonPath('data.cohorts.0.offerings.0.fundingLabel', 'Pris en charge');
    }

    public function test_every_enum_case_has_an_english_label_and_keeps_its_french_label(): void
    {
        foreach ([Activity::class, ArtworkKind::class, ProgramKind::class, CohortStatus::class, FundingMode::class, PriceUnit::class] as $enum) {
            foreach ($enum::cases() as $case) {
                $this->assertSame($case->getLabel(), $case->labelFor('fr'), $enum.'::'.$case->name);
                $this->assertNotSame('', $case->labelFor('en'), $enum.'::'.$case->name);
            }
        }
        $this->assertSame('par heure', PriceUnit::HOUR->getLabel());
        $this->assertSame('per hour', PriceUnit::HOUR->labelFor('en'));
    }

    public function test_saving_or_deleting_a_translation_refreshes_the_website(): void
    {
        $faq = Faq::create(['group' => 'general', 'question' => 'Comment postuler ?', 'answer' => 'En ligne.', 'position' => 1]);
        config(['services.frontend.url' => 'https://emsi.test', 'services.frontend.revalidate_secret' => 'secret']);
        Http::fake(['emsi.test/*' => Http::response(['revalidated' => true])]);

        $this->translate($faq, 'question', 'How do I apply?');
        Http::assertSentCount(1);

        // Écrite sous transaction (tâche de traduction) : rien n'est demandé avant la validation.
        DB::transaction(function () use ($faq) {
            $faq->translation('question')->update(['value' => 'How can I apply?']);
            Http::assertSentCount(1);
        });
        Http::assertSentCount(2);

        $faq->translation('question')->delete();
        Http::assertSentCount(3);
        Http::assertSent(fn ($request) => $request->url() === 'https://emsi.test/api/revalidate' && $request['tags'] === ['content']);
    }

    public function test_english_news_list_uses_a_bounded_number_of_queries(): void
    {
        foreach (range(1, 5) as $n) {
            $news = News::create(['title' => "Actualité {$n}", 'content' => '<p>x</p>', 'is_published' => true, 'published_at' => now()->subDays($n)]);
            $this->translate($news, 'title', "News {$n}");
        }

        DB::enableQueryLog();
        $this->getJson('/api/v1/public/news?locale=en')->assertOk()->assertJsonPath('data.4.title', 'News 5');
        $this->assertLessThanOrEqual(4, count(DB::getQueryLog()));
    }

    private function applicationPayload(Offering $offering, array $overrides = []): array
    {
        return array_merge([
            'offeringId' => $offering->id, 'firstName' => 'Awa', 'lastName' => 'Ndiaye', 'phone' => '+221 77 123 45 67', 'consent' => true,
        ], $overrides);
    }

    public function test_application_validation_messages_are_in_english(): void
    {
        $offering = Offering::factory()->create();

        $this->postJson('/api/v1/public/applications?locale=en', $this->applicationPayload($offering, ['firstName' => '', 'consent' => false, 'phone' => 'abc']))
            ->assertUnprocessable()
            ->assertJsonPath('errors.firstName.0', 'The first name field is required.')
            ->assertJsonPath('errors.consent.0', 'You must accept the processing of your data to submit your application.')
            ->assertJsonPath('errors.phone.0', 'The phone number is not valid (e.g. +221 77 123 45 67).');

        $offering->update(['is_open' => false]);
        $this->postJson('/api/v1/public/applications?locale=en', $this->applicationPayload($offering))
            ->assertJsonPath('errors.offeringId.0', 'This programme is not accepting applications at the moment.');

        $this->postJson('/api/v1/public/applications', $this->applicationPayload($offering, ['firstName' => '']))
            ->assertJsonPath('errors.firstName.0', 'Le champ prénom est obligatoire.')
            ->assertJsonPath('errors.offeringId.0', 'Cette formation n\'accepte pas de candidature pour le moment.');
    }

    public function test_contact_and_booking_validation_messages_are_in_english(): void
    {
        $this->postJson('/api/v1/public/contact-messages?locale=en', ['subject' => 'other', 'message' => 'Hi', 'consent' => true])
            ->assertJsonPath('errors.name.0', 'The name field is required.');
        $this->postJson('/api/v1/public/support?locale=en', ['supportType' => 'donation', 'message' => 'Hi', 'consent' => true])
            ->assertJsonPath('errors.name.0', 'The name field is required.');
        $this->postJson('/api/v1/public/booking-requests?locale=en', ['type' => 'studio_session', 'name' => 'Awa', 'phone' => 'abc', 'consent' => true])
            ->assertJsonPath('errors.phone.0', 'Invalid number (e.g. +221 77 123 45 67).');
    }

    public function test_requests_store_the_request_locale(): void
    {
        $offering = Offering::factory()->create();

        $this->postJson('/api/v1/public/applications?locale=en', $this->applicationPayload($offering))->assertCreated();
        $this->postJson('/api/v1/public/contact-messages?locale=en', ['subject' => 'other', 'name' => 'Awa', 'email' => 'a@example.com', 'message' => 'Hi', 'consent' => true])->assertCreated();
        $this->postJson('/api/v1/public/support?locale=en', ['name' => 'Awa', 'email' => 'a@example.com', 'supportType' => 'donation', 'message' => 'Hi', 'consent' => true])->assertCreated();
        $this->postJson('/api/v1/public/booking-requests?locale=en', ['type' => 'studio_session', 'name' => 'Awa', 'phone' => '+221 77 123 45 67', 'consent' => true])->assertCreated();

        $this->assertSame('en', Application::firstOrFail()->locale);
        $this->assertSame(['en', 'en'], ContactMessage::orderBy('id')->pluck('locale')->all());
        $this->assertSame('en', BookingRequest::firstOrFail()->locale);

        $this->postJson('/api/v1/public/contact-messages?locale=de', ['subject' => 'other', 'name' => 'Awa', 'email' => 'a@example.com', 'message' => 'Salut', 'consent' => true])->assertCreated();
        $this->assertSame('fr', ContactMessage::latest('id')->firstOrFail()->locale);
    }
}
