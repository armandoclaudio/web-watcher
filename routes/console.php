<?php

use App\Jobs\WatchWeb;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new WatchWeb)
    ->cron(config('services.watcher.cron', '*/10 * * * *'))
    ->withoutOverlapping();
