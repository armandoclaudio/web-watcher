<?php

namespace App\Jobs;

use App\Mail\AlertNotification;
use App\Models\Alert;
use App\Models\WebNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WatchWeb implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $urls = Alert::query()->groupBy('url')->pluck('url');

        $urls->each(function (string $url): void {
            $keywords = Alert::where('url', $url)->pluck('keywords', 'id');

            $response = Http::timeout(30)->get($url);

            if ($response->failed()) {
                Log::warning("WatchWeb: failed to fetch {$url}", ['status' => $response->status()]);

                return;
            }

            $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);

            $data = $this->parseEntries($xml);

            if ($data === null) {
                Log::warning("WatchWeb: could not parse feed for {$url}");

                return;
            }

            $matches = $data->map(function (array $item) use ($keywords): array {
                $matchedKeywords = $keywords->filter(function (string $keyword, int $id) use ($item): bool {
                    $alreadyNotified = WebNotification::where('alert_id', $id)
                        ->where('external_id', $item['date'])
                        ->exists();

                    return ! $alreadyNotified
                        && Str::contains(Str::upper($item['title']), Str::upper($keyword));
                });

                return [
                    'date' => $item['date'],
                    'title' => $item['title'],
                    'link' => $item['link'],
                    'ids' => $matchedKeywords->keys(),
                ];
            })->filter(fn (array $item): bool => count($item['ids']) > 0);

            if ($matches->isEmpty()) {
                return;
            }

            Mail::to(config('services.watcher.email'))->queue(new AlertNotification($matches));

            $matches->each(function (array $item) use ($url): void {
                Alert::where('url', $url)
                    ->whereIn('id', $item['ids'])
                    ->each(function (Alert $alert) use ($item): void {
                        WebNotification::create([
                            'alert_id' => $alert->id,
                            'external_id' => $item['date'],
                        ]);
                    });
            });
        });
    }

    /** @return \Illuminate\Support\Collection<int, array{date: string, title: string, link: string}>|null */
    private function parseEntries(mixed $xml): ?\Illuminate\Support\Collection
    {
        if ($xml === false) {
            return null;
        }

        // Atom feed
        if (isset($xml->entry)) {
            return collect(iterator_to_array($xml->entry, false))
                ->map(fn ($entry) => [
                    'date' => (string) $entry->published,
                    'title' => (string) $entry->title,
                    'link' => (string) $entry->link['href'],
                ]);
        }

        // RSS 2.0 feed
        if (isset($xml->channel->item)) {
            return collect(iterator_to_array($xml->channel->item, false))
                ->map(fn ($item) => [
                    'date' => (string) $item->pubDate,
                    'title' => (string) $item->title,
                    'link' => (string) $item->link,
                ]);
        }

        return null;
    }
}
