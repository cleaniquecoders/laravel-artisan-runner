# Contracts and Actions

The contract-based architecture and action classes that power command execution
and discovery.

## CommandRunnerContract

The `CommandRunnerContract` defines three methods:

```php
namespace CleaniqueCoders\ArtisanRunner\Contracts;

interface CommandRunnerContract
{
    public function isAllowed(string $command): bool;
    public function dispatch(string $command, array $parameters = [], $ranBy = null): CommandLog;
    public function execute(CommandLog $log): CommandLog;
}
```

| Method | Purpose |
|--------|---------|
| `isAllowed()` | Checks the command against resolved allowed commands |
| `dispatch()` | Creates a `CommandLog` (pending) and pushes job to queue |
| `execute()` | Runs the Artisan command and updates the log |

## RunCommandAction

The default implementation of `CommandRunnerContract`.

### dispatch() Flow

1. Validate the command via `ResolveCommandsAction`
2. Create a `CommandLog` with status `pending`
3. Push `RunArtisanCommandJob` to the queue
4. Return the `CommandLog` instance

### execute() Flow

1. Mark the log as `running`
2. Build parameters from schema and stored values
3. Call `Artisan::call()` with the command and parameters
4. Capture output and exit code
5. Mark as `completed` (exit code 0) or `failed` (non-zero / exception)
6. Fire notification

## ResolveCommandsAction

Resolves which commands are available based on `discovery_mode`:

| Mode | Behavior |
|------|----------|
| `manual` | Returns `allowed_commands` from config only |
| `auto` | Discovers all commands via `DiscoverCommandsAction`, merges with `allowed_commands` |
| `selection` | Discovers only `included_commands`, merges with `allowed_commands` |

In `auto` and `selection` modes, manually defined `allowed_commands` take
precedence over discovered ones. Results are cached based on `discovery_cache_ttl`.

## DiscoverCommandsAction

Auto-discovers Artisan commands and generates parameter schemas:

- Iterates all registered Artisan commands
- Skips hidden commands
- Filters out `excluded_commands` and `excluded_namespaces`
- Maps each command's `InputDefinition` to a parameter schema:
  - Arguments become `text` type inputs
  - Boolean options (flags) become `boolean` type checkboxes
  - Value options with numeric defaults become `number` type
  - Other value options become `text` type
- Derives group names from command namespace (e.g., `cache:clear` becomes "Cache")
- Filters out global Symfony options (help, quiet, verbose, etc.)

## DiscoverCommandsCommand

Artisan command `artisan-runner:discover` for managing command discovery:

```bash
# Preview discovered commands
php artisan artisan-runner:discover --dry-run

# Output as JSON
php artisan artisan-runner:discover --output=json

# Write to config file
php artisan artisan-runner:discover
```

## Swapping Implementations

To use a custom implementation, rebind in a service provider:

```php
$this->app->bind(CommandRunnerContract::class, YourCustomAction::class);
```

## Next Steps

- [Data Layer](03-data-layer.md) - CommandLog model details
- [Configuration](../03-configuration/01-config-file.md) - Discovery config
