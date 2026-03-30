<?php

use Illuminate\Support\Facades\Artisan;

it('lists discovered commands with dry-run', function () {
    $this->artisan('artisan-runner:discover', ['--dry-run' => true])
        ->assertSuccessful();
});

it('outputs json when requested', function () {
    $this->artisan('artisan-runner:discover', ['--output' => 'json', '--dry-run' => true])
        ->assertSuccessful();
});

it('outputs valid json', function () {
    Artisan::call('artisan-runner:discover', ['--output' => 'json', '--dry-run' => true]);
    $output = Artisan::output();

    $decoded = json_decode(trim($output), true);

    expect($decoded)->toBeArray()
        ->and($decoded)->not->toBeEmpty();
});
