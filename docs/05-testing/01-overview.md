# Testing Overview

How to test Laravel Artisan Runner features using Pest.

## Setup

Tests use Orchestra Testbench and Pest. The `TestCase` base class registers the service provider
and configures an in-memory database.

```bash
composer test
```

## Test Conventions

- Test files mirror the `src/` directory structure under `tests/`
- Use Pest syntax (no PHPUnit classes)
- Architecture tests enforce no debugging functions (`dd`, `dump`, `ray`)

## Example Tests

### Allowlist Enforcement

```php
it('rejects commands not in allowlist', function () {
    $action = new RunCommandAction;

    expect(fn () => $action->dispatch('down'))
        ->toThrow(\InvalidArgumentException::class);
});
```

### Dispatch Creates Pending Log

```php
it('creates a pending log on dispatch', function () {
    Queue::fake();

    config([
        'artisan-runner.allowed_commands' => [
            'cache:clear' => [
                'label'      => 'Clear Cache',
                'parameters' => [],
            ],
        ],
    ]);

    $log = app(RunCommandAction::class)->dispatch('cache:clear');

    expect($log->status)->toBe(CommandStatus::Pending);
    Queue::assertPushed(RunArtisanCommandJob::class);
});
```

### Execute Updates Log

```php
it('marks log as completed on success', function () {
    $log = CommandLog::factory()->pending()->create([
        'command' => 'cache:clear',
    ]);

    $result = app(RunCommandAction::class)->execute($log);

    expect($result->status)->toBe(CommandStatus::Completed)
        ->and($result->finished_at)->not->toBeNull();
});
```

### Failed Command

```php
it('marks log as failed on non-zero exit code', function () {
    $log = CommandLog::factory()->pending()->create([
        'command' => 'migrate',
    ]);

    // Simulate a failure scenario
    $result = app(RunCommandAction::class)->execute($log);

    expect($result->status)->toBe(CommandStatus::Failed)
        ->and($result->exit_code)->not->toBe(0);
});
```

## Running Tests

```bash
# All tests
composer test

# With coverage
composer test-coverage

# Static analysis
composer analyse
```

## Next Steps

- [Architecture](../02-architecture/02-contracts-and-actions.md) - What you are testing
- [Advanced](../06-advanced/README.md) - Extending the package
