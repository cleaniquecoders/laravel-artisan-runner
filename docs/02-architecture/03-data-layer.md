# Data Layer

The `CommandLog` model and supporting enums.

## CommandStatus Enum

```php
enum CommandStatus: string
{
    case Pending   = 'pending';
    case Running   = 'running';
    case Completed = 'completed';
    case Failed    = 'failed';
}
```

## DiscoveryMode Enum

```php
enum DiscoveryMode: string
{
    case Manual    = 'manual';
    case Auto      = 'auto';
    case Selection = 'selection';
}
```

## CommandLog Model

### Schema

| Column | Type | Description |
|--------|------|-------------|
| `id` | Auto-increment | Internal primary key |
| `uuid` | UUID | Public identifier, generated on `creating` |
| `command` | String | Artisan command name |
| `parameters` | JSON | Cast to `AsCollection` |
| `status` | String | Cast to `CommandStatus` enum |
| `output` | LongText (nullable) | Command output |
| `exit_code` | Integer (nullable) | Process exit code |
| `ran_by_type` | String (nullable) | Polymorphic morph type |
| `ran_by_id` | Integer (nullable) | Polymorphic morph ID |
| `started_at` | Timestamp (nullable) | When execution began |
| `finished_at` | Timestamp (nullable) | When execution ended |
| `created_at` | Timestamp | Record creation |
| `updated_at` | Timestamp | Last update |

### Key Methods

```php
$log->markAsRunning();                      // Sets status + started_at
$log->markAsCompleted($output, $exitCode);  // Sets status + output + finished_at
$log->markAsFailed($output, $exitCode);     // Sets status + output + finished_at
$log->formattedDuration();                  // Human-readable "Xm Ys" format
```

### Scopes

```php
CommandLog::completed();      // Where status = completed
CommandLog::failed();         // Where status = failed
CommandLog::recent($days);    // Where created_at >= now - $days
```

### Polymorphic ran_by

The `ran_by` relationship uses `morphTo`, allowing any authenticatable model
to be the executor. Nullable for system-triggered runs (e.g., scheduled tasks).

```php
$log->ranBy;  // Returns the User (or any model) who triggered the command
```

## Next Steps

- [Contracts and Actions](02-contracts-and-actions.md) - How commands are dispatched
- [Testing](../05-testing/01-overview.md) - Testing the log lifecycle
