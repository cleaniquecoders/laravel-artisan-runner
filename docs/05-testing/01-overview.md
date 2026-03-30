# Testing Overview

How to test Laravel Artisan Runner features using Pest.

## Setup

Tests use Orchestra Testbench, Livewire, and Pest. The `TestCase` base class
registers both `LivewireServiceProvider` and `ArtisanRunnerServiceProvider`,
and configures an in-memory SQLite database.

```bash
composer test
```

## Test Conventions

- Test files mirror the `src/` directory structure under `tests/`
- Use Pest syntax (no PHPUnit classes)
- Architecture tests enforce no debugging functions (`dd`, `dump`, `ray`)

## Test Coverage

| Test File | What It Covers |
|-----------|---------------|
| `CommandStatusTest` | Enum values and case count |
| `CommandLogTest` | UUID generation, casts, scopes, status methods, duration |
| `RunCommandActionTest` | Allowlist enforcement, dispatch, execute, job queueing |
| `RunArtisanCommandJobTest` | Job configuration, queuing, execution |
| `CommandCompletedNotificationTest` | Channels, mail message, array representation |
| `ResolveCommandsActionTest` | Manual/auto/selection modes, caching, precedence |
| `DiscoverCommandsActionTest` | Discovery, exclusions, schema mapping, groups |
| `DiscoverCommandsCommandTest` | Dry-run, JSON output |
| `ConfigTest` | Default config values |
| `ArchTest` | No debug functions |

## Example Tests

### Allowlist Enforcement

```php
it('rejects commands not in allowlist', function () {
    $action = new RunCommandAction;

    expect(fn () => $action->dispatch('down'))
        ->toThrow(\InvalidArgumentException::class);
});
```

### Discovery Mode Resolution

```php
it('returns only allowed_commands in manual mode', function () {
    config(['artisan-runner.discovery_mode' => 'manual']);
    config(['artisan-runner.allowed_commands' => [
        'cache:clear' => ['label' => 'Clear Cache', 'parameters' => []],
    ]]);

    $commands = app(ResolveCommandsAction::class)->resolve();

    expect($commands)->toHaveKey('cache:clear')
        ->and($commands)->toHaveCount(1);
});
```

### Execute Updates Log

```php
it('marks log as completed on success', function () {
    $log = CommandLog::factory()->pending()->create([
        'command' => 'config:clear',
    ]);

    $result = app(RunCommandAction::class)->execute($log);

    expect($result->status)->toBe(CommandStatus::Completed)
        ->and($result->finished_at)->not->toBeNull();
});
```

## Running Tests

```bash
# All tests
composer test

# With coverage
composer test-coverage

# Specific file
vendor/bin/pest tests/CommandLogTest.php

# Filter by name
vendor/bin/pest --filter="creates a pending log"

# Static analysis
composer analyse
```

## Next Steps

- [Architecture](../02-architecture/02-contracts-and-actions.md) - What you are testing
- [Advanced](../06-advanced/README.md) - Extending the package
