# Architecture Overview

High-level design of Laravel Artisan Runner and how its components interact.

## Component Structure

```text
src/
  Actions/
    DiscoverCommandsAction.php     # Auto-discovers Artisan commands
    ResolveCommandsAction.php      # Resolves commands by discovery mode
    RunCommandAction.php           # Implements CommandRunnerContract
  Commands/
    DiscoverCommandsCommand.php    # artisan-runner:discover CLI command
  Contracts/
    CommandRunnerContract.php      # isAllowed(), dispatch(), execute()
  Enums/
    CommandStatus.php              # pending | running | completed | failed
    DiscoveryMode.php              # manual | auto | selection
  Jobs/
    RunArtisanCommandJob.php       # ShouldQueue, tries=1, timeout=300
  Livewire/
    CommandRunner.php              # UI component
  Models/
    CommandLog.php                 # Execution log with UUID PK
  Notifications/
    CommandCompletedNotification.php  # mail + database channels
```

## Request Flow

1. **User selects a command** in the Livewire UI
2. **Livewire component** calls `RunCommandAction::dispatch($command, $params)`
3. **dispatch()** validates the command against the resolved allowlist
4. **CommandLog** record is created with status `pending`
5. **RunArtisanCommandJob** is pushed to the queue
6. **Job executes** and calls `RunCommandAction::execute($log)`
7. **Log is updated** to `completed` or `failed` with output and exit code
8. **Notification is sent** via configured channels
9. **UI auto-refreshes** every 5 seconds via Livewire polling

## Discovery Pipeline

```text
Config (discovery_mode)
  ├── manual  → allowed_commands only
  ├── auto    → DiscoverCommandsAction (all commands - excluded)
  │               └── merged with allowed_commands (manual takes precedence)
  └── selection → DiscoverCommandsAction (included_commands only)
                    └── merged with allowed_commands (manual takes precedence)
```

Results are cached based on `discovery_cache_ttl` (default: 3600 seconds).

## Key Design Principles

- **Allowlist-only**: No command runs unless explicitly configured or discovered
- **Async by default**: All commands run via queued jobs, never blocking HTTP
- **Contract-driven**: `CommandRunnerContract` allows swapping implementations
- **Auditable**: Every execution is logged with who, what, when, and result
- **Discovery modes**: Manual, auto, or selective command discovery

## Service Provider

The `ArtisanRunnerServiceProvider` handles:

- Merging the config file
- Loading migrations
- Registering the Livewire namespace (`artisan-runner::command-runner`)
- Loading routes
- Binding `CommandRunnerContract` to `RunCommandAction` in the container
- Registering `artisan-runner:discover` command
- Publishing logo assets

## Next Steps

- [Contracts and Actions](02-contracts-and-actions.md) - Interface and action details
- [Data Layer](03-data-layer.md) - CommandLog model internals
