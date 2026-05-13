<?php

use App\Models\Alert;
use App\Models\WebNotification;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('alert can be created with factory', function () {
    $alert = Alert::factory()->create();

    expect($alert->url)->not->toBeEmpty()
        ->and($alert->keywords)->not->toBeEmpty();
});

test('alert has many web notifications', function () {
    $alert = Alert::factory()->create();
    WebNotification::factory()->count(3)->create(['alert_id' => $alert->id]);

    expect($alert->webNotifications)->toHaveCount(3);
});

test('web notification belongs to an alert', function () {
    $notification = WebNotification::factory()->create();

    expect($notification->alert)->toBeInstanceOf(Alert::class);
});
