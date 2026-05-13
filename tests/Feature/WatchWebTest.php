<?php

use App\Jobs\WatchWeb;
use App\Mail\AlertNotification;
use App\Models\Alert;
use App\Models\WebNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function atomFeed(string $title, string $published, string $link): string
{
    return <<<XML
    <?xml version="1.0" encoding="UTF-8"?>
    <feed>
        <entry>
            <published>{$published}</published>
            <title>{$title}</title>
            <link href="{$link}"/>
        </entry>
    </feed>
    XML;
}

test('queues an email when a keyword matches a new feed entry', function () {
    Mail::fake();
    Http::preventStrayRequests();

    $url = 'https://example.com/feed.xml';
    $alert = Alert::factory()->create(['url' => $url, 'keywords' => 'laravel']);

    Http::fake([
        $url => Http::response(atomFeed('Laravel 13 Released', '2026-01-01T00:00:00Z', 'https://example.com/post'), 200),
    ]);

    (new WatchWeb)->handle();

    Mail::assertQueued(AlertNotification::class);
    expect(WebNotification::where('alert_id', $alert->id)->count())->toBe(1);
});

test('does not queue an email when no keywords match', function () {
    Mail::fake();
    Http::preventStrayRequests();

    $url = 'https://example.com/feed.xml';
    Alert::factory()->create(['url' => $url, 'keywords' => 'symfony']);

    Http::fake([
        $url => Http::response(atomFeed('Laravel 13 Released', '2026-01-01T00:00:00Z', 'https://example.com/post'), 200),
    ]);

    (new WatchWeb)->handle();

    Mail::assertNothingQueued();
    expect(WebNotification::count())->toBe(0);
});

test('does not send duplicate notifications for the same entry', function () {
    Mail::fake();
    Http::preventStrayRequests();

    $url = 'https://example.com/feed.xml';
    $alert = Alert::factory()->create(['url' => $url, 'keywords' => 'laravel']);

    WebNotification::factory()->create([
        'alert_id' => $alert->id,
        'external_id' => '2026-01-01T00:00:00Z',
    ]);

    Http::fake([
        $url => Http::response(atomFeed('Laravel 13 Released', '2026-01-01T00:00:00Z', 'https://example.com/post'), 200),
    ]);

    (new WatchWeb)->handle();

    Mail::assertNothingQueued();
    expect(WebNotification::where('alert_id', $alert->id)->count())->toBe(1);
});

test('skips url when the http request fails', function () {
    Mail::fake();
    Http::preventStrayRequests();

    $url = 'https://example.com/feed.xml';
    Alert::factory()->create(['url' => $url, 'keywords' => 'laravel']);

    Http::fake([
        $url => Http::response('', 500),
    ]);

    (new WatchWeb)->handle();

    Mail::assertNothingQueued();
});

test('keyword matching is case-insensitive', function () {
    Mail::fake();
    Http::preventStrayRequests();

    $url = 'https://example.com/feed.xml';
    $alert = Alert::factory()->create(['url' => $url, 'keywords' => 'LARAVEL']);

    Http::fake([
        $url => Http::response(atomFeed('laravel 13 released', '2026-01-01T00:00:00Z', 'https://example.com/post'), 200),
    ]);

    (new WatchWeb)->handle();

    Mail::assertQueued(AlertNotification::class);
    expect(WebNotification::where('alert_id', $alert->id)->count())->toBe(1);
});
