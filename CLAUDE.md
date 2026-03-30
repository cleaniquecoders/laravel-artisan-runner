# CLAUDE.md — laravel-artisan-runner

## Package Overview

**Package**: `cleaniquecoders/laravel-artisan-runner`
**Purpose**: Run allowlisted or auto-discovered Artisan commands from a Livewire UI.
Logs every execution to DB and sends notifications on completion/failure.
**Stack**: Laravel 11/12/13, Livewire 4, Tailwind CSS 4, PHP 8.3+

---

## Architecture

```text
src/
├── Actions/
│   ├── DiscoverCommandsAction.php   # Auto-discovers Artisan commands
│   ├── ResolveCommandsAction.php    # Resolves commands by discovery mode
│   └── RunCommandAction.php         # Implements CommandRunnerContract
├── Commands/
│   └── DiscoverCommandsCommand.php  # artisan-runner:discover CLI
├── Contracts/
│   └── CommandRunnerContract.php    # isAllowed(), dispatch(), execute()
├── Enums/
│   ├── CommandStatus.php            # pending | running | completed | failed
│   └── DiscoveryMode.php           # manual | auto | selection
├── Jobs/
│   └── RunArtisanCommandJob.php     # ShouldQueue, tries=1, timeout=300
├── Livewire/
│   └── CommandRunner.php            # UI component
├── Models/
│   └── CommandLog.php               # UUID PK (public), auto-increment ID (internal)
├── Notifications/
│   └── CommandCompletedNotification.php  # mail + database channels
config/
└── artisan-runner.php
database/migrations/
└── ..._create_command_logs_table.php
resources/views/livewire/
└── command-runner.blade.php
```

---

## Key Design Decisions

### Discovery Modes

Three modes controlled by `config('artisan-runner.discovery_mode')`:

- **manual** (default): Only `allowed_commands` from config
- **auto**: Auto-discover all Artisan commands minus excluded ones, merge with manual
- **selection**: Auto-discover only `included_commands`, merge with manual

Manual entries always take precedence over discovered ones.

### Allowlist-only (Manual Mode)

Commands must be explicitly listed in `config/artisan-runner.php` under `allowed_commands`.
Each entry defines `label`, `description`, `group`, and `parameters`.

### Command Parameters

Each command declares its parameter schema. Arguments (no `--` prefix) and
options (`--` prefix) are rendered separately in the UI:

```php
'parameters' => [
    ['name' => 'model',    'type' => 'text',    'label' => 'Model', 'required' => true],
    ['name' => '--force',  'type' => 'boolean', 'label' => 'Force', 'default' => false],
    ['name' => '--step',   'type' => 'number',  'label' => 'Steps', 'default' => 1],
]
```

In auto/selection modes, parameter schemas are auto-generated from `InputDefinition`.

### Async by Default

UI calls `RunCommandAction::dispatch()` → creates `CommandLog` (status: pending) →
pushes `RunArtisanCommandJob` to queue → job calls `execute()` → updates log →
fires notification.

### Polymorphic `ran_by`

`command_logs.ran_by_type / ran_by_id` — morphTo, works with any `User` model.
Nullable for system-triggered runs.

---

## CommandLog Model Conventions

- UUID generated on `creating` via `Str::uuid()`
- `status` cast to `CommandStatus` enum
- `parameters` cast to `AsCollection`
- Key methods: `markAsRunning()`, `markAsCompleted($output, $exitCode)`,
  `markAsFailed($output, $exitCode)`, `formattedDuration()`
- Scopes: `completed()`, `failed()`, `recent($days)`

---

## Config Reference

```php
'discovery_mode' => 'manual',
'excluded_commands' => ['down', 'up', 'serve', ...],
'excluded_namespaces' => ['make', 'schedule', 'queue', 'stub'],
'included_commands' => [],
'discovery_cache_ttl' => 3600,

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

'log_retention_days' => 30,

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
```

---

## Service Provider Responsibilities

- Merges config
- Loads migrations
- Registers Livewire namespace (`artisan-runner::command-runner`)
- Loads routes
- Binds `CommandRunnerContract` → `RunCommandAction` in container
- Registers `artisan-runner:discover` command
- Publishes logo assets

---

## Environment Variables

```env
ARTISAN_RUNNER_NOTIFY_EMAIL=ops@yourdomain.com
```
