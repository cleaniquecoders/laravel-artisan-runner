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
