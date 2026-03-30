# Config File Reference

Complete reference for `config/artisan-runner.php`.

## Full Config Structure

```php
return [
    'discovery_mode' => 'manual',

    'excluded_commands' => [
        'down', 'up', 'serve', 'tinker', 'env', 'inspire',
        'db:wipe', 'migrate:fresh', 'migrate:reset',
        'vendor:publish', 'package:discover',
        'artisan-runner:discover',
    ],

    'excluded_namespaces' => ['make', 'schedule', 'queue', 'stub'],

    'included_commands' => [],

    'discovery_cache_ttl' => 3600,

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

## Discovery Mode

| Value | Behavior |
|-------|----------|
| `manual` | Only `allowed_commands` are available (default, most secure) |
| `auto` | All Artisan commands discovered minus excluded, merged with `allowed_commands` |
| `selection` | Only `included_commands` discovered, merged with `allowed_commands` |

## Excluded Commands

Commands skipped during auto-discovery. Only applies to `auto` and `selection` modes.
Dangerous commands like `down`, `db:wipe`, `migrate:fresh` are excluded by default.

## Excluded Namespaces

Entire command namespaces skipped during discovery. Default excludes `make`,
`schedule`, `queue`, and `stub`.

## Included Commands

When `discovery_mode` is `selection`, only these commands will be discovered.
Their parameter schemas are auto-generated from the command's `InputDefinition`.

## Discovery Cache TTL

How long (in seconds) to cache discovered commands. Set to `null` to disable
caching. Default: `3600` (1 hour). Only applies to `auto` and `selection` modes.

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

> **Note**: In `auto` and `selection` modes, manually defined `allowed_commands`
> take precedence over auto-discovered ones. Use this to override labels or
> parameter schemas for specific commands.

## Notification

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `enabled` | bool | `true` | Toggle notifications globally |
| `channels` | array | `['database', 'mail']` | Laravel notification channels |
| `notifiable.model` | string | `App\Models\User` | Eloquent model class |
| `notifiable.identifier` | string | `email` | Column to look up the notifiable |
| `notifiable.value` | string | `''` | Value to match (from env) |

## Log Retention

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `log_retention_days` | int/null | `30` | Days to keep logs. `null` = forever. |

See [Log Pruning](../06-advanced/01-log-pruning.md) for scheduling automatic cleanup.

## Route

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `prefix` | string | `artisan-runner` | URL prefix for package routes |
| `middleware` | array | `['web', 'auth']` | Middleware applied to all routes |
| `name` | string | `artisan-runner.` | Route name prefix |

## Next Steps

- [Environment Variables](02-environment-variables.md)
- [Basic Usage](../01-getting-started/03-basic-usage.md) - Discovery modes in practice
