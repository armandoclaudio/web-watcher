<?php

use App\Http\Controllers\AlertController;
use Illuminate\Support\Facades\Route;

Route::middleware('basic-auth')->group(function () {
    Route::get('/', fn () => redirect()->route('alerts.index'));
    Route::resource('alerts', AlertController::class)->except(['show']);
});
