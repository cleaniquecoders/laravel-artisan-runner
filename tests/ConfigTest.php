<?php

it('has default allowed commands', function () {
    expect(config('artisan-runner.allowed_commands'))->toBeArray()
        ->and(config('artisan-runner.allowed_commands'))->not->toBeEmpty();
});

it('has notification config', function () {
    expect(config('artisan-runner.notification'))->toBeArray()
        ->and(config('artisan-runner.notification.enabled'))->toBeBool()
        ->and(config('artisan-runner.notification.channels'))->toBeArray();
});

it('has log retention days', function () {
    expect(config('artisan-runner.log_retention_days'))->toBe(30);
});

it('has route config', function () {
    expect(config('artisan-runner.route.prefix'))->toBe('artisan-runner')
        ->and(config('artisan-runner.route.middleware'))->toBeArray()
        ->and(config('artisan-runner.route.name'))->toBe('artisan-runner.');
});

it('has discovery mode defaulting to manual', function () {
    expect(config('artisan-runner.discovery_mode'))->toBe('manual');
});

it('has excluded commands config', function () {
    expect(config('artisan-runner.excluded_commands'))->toBeArray()
        ->and(config('artisan-runner.excluded_commands'))->toContain('down');
});

it('has excluded namespaces config', function () {
    expect(config('artisan-runner.excluded_namespaces'))->toBeArray()
        ->and(config('artisan-runner.excluded_namespaces'))->toContain('make');
});

it('has included commands config', function () {
    expect(config('artisan-runner.included_commands'))->toBeArray();
});

it('has discovery cache ttl config', function () {
    expect(config('artisan-runner.discovery_cache_ttl'))->toBe(3600);
});
