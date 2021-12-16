<?php

namespace App\Jobs;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\Notification as MailNotification;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class WatchWeb implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $urls = Alert::groupBy('url')->pluck('url');
        $urls->each(function($url) {
            $keywords = Alert::where('url', $url)->pluck('keywords', 'id');
            $keywords = Alert::pluck('keywords', 'id');
            $response = Http::get($url);

            $xml = simplexml_load_string($response->body(),'SimpleXMLElement', LIBXML_NOCDATA);
            $data = collect(((array) $xml)['entry']);

            $matches = $data->map(fn($entry) => [
                'date' => (string) $entry->published,
                'title' => (string) $entry->title,
                'link' => (string) ((array) ((array) $entry)['link'])['@attributes']['href'],
            ])->map(function($item) use ($keywords) {
                $matchedKeywords = $keywords->filter(function($keyword, $id) use ($item) {
                    $notification = Notification::where('alert_id', $id)
                        ->where('external_id', $item['date'])
                        ->get();

                    return Str::contains(Str::upper($item['title']), Str::upper($keyword))
                        && $notification->count() == 0;
                });

                return [
                    'date' => $item['date'],
                    'title' => $item['title'],
                    'link' => $item['link'],
                    'ids' => $matchedKeywords->keys(),
                ];
            })->filter(fn($item) => sizeof($item['ids']) > 0);

            if($matches->count() == 0) return null;

            Mail::to(env('WATCHER_EMAIL'))->queue(new MailNotification($matches));

            collect($matches)->each(function($item) use ($url) {
                Alert::where('url', $url)
                    ->whereIn('id', $item['ids'])
                    ->each(function($alert) use ($item) {
                        Notification::create([
                            'alert_id' => $alert->id,
                            'external_id' => $item['date'],
                        ]);
                    });
            });
        });
    }
}
