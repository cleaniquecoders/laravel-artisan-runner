<?php

use CleaniqueCoders\ArtisanRunner\Actions\ResolveCommandsAction;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    config(['artisan-runner.notification.enabled' => false]);
    config(['artisan-runner.discovery_cache_ttl' => null]);
});

it('returns only allowed_commands in manual mode', function () {
    config(['artisan-runner.discovery_mode' => 'manual']);
    config(['artisan-runner.allowed_commands' => [
        'cache:clear' => [
            'label' => 'Clear Cache',
            'description' => 'Flush the application cache.',
            'group' => 'Cache',
            'parameters' => [],
        ],
    ]]);

    $commands = app(ResolveCommandsAction::class)->resolve();

    expect($commands)->toHaveCount(1)
        ->and($commands)->toHaveKey('cache:clear');
});

it('returns auto-discovered commands in auto mode', function () {
    config(['artisan-runner.discovery_mode' => 'auto']);
    config(['artisan-runner.allowed_commands' => []]);
    config(['artisan-runner.excluded_commands' => []]);
    config(['artisan-runner.excluded_namespaces' => []]);

    $commands = app(ResolveCommandsAction::class)->resolve();

    expect($commands)->not->toBeEmpty()
        ->and(count($commands))->toBeGreaterThan(1);
});

it('returns only included commands in selection mode', function () {
    config(['artisan-runner.discovery_mode' => 'selection']);
    config(['artisan-runner.allowed_commands' => []]);
    config(['artisan-runner.included_commands' => ['cache:clear', 'config:clear']]);
    config(['artisan-runner.excluded_commands' => []]);
    config(['artisan-runner.excluded_namespaces' => []]);

    $commands = app(ResolveCommandsAction::class)->resolve();

    expect($commands)->toHaveCount(2)
        ->and($commands)->toHaveKeys(['cache:clear', 'config:clear']);
});

it('gives manual entries precedence over discovered ones', function () {
    config(['artisan-runner.discovery_mode' => 'auto']);
    config(['artisan-runner.allowed_commands' => [
        'cache:clear' => [
            'label' => 'Custom Label',
            'description' => 'Custom description.',
            'group' => 'Custom',
            'parameters' => [],
        ],
    ]]);
    config(['artisan-runner.excluded_commands' => []]);
    config(['artisan-runner.excluded_namespaces' => []]);

    $commands = app(ResolveCommandsAction::class)->resolve();

    expect($commands['cache:clear']['label'])->toBe('Custom Label')
        ->and($commands['cache:clear']['group'])->toBe('Custom');
});

it('caches results when ttl is set', function () {
    config(['artisan-runner.discovery_mode' => 'auto']);
    config(['artisan-runner.discovery_cache_ttl' => 3600]);
    config(['artisan-runner.excluded_commands' => []]);
    config(['artisan-runner.excluded_namespaces' => []]);

    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(function ($key, $ttl) {
            return $key === 'artisan-runner:discovered-commands' && $ttl === 3600;
        })
        ->andReturn(['cache:clear' => ['label' => 'Cached', 'description' => '', 'group' => 'Cache', 'parameters' => []]]);

    $commands = app(ResolveCommandsAction::class)->resolve();

    expect($commands)->toHaveKey('cache:clear');
});

it('defaults to manual mode for invalid discovery_mode', function () {
    config(['artisan-runner.discovery_mode' => 'invalid']);
    config(['artisan-runner.allowed_commands' => [
        'cache:clear' => [
            'label' => 'Clear Cache',
            'description' => '',
            'group' => 'Cache',
            'parameters' => [],
        ],
    ]]);

    $commands = app(ResolveCommandsAction::class)->resolve();

    expect($commands)->toHaveCount(1)
        ->and($commands)->toHaveKey('cache:clear');
});

it('flushes the discovery cache', function () {
    Cache::shouldReceive('forget')
        ->once()
        ->with('artisan-runner:discovered-commands');

    app(ResolveCommandsAction::class)->flush();
});
