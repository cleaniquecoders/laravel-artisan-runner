# Extending

How to swap or extend the command runner implementation.

## Custom Command Runner

Create a class implementing `CommandRunnerContract`:

```php
namespace App\Services;

use CleaniqueCoders\ArtisanRunner\Contracts\CommandRunnerContract;
use CleaniqueCoders\ArtisanRunner\Models\CommandLog;

class CustomCommandRunner implements CommandRunnerContract
{
    public function isAllowed(string $command): bool
    {
        // Custom allowlist logic (e.g., database-driven)
        return AllowedCommand::where('command', $command)->exists();
    }

    public function dispatch(string $command, array $parameters = []): CommandLog
    {
        // Custom dispatch logic
    }

    public function execute(CommandLog $log): CommandLog
    {
        // Custom execution logic
    }
}
```

## Register Your Implementation

Rebind in a service provider:

```php
use CleaniqueCoders\ArtisanRunner\Contracts\CommandRunnerContract;
use App\Services\CustomCommandRunner;

public function register(): void
{
    $this->app->bind(CommandRunnerContract::class, CustomCommandRunner::class);
}
```

The Livewire component and job resolve the contract from the container, so your implementation is used automatically.

## Use Cases

- **Database-driven allowlist**: Store allowed commands in a database table instead of config
- **Custom logging**: Add additional audit fields or external logging
- **Approval workflows**: Require approval before execution
- **Rate limiting**: Throttle command execution per user

## Next Steps

- [Contracts and Actions](../02-architecture/02-contracts-and-actions.md) - Contract details
- [Testing](../05-testing/01-overview.md) - Test your custom implementation
