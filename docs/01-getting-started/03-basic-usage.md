# Basic Usage

Learn how to define commands, use discovery modes, and view execution logs.

## Discovery Modes

The package supports three modes for determining which commands are available:

| Mode | Config Value | Description |
|------|-------------|-------------|
| Manual | `manual` | Only commands in `allowed_commands` (default) |
| Auto | `auto` | Auto-discover all Artisan commands minus excluded ones |
| Selection | `selection` | Auto-discover only commands in `included_commands` |

Set the mode in `config/artisan-runner.php`:

```php
'discovery_mode' => 'manual', // 'manual', 'auto', or 'selection'
```

## Defining Allowed Commands (Manual Mode)

Every command you want to run from the UI must be explicitly listed in
`config/artisan-runner.php`. No arbitrary command execution is possible.

```php
'allowed_commands' => [
    'migrate' => [
        'label'       => 'Run Migrations',
        'description' => 'Execute pending database migrations.',
        'group'       => 'Database',
        'parameters'  => [
            ['name' => '--force', 'type' => 'boolean', 'label' => 'Force', 'default' => false],
            ['name' => '--seed',  'type' => 'boolean', 'label' => 'Run seeders', 'default' => false],
        ],
    ],
],
```

## Auto-Discovery Mode

Set `discovery_mode` to `auto` to automatically discover all Artisan commands.
Use exclusion lists to filter out unsafe commands:

```php
'discovery_mode' => 'auto',

'excluded_commands' => [
    'down', 'up', 'serve', 'tinker',
    'db:wipe', 'migrate:fresh', 'migrate:reset',
],

'excluded_namespaces' => [
    'make', 'schedule', 'queue', 'stub',
],
```

Run `php artisan artisan-runner:discover --dry-run` to preview what gets discovered.

## Selection Mode

Only auto-discover specific commands:

```php
'discovery_mode' => 'selection',

'included_commands' => [
    'cache:clear',
    'config:clear',
    'migrate',
    'migrate:status',
],
```

Parameter schemas are auto-generated from the command's `InputDefinition`.

## Parameter Types

The Livewire UI renders form inputs dynamically based on each parameter's `type`:

| Type | Input Rendered | Example |
|------|---------------|---------|
| `boolean` | Checkbox | `--force` flag |
| `text` | Text input | Model name, table name |
| `number` | Number input | Step count, batch size |

Arguments (no `--` prefix) and options (`--` prefix) are displayed in separate
sections in the UI.

## Command Groups

Use the `group` field to organize commands in the UI dropdown.
Commands with the same group are displayed together in an optgroup.

In auto/selection modes, groups are derived from the command namespace
(e.g., `cache:clear` becomes group "Cache").

## Viewing Execution Logs

The `CommandLog` model stores every execution. Click any row in the
Recent Executions table to expand and view the command output.

Query logs programmatically:

```php
use CleaniqueCoders\ArtisanRunner\Models\CommandLog;

// Recent completed runs
$logs = CommandLog::completed()->latest()->take(10)->get();

// Failed runs in the last 7 days
$failed = CommandLog::failed()->recent(7)->get();
```

## Next Steps

- [Configuration Reference](../03-configuration/01-config-file.md) - All config options
- [Livewire UI](../04-livewire-ui/01-command-runner-component.md) - Component details
