<?php

namespace CleaniqueCoders\ArtisanRunner;

use CleaniqueCoders\ArtisanRunner\Actions\RunCommandAction;
use CleaniqueCoders\ArtisanRunner\Commands\DiscoverCommandsCommand;
use CleaniqueCoders\ArtisanRunner\Contracts\CommandRunnerContract;
use CleaniqueCoders\ArtisanRunner\Livewire\CommandRunner;
use Livewire\Finder\Finder;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ArtisanRunnerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('artisan-runner')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_command_logs_table')
            ->hasRoute('web')
            ->hasCommand(DiscoverCommandsCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->publish('assets');
            });
    }

    public function packageRegistered(): void
    {
        $this->app->bind(CommandRunnerContract::class, RunCommandAction::class);
    }

    public function packageBooted(): void
    {
        // Livewire 4 ships Finder (and addNamespace()); Livewire 3 has neither.
        // Don't method_exists() the Livewire facade — it proxies via
        // __callStatic, so that check is always false (issue #7).
        if (class_exists(Finder::class)) {
            Livewire::addNamespace(
                namespace: 'artisan-runner',
                classNamespace: 'CleaniqueCoders\\ArtisanRunner\\Livewire',
                classPath: __DIR__.'/Livewire',
                classViewPath: __DIR__.'/../resources/views/livewire',
            );
        } else {
            Livewire::component('artisan-runner::command-runner', CommandRunner::class);
        }

        $this->publishes([
            __DIR__.'/../art' => public_path('vendor/artisan-runner'),
        ], 'artisan-runner-assets');
    }
}
