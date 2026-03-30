# Architecture Overview

High-level design of Laravel Artisan Runner and how its components interact.

## Component Structure

```text
src/
  Contracts/CommandRunnerContract.php
  Enums/CommandStatus.php
  Models/CommandLog.php
  Actions/RunCommandAction.php
  Jobs/RunArtisanCommandJob.php
  Notifications/CommandCompletedNotification.php
  Livewire/CommandRunner.php
config/artisan-runner.php
database/migrations/..._create_command_logs_table.php
resources/views/livewire/command-runner.blade.php
```

## Request Flow

1. **User selects a command** in the Livewire UI
2. **Livewire component** calls `RunCommandAction::dispatch($command, $params)`
3. **dispatch()** validates the command against the allowlist
4. **CommandLog** record is created with status `pending`
5. **RunArtisanCommandJob** is pushed to the queue
6. **Job executes** and calls `RunCommandAction::execute($log)`
7. **Log is updated** to `completed` or `failed` with output and exit code
8. **Notification is sent** via configured channels

## Key Design Principles

- **Allowlist-only**: No command runs unless explicitly configured
- **Async by default**: All commands run via queued jobs, never blocking HTTP
- **Contract-driven**: `CommandRunnerContract` allows swapping implementations
- **Auditable**: Every execution is logged with who, what, when, and result

## Service Provider

The `ArtisanRunnerServiceProvider` handles:

- Merging the config file
- Loading migrations
- Registering the Livewire component (`artisan-runner::command-runner`)
- Loading routes
- Binding `CommandRunnerContract` to `RunCommandAction` in the container
- Registering the `artisan-runner:install` publish command

## Next Steps

- [Contracts and Actions](02-contracts-and-actions.md) - Interface details
- [Data Layer](03-data-layer.md) - CommandLog model internals
