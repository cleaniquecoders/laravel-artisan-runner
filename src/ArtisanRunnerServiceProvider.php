<?php

namespace CleaniqueCoders\ArtisanRunner;

use CleaniqueCoders\ArtisanRunner\Actions\RunCommandAction;
use CleaniqueCoders\ArtisanRunner\Commands\DiscoverCommandsCommand;
use CleaniqueCoders\ArtisanRunner\Contracts\CommandRunnerContract;
use CleaniqueCoders\ArtisanRunner\Livewire\CommandRunner;
use Livewire\Livewire;
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
            ->hasCommand(DiscoverCommandsCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->bind(CommandRunnerContract::class, RunCommandAction::class);
    }

    public function packageBooted(): void
    {
        if (method_exists(Livewire::class, 'addNamespace')) {
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
