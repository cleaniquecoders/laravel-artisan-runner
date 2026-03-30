<?php

use CleaniqueCoders\ArtisanRunner\Enums\CommandStatus;
use CleaniqueCoders\ArtisanRunner\Jobs\RunArtisanCommandJob;
use CleaniqueCoders\ArtisanRunner\Models\CommandLog;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    config(['artisan-runner.allowed_commands' => [
        'config:clear' => [
            'label' => 'Clear Cache',
            'description' => 'Flush the application cache.',
            'group' => 'Cache',
            'parameters' => [],
        ],
    ]]);
    config(['artisan-runner.notification.enabled' => false]);
});

it('is queued', function () {
    Queue::fake();

    $log = CommandLog::factory()->pending()->create(['command' => 'config:clear']);
    RunArtisanCommandJob::dispatch($log);

    Queue::assertPushed(RunArtisanCommandJob::class);
});

it('has correct configuration', function () {
    $log = CommandLog::factory()->pending()->create(['command' => 'config:clear']);
    $job = new RunArtisanCommandJob($log);

    expect($job->tries)->toBe(1)
        ->and($job->timeout)->toBe(300);
});

it('executes the command and updates the log', function () {
    $log = CommandLog::factory()->pending()->create(['command' => 'config:clear']);

    $job = new RunArtisanCommandJob($log);
    app()->call([$job, 'handle']);

    $fresh = $log->fresh();
    expect($fresh->status)->toBe(CommandStatus::Completed)
        ->and($fresh->exit_code)->toBe(0);
});
