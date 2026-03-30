<?php

use CleaniqueCoders\ArtisanRunner\Enums\CommandStatus;
use CleaniqueCoders\ArtisanRunner\Models\CommandLog;
use Illuminate\Support\Collection;

it('generates a uuid on creation', function () {
    $log = CommandLog::factory()->create();

    expect($log->uuid)->not->toBeNull()
        ->and($log->uuid)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
});

it('casts status to CommandStatus enum', function () {
    $log = CommandLog::factory()->pending()->create();

    expect($log->status)->toBeInstanceOf(CommandStatus::class)
        ->and($log->status)->toBe(CommandStatus::Pending);
});

it('casts parameters to a collection', function () {
    $log = CommandLog::factory()->create(['parameters' => ['--force' => true]]);

    expect($log->parameters)->toBeInstanceOf(Collection::class)
        ->and($log->parameters->get('--force'))->toBeTrue();
});

it('marks as running', function () {
    $log = CommandLog::factory()->pending()->create();

    $log->markAsRunning();

    expect($log->fresh()->status)->toBe(CommandStatus::Running)
        ->and($log->fresh()->started_at)->not->toBeNull();
});

it('marks as completed', function () {
    $log = CommandLog::factory()->running()->create();

    $log->markAsCompleted('Done.', 0);

    $fresh = $log->fresh();
    expect($fresh->status)->toBe(CommandStatus::Completed)
        ->and($fresh->output)->toBe('Done.')
        ->and($fresh->exit_code)->toBe(0)
        ->and($fresh->finished_at)->not->toBeNull();
});

it('marks as failed', function () {
    $log = CommandLog::factory()->running()->create();

    $log->markAsFailed('Error occurred.', 1);

    $fresh = $log->fresh();
    expect($fresh->status)->toBe(CommandStatus::Failed)
        ->and($fresh->output)->toBe('Error occurred.')
        ->and($fresh->exit_code)->toBe(1)
        ->and($fresh->finished_at)->not->toBeNull();
});

it('returns formatted duration', function () {
    $log = CommandLog::factory()->create([
        'started_at' => now()->subSeconds(65),
        'finished_at' => now(),
    ]);

    expect($log->formattedDuration())->toBe('1m 5s');
});

it('returns null duration when timestamps are missing', function () {
    $log = CommandLog::factory()->pending()->create();

    expect($log->formattedDuration())->toBeNull();
});

it('scopes completed logs', function () {
    CommandLog::factory()->completed()->count(2)->create();
    CommandLog::factory()->failed()->create();

    expect(CommandLog::completed()->count())->toBe(2);
});

it('scopes failed logs', function () {
    CommandLog::factory()->completed()->create();
    CommandLog::factory()->failed()->count(3)->create();

    expect(CommandLog::failed()->count())->toBe(3);
});

it('scopes recent logs', function () {
    CommandLog::factory()->create(['created_at' => now()->subDays(2)]);
    CommandLog::factory()->create(['created_at' => now()->subDays(10)]);

    expect(CommandLog::recent(7)->count())->toBe(1);
});
