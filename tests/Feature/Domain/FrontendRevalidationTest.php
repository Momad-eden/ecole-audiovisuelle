<?php

namespace Tests\Feature\Domain;

use App\Models\News;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FrontendRevalidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_public_content_asks_the_website_to_refresh(): void
    {
        config(['services.frontend.url' => 'https://emsi.test', 'services.frontend.revalidate_secret' => 'secret']);
        Http::fake(['emsi.test/*' => Http::response(['revalidated' => true])]);

        News::create(['title' => 'Portes ouvertes', 'content' => 'x', 'is_published' => true]);
        Setting::current()->update(['phone' => '+221 77 000 00 00']);

        Http::assertSent(fn ($request) => $request->url() === 'https://emsi.test/api/revalidate'
            && $request['secret'] === 'secret'
            && $request['tags'] === ['content']);
    }

    public function test_nothing_is_sent_when_the_website_is_not_configured_or_unreachable(): void
    {
        config(['services.frontend.revalidate_secret' => null]);
        Http::fake();

        News::create(['title' => 'Sans site', 'content' => 'x']);
        Http::assertNothingSent();

        config(['services.frontend.url' => 'https://emsi.test', 'services.frontend.revalidate_secret' => 'secret']);
        Http::fake(fn () => throw new ConnectionException('down'));

        News::create(['title' => 'Site injoignable', 'content' => 'x']);
        $this->assertDatabaseHas('news', ['title' => 'Site injoignable']);
    }
}
