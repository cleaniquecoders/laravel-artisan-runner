<?php

use CleaniqueCoders\ArtisanRunner\Livewire\CommandRunner;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('artisan-runner.route.prefix', 'artisan-runner'),
    'middleware' => config('artisan-runner.route.middleware', ['web', 'auth']),
    'as' => config('artisan-runner.route.name', 'artisan-runner.'),
], function () {
    Route::get('/', function () {
        return view('artisan-runner::index');
    })->name('index');
});
