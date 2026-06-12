<?php

use CleaniqueCoders\ArtisanRunner\Livewire\CommandRunner;
use Livewire\Livewire;

it('resolves the command-runner component', function () {
    Livewire::test('artisan-runner::command-runner')
        ->assertOk();
});

it('resolves the command-runner component by class', function () {
    Livewire::test(CommandRunner::class)
        ->assertOk();
});
