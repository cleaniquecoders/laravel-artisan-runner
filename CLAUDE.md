# CLAUDE.md — laravel-artisan-runner

## Package Overview

**Package**: `cleaniquecoders/laravel-artisan-runner`
**Purpose**: Run allowlisted Artisan commands from a Livewire UI. Logs every execution to DB and sends notifications on completion/failure.
**Stack**: Laravel 11/12/13, Livewire 4, Tailwind CSS 4, PHP 8.2+

---

## Architecture

```
src/
├── Contracts/
│   └── CommandRunnerContract.php   # isAllowed(), dispatch(), execute()
├── Enums/
│   └── CommandStatus.php           # pending | running | completed | failed
├── Models/
│   └── CommandLog.php              # UUID PK (public), auto-increment ID (internal)
├── Actions/
│   └── RunCommandAction.php        # Implements CommandRunnerContract
├── Jobs/
│   └── RunArtisanCommandJob.php    # ShouldQueue, tries=1, timeout=300
├── Notifications/
│   └── CommandCompletedNotification.php  # mail + database channels
├── Livewire/
│   └── CommandRunner.php           # UI component
config/
└── artisan-runner.php
database/migrations/
└── ..._create_command_logs_table.php
resources/views/livewire/
└── command-runner.blade.php
```

---

## Key Design Decisions

### Allowlist-only
Commands must be explicitly listed in `config/artisan-runner.php` under `allowed_commands`. No arbitrary command execution. Each entry defines `label`, `description`, `group`, and `parameters`.

### Command parameters
Each allowed command declares its own parameter schema:
```php
'parameters' => [
    ['name' => '--force',  'type' => 'boolean', 'label' => 'Force', 'default' => false],
    ['name' => '--step',   'type' => 'number',  'label' => 'Steps', 'default' => 1],
    ['name' => 'model',    'type' => 'text',    'label' => 'Model', 'required' => true],
]
```
The Livewire UI renders inputs dynamically based on this schema.

### Async by default
UI calls `RunCommandAction::dispatch()` → creates `CommandLog` (status: pending) → pushes `RunArtisanCommandJob` to queue → job calls `execute()` → updates log → fires notification.

Never run blocking commands in the HTTP request cycle.

### Polymorphic `ran_by`
`command_logs.ran_by_type / ran_by_id` — morphTo, works with any `User` model. Nullable for system-triggered runs.

### Notification target
Configured via `config/artisan-runner.notification.notifiable`. Resolves a single notifiable model by identifier (e.g. email). Not broadcast to all users.

---

## CommandLog Model Conventions

- UUID generated on `creating` via `Str::uuid()`
- `status` cast to `CommandStatus` enum
- `parameters` cast to `AsCollection`
- Key methods: `markAsRunning()`, `markAsCompleted($output, $exitCode)`, `markAsFailed($output, $exitCode)`, `formattedDuration()`
- Scopes: `completed()`, `failed()`, `recent($days)`

---

## Adding a New Allowed Command

Only in `config/artisan-runner.php`:

```php
'your:command' => [
    'label'       => 'Human Label',
    'description' => 'What it does.',
    'group'       => 'GroupName',
    'parameters'  => [
        ['name' => '--option', 'type' => 'boolean', 'label' => 'Enable X', 'default' => false],
    ],
],
```

No code changes needed. The Livewire component reads config dynamically.

---

## Config Reference

```php
// config/artisan-runner.php

'allowed_commands' => [...],

'notification' => [
    'enabled'    => true,
    'channels'   => ['database', 'mail'],
    'notifiable' => [
        'model'      => \App\Models\User::class,
        'identifier' => 'email',
        'value'      => env('ARTISAN_RUNNER_NOTIFY_EMAIL', ''),
    ],
],

'log_retention_days' => 30,  // null = keep forever

'route' => [
    'prefix'     => 'artisan-runner',
    'middleware' => ['web', 'auth'],
    'name'       => 'artisan-runner.',
],
```

---

## Testing Conventions

Use Pest. Test file mirrors src path under `tests/`.

```php
// Verify allowlist enforcement
it('rejects commands not in allowlist', function () {
    $action = new RunCommandAction;
    expect(fn () => $action->dispatch('down'))->toThrow(\InvalidArgumentException::class);
});

// Verify log lifecycle
it('creates a pending log on dispatch', function () {
    Queue::fake();
    config(['artisan-runner.allowed_commands' => ['cache:clear' => ['label' => 'Clear Cache', 'parameters' => []]]]);

    $log = app(RunCommandAction::class)->dispatch('cache:clear');

    expect($log->status)->toBe(CommandStatus::Pending);
    Queue::assertPushed(RunArtisanCommandJob::class);
});

// Verify execute updates log
it('marks log as completed on success', function () {
    $log    = CommandLog::factory()->pending()->create(['command' => 'cache:clear']);
    $result = app(RunCommandAction::class)->execute($log);

    expect($result->status)->toBe(CommandStatus::Completed)
        ->and($result->finished_at)->not->toBeNull();
});
```

---

## Log Pruning

Add to your app's scheduled commands:

```php
// routes/console.php or Kernel.php
Schedule::call(function () {
    $days = config('artisan-runner.log_retention_days');
    if ($days) {
        \CleaniqueCoders\ArtisanRunner\Models\CommandLog::where('created_at', '<', now()->subDays($days))->delete();
    }
})->daily()->name('artisan-runner:prune-logs');
```

---

## Environment Variables

```env
ARTISAN_RUNNER_NOTIFY_EMAIL=ops@yourdomain.com
```

---

## Service Provider Responsibilities

- Merges config
- Loads migrations
- Loads Livewire component (`artisan-runner::command-runner`)
- Loads routes
- Binds `CommandRunnerContract` → `RunCommandAction` in container
- Registers `artisan-runner:install` publish command
