# Contracts and Actions

The contract-based architecture that powers command execution.

## CommandRunnerContract

The `CommandRunnerContract` defines three methods:

```php
namespace CleaniqueCoders\ArtisanRunner\Contracts;

interface CommandRunnerContract
{
    public function isAllowed(string $command): bool;
    public function dispatch(string $command, array $parameters = []): CommandLog;
    public function execute(CommandLog $log): CommandLog;
}
```

| Method | Purpose |
|--------|---------|
| `isAllowed()` | Checks the command against `config('artisan-runner.allowed_commands')` |
| `dispatch()` | Creates a `CommandLog` (pending) and pushes `RunArtisanCommandJob` to the queue |
| `execute()` | Runs the actual Artisan command and updates the log with output and status |

## RunCommandAction

The default implementation of `CommandRunnerContract`. Bound in the container via the service provider:

```php
$this->app->bind(CommandRunnerContract::class, RunCommandAction::class);
```

### dispatch() Flow

1. Validate the command is allowed (throws `InvalidArgumentException` if not)
2. Create a `CommandLog` with status `pending`
3. Push `RunArtisanCommandJob` to the queue
4. Return the `CommandLog` instance

### execute() Flow

1. Mark the log as `running`
2. Call `Artisan::call()` with the command and parameters
3. Capture output and exit code
4. Mark as `completed` (exit code 0) or `failed` (non-zero exit code)
5. Fire notification

## Swapping Implementations

To use a custom implementation, rebind in a service provider:

```php
$this->app->bind(CommandRunnerContract::class, YourCustomAction::class);
```

## Next Steps

- [Data Layer](03-data-layer.md) - CommandLog model details
- [Configuration](../03-configuration/01-config-file.md) - Allowed commands config
