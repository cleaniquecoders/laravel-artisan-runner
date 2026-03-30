<?php

use CleaniqueCoders\ArtisanRunner\Actions\DiscoverCommandsAction;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

it('discovers registered artisan commands', function () {
    $action = new DiscoverCommandsAction;

    $commands = $action->discover();

    expect($commands)->toBeArray()
        ->and($commands)->not->toBeEmpty();
});

it('skips hidden commands', function () {
    $action = new DiscoverCommandsAction;

    $commands = $action->discover();

    // Hidden commands from Artisan::all() should be filtered out
    $allCommands = Artisan::all();
    $hiddenNames = collect($allCommands)
        ->filter(fn ($cmd) => $cmd->isHidden())
        ->keys();

    foreach ($hiddenNames as $name) {
        expect($commands)->not->toHaveKey($name);
    }
});

it('skips excluded commands', function () {
    config(['artisan-runner.excluded_commands' => ['cache:clear']]);

    $action = new DiscoverCommandsAction;

    $commands = $action->discover();

    expect($commands)->not->toHaveKey('cache:clear');
});

it('skips excluded namespaces', function () {
    config(['artisan-runner.excluded_namespaces' => ['cache']]);

    $action = new DiscoverCommandsAction;

    $commands = $action->discover();

    expect($commands)->not->toHaveKey('cache:clear')
        ->and($commands)->not->toHaveKey('cache:forget');
});

it('maps a command with correct schema structure', function () {
    $action = new DiscoverCommandsAction;

    $commands = Artisan::all();
    $command = $commands['cache:clear'];
    $mapped = $action->mapCommand($command);

    expect($mapped)->toHaveKeys(['label', 'description', 'group', 'parameters'])
        ->and($mapped['group'])->toBe('Cache')
        ->and($mapped['label'])->toBe('Cache Clear')
        ->and($mapped['parameters'])->toBeArray();
});

it('derives group from command namespace', function () {
    $action = new DiscoverCommandsAction;

    $commands = $action->discover();

    if (isset($commands['cache:clear'])) {
        expect($commands['cache:clear']['group'])->toBe('Cache');
    }

    if (isset($commands['config:clear'])) {
        expect($commands['config:clear']['group'])->toBe('Config');
    }
});

it('maps boolean options correctly', function () {
    $action = new DiscoverCommandsAction;

    $definition = new InputDefinition([
        new InputOption('force', null, InputOption::VALUE_NONE, 'Force the operation'),
    ]);

    $params = $action->mapDefinition($definition);

    expect($params)->toHaveCount(1)
        ->and($params[0]['name'])->toBe('--force')
        ->and($params[0]['type'])->toBe('boolean')
        ->and($params[0]['default'])->toBeFalse();
});

it('maps value options with numeric default to number type', function () {
    $action = new DiscoverCommandsAction;

    $definition = new InputDefinition([
        new InputOption('step', null, InputOption::VALUE_REQUIRED, 'Steps', 1),
    ]);

    $params = $action->mapDefinition($definition);

    expect($params[0]['type'])->toBe('number')
        ->and($params[0]['default'])->toBe(1);
});

it('maps arguments as text type', function () {
    $action = new DiscoverCommandsAction;

    $definition = new InputDefinition([
        new InputArgument('name', InputArgument::REQUIRED, 'The name'),
    ]);

    $params = $action->mapDefinition($definition);

    expect($params[0]['name'])->toBe('name')
        ->and($params[0]['type'])->toBe('text')
        ->and($params[0]['required'])->toBeTrue();
});

it('skips global symfony options', function () {
    $action = new DiscoverCommandsAction;

    $definition = new InputDefinition([
        new InputOption('help', null, InputOption::VALUE_NONE, 'Display help'),
        new InputOption('quiet', null, InputOption::VALUE_NONE, 'Quiet mode'),
        new InputOption('force', null, InputOption::VALUE_NONE, 'Force'),
    ]);

    $params = $action->mapDefinition($definition);

    expect($params)->toHaveCount(1)
        ->and($params[0]['name'])->toBe('--force');
});

it('discovers only selected commands', function () {
    config(['artisan-runner.excluded_commands' => []]);
    config(['artisan-runner.excluded_namespaces' => []]);

    $action = new DiscoverCommandsAction;

    $selected = $action->discoverSelected(['cache:clear', 'config:clear']);

    expect($selected)->toHaveCount(2)
        ->and($selected)->toHaveKeys(['cache:clear', 'config:clear']);
});
