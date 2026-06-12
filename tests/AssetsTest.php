<?php

use CleaniqueCoders\ArtisanRunner\ArtisanRunnerServiceProvider;
use Illuminate\Support\Facades\File;

it('ships runtime logo assets in the dist path', function () {
    expect(ArtisanRunnerServiceProvider::DIST_PATH.'/logo-icon-dark.svg')->toBeFile()
        ->and(ArtisanRunnerServiceProvider::DIST_PATH.'/logo-icon-light.svg')->toBeFile();
});

it('publishes assets to the public vendor directory', function () {
    $target = public_path('vendor/artisan-runner');
    File::deleteDirectory($target);

    $this->artisan('vendor:publish', ['--tag' => 'artisan-runner-assets'])->assertSuccessful();

    expect($target.'/logo-icon-dark.svg')->toBeFile()
        ->and($target.'/logo-icon-light.svg')->toBeFile();

    File::deleteDirectory($target);
});

it('renders the layout with inline logos when assets are not published', function () {
    File::deleteDirectory(public_path('vendor/artisan-runner'));

    $html = view('artisan-runner::layout')->render();

    expect($html)->toContain('data:image/svg+xml;base64,');
});

it('renders the layout with published assets when available', function () {
    $this->artisan('vendor:publish', ['--tag' => 'artisan-runner-assets'])->assertSuccessful();

    $html = view('artisan-runner::layout')->render();

    expect($html)->toContain('vendor/artisan-runner/logo-icon-light.svg')
        ->not->toContain('data:image/svg+xml;base64,');

    File::deleteDirectory(public_path('vendor/artisan-runner'));
});
