<?php

use CleaniqueCoders\ArtisanRunner\Actions\RunCommandAction;
use CleaniqueCoders\ArtisanRunner\Enums\CommandStatus;
use CleaniqueCoders\ArtisanRunner\Jobs\RunArtisanCommandJob;
use CleaniqueCoders\ArtisanRunner\Models\CommandLog;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    config(['artisan-runner.allowed_commands' => [
        'config:clear' => [
            'label' => 'Clear Config',
            'description' => 'Remove the configuration cache file.',
            'group' => 'Cache',
            'parameters' => [],
        ],
        'migrate' => [
            'label' => 'Migrate',
            'description' => 'Run migrations.',
            'group' => 'Database',
            'parameters' => [
                ['name' => '--force', 'type' => 'boolean', 'label' => 'Force', 'default' => false],
            ],
        ],
    ]]);
    config(['artisan-runner.notification.enabled' => false]);
});

it('allows commands in the allowlist', function () {
    $action = new RunCommandAction;

    expect($action->isAllowed('config:clear'))->toBeTrue();
});

it('rejects commands not in the allowlist', function () {
    $action = new RunCommandAction;

    expect($action->isAllowed('down'))->toBeFalse();
});

it('throws exception when dispatching disallowed command', function () {
    $action = new RunCommandAction;

    expect(fn () => $action->dispatch('down'))->toThrow(\InvalidArgumentException::class);
});

it('creates a pending log on dispatch', function () {
    Queue::fake();

    $log = app(RunCommandAction::class)->dispatch('config:clear');

    expect($log->status)->toBe(CommandStatus::Pending)
        ->and($log->command)->toBe('config:clear')
        ->and($log->uuid)->not->toBeNull();

    Queue::assertPushed(RunArtisanCommandJob::class);
});

it('dispatches job with correct log', function () {
    Queue::fake();

    $log = app(RunCommandAction::class)->dispatch('config:clear');

    Queue::assertPushed(RunArtisanCommandJob::class, function ($job) use ($log) {
        return $job->log->id === $log->id;
    });
});

it('stores parameters on dispatch', function () {
    Queue::fake();

    $log = app(RunCommandAction::class)->dispatch('migrate', ['--force' => true]);

    expect($log->parameters->toArray())->toBe(['--force' => true]);
});

it('marks log as completed on successful execution', function () {
    $log = CommandLog::factory()->pending()->create(['command' => 'config:clear']);

    $result = app(RunCommandAction::class)->execute($log);

    expect($result->status)->toBe(CommandStatus::Completed)
        ->and($result->exit_code)->toBe(0)
        ->and($result->finished_at)->not->toBeNull();
});

it('marks log as failed on exception', function () {
    config(['artisan-runner.allowed_commands.invalid:command' => [
        'label' => 'Invalid',
        'parameters' => [],
    ]]);

    $log = CommandLog::factory()->pending()->create(['command' => 'invalid:command']);

    $result = app(RunCommandAction::class)->execute($log);

    expect($result->status)->toBe(CommandStatus::Failed)
        ->and($result->exit_code)->toBe(1);
});
