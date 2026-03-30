# Config File Reference

Complete reference for `config/artisan-runner.php`.

## Full Config Structure

```php
return [
    'allowed_commands' => [
        'cache:clear' => [
            'label'       => 'Clear Cache',
            'description' => 'Flush the application cache.',
            'group'       => 'Cache',
            'parameters'  => [],
        ],
    ],

    'notification' => [
        'enabled'    => true,
        'channels'   => ['database', 'mail'],
        'notifiable' => [
            'model'      => \App\Models\User::class,
            'identifier' => 'email',
            'value'      => env('ARTISAN_RUNNER_NOTIFY_EMAIL', ''),
        ],
    ],

    'log_retention_days' => 30,

    'route' => [
        'prefix'     => 'artisan-runner',
        'middleware'  => ['web', 'auth'],
        'name'       => 'artisan-runner.',
    ],
];
```

## Allowed Commands

Each entry maps an Artisan command signature to its UI configuration.

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `label` | string | Yes | Display name in the UI |
| `description` | string | Yes | Explanation shown in the UI |
| `group` | string | Yes | Grouping for UI organization |
| `parameters` | array | Yes | List of parameter definitions (can be empty) |

### Parameter Definition

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | Yes | Artisan option/argument (e.g., `--force`, `name`) |
| `type` | string | Yes | Input type: `boolean`, `text`, or `number` |
| `label` | string | Yes | UI label |
| `default` | mixed | No | Default value |
| `required` | bool | No | Whether the field is mandatory |

## Notification

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `enabled` | bool | `true` | Toggle notifications globally |
| `channels` | array | `['database', 'mail']` | Laravel notification channels |
| `notifiable.model` | string | `App\Models\User` | Eloquent model class |
| `notifiable.identifier` | string | `email` | Column to look up the notifiable |
| `notifiable.value` | string | `''` | Value to match (from env) |

The notification resolves a single notifiable model. It does not broadcast to all users.

## Log Retention

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `log_retention_days` | int/null | `30` | Days to keep logs. `null` keeps logs forever. |

See [Log Pruning](../06-advanced/01-log-pruning.md) for scheduling automatic cleanup.

## Route

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `prefix` | string | `artisan-runner` | URL prefix for the package routes |
| `middleware` | array | `['web', 'auth']` | Middleware applied to all routes |
| `name` | string | `artisan-runner.` | Route name prefix |

## Next Steps

- [Environment Variables](02-environment-variables.md)
- [Basic Usage](../01-getting-started/03-basic-usage.md) - Adding commands
