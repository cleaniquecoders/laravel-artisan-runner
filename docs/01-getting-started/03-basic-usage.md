# Basic Usage

Learn how to define allowed commands with parameters and organize them into groups.

## Defining Allowed Commands

Every command you want to run from the UI must be explicitly listed in `config/artisan-runner.php`.
No arbitrary command execution is possible.

```php
'allowed_commands' => [
    'migrate' => [
        'label'       => 'Run Migrations',
        'description' => 'Execute pending database migrations.',
        'group'       => 'Database',
        'parameters'  => [
            ['name' => '--force', 'type' => 'boolean', 'label' => 'Force', 'default' => false],
            ['name' => '--step',  'type' => 'number',  'label' => 'Steps', 'default' => 1],
        ],
    ],
    'make:model' => [
        'label'       => 'Create Model',
        'description' => 'Generate a new Eloquent model class.',
        'group'       => 'Generators',
        'parameters'  => [
            ['name' => 'name', 'type' => 'text', 'label' => 'Model Name', 'required' => true],
        ],
    ],
],
```

## Parameter Types

The Livewire UI renders form inputs dynamically based on each parameter's `type`:

| Type | Input Rendered | Example |
|------|---------------|---------|
| `boolean` | Checkbox | `--force` flag |
| `text` | Text input | Model name, table name |
| `number` | Number input | Step count, batch size |

Each parameter supports these fields:

| Field | Required | Description |
|-------|----------|-------------|
| `name` | Yes | The Artisan option/argument name (e.g., `--force`, `name`) |
| `type` | Yes | One of `boolean`, `text`, `number` |
| `label` | Yes | Human-readable label for the UI |
| `default` | No | Default value |
| `required` | No | Whether the parameter is mandatory |

## Command Groups

Use the `group` field to organize commands in the UI. Commands with the same group value are displayed together.

Common group names: `Cache`, `Database`, `Generators`, `Queue`, `Maintenance`.

## Viewing Execution Logs

The `CommandLog` model stores every execution with:

- Command name and parameters
- Status (`pending`, `running`, `completed`, `failed`)
- Output and exit code
- Who ran it (`ran_by` polymorphic relationship)
- Start, finish timestamps, and duration

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
