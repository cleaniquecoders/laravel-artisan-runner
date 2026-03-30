# Log Pruning

Automatically clean up old command logs based on the configured retention period.

## Configuration

Set the retention period in `config/artisan-runner.php`:

```php
'log_retention_days' => 30,  // null = keep forever
```

## Schedule the Pruning

Add to your application's scheduled commands in `routes/console.php` (Laravel 11+) or `app/Console/Kernel.php`:

```php
use CleaniqueCoders\ArtisanRunner\Models\CommandLog;

Schedule::call(function () {
    $days = config('artisan-runner.log_retention_days');

    if ($days) {
        CommandLog::where('created_at', '<', now()->subDays($days))->delete();
    }
})->daily()->name('artisan-runner:prune-logs');
```

This runs daily and deletes any `CommandLog` records older than the configured retention period.

## Disabling Pruning

Set `log_retention_days` to `null` to keep logs indefinitely:

```php
'log_retention_days' => null,
```

> **Warning**: Without pruning, the `command_logs` table will grow indefinitely. Monitor table size in production.

## Next Steps

- [Custom Notifications](02-custom-notifications.md)
- [Configuration](../03-configuration/01-config-file.md)
