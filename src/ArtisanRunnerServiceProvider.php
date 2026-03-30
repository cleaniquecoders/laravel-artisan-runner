<?php

namespace CleaniqueCoders\ArtisanRunner;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use CleaniqueCoders\ArtisanRunner\Commands\ArtisanRunnerCommand;

class ArtisanRunnerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-artisan-runner')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_artisan_runner_table')
            ->hasCommand(ArtisanRunnerCommand::class);
    }
}
