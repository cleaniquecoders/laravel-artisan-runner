<?php

use CleaniqueCoders\ArtisanRunner\Enums\CommandStatus;

it('has the correct enum values', function () {
    expect(CommandStatus::Pending->value)->toBe('pending')
        ->and(CommandStatus::Running->value)->toBe('running')
        ->and(CommandStatus::Completed->value)->toBe('completed')
        ->and(CommandStatus::Failed->value)->toBe('failed');
});

it('has exactly four cases', function () {
    expect(CommandStatus::cases())->toHaveCount(4);
});
