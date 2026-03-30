# CLAUDE.md — laravel-artisan-runner

> This file is a **living document**. Claude updates it automatically whenever
> a correction, preference, better pattern, or gotcha is discovered during work.
> Last updated: 2026-03-30

---

## Project Overview

**Name:** Laravel Artisan Runner
**Type:** Laravel package
**Purpose:** Run allowlisted or auto-discovered Artisan commands from a Livewire UI
with full audit logging and notifications.
**Repo:** <https://github.com/cleaniquecoders/laravel-artisan-runner>

---

## Stack

| Layer | Technology | Version | Notes |
|---|---|---|---|
| Language | PHP | ^8.3 | |
| Framework | Laravel | 11 / 12 / 13 | |
| Frontend | Livewire | 4 | Registered via `addNamespace()` |
| CSS | Tailwind CSS | 4 | CDN in layout, Tailwind classes in blade |
| Testing | Pest | 4 | BDD-style `it()` blocks |
| Static Analysis | Larastan | 3 | PHPStan level 5 |
| Code Style | Pint | 1.14 | Laravel preset |
| Package Tools | Spatie Package Tools | 1.16 | `PackageServiceProvider` base |
| Test Harness | Orchestra Testbench | 9 / 10 | With `LivewireServiceProvider` |

---

## Architecture

### Key Patterns

- `PackageServiceProvider` via Spatie for config/migration/views/routes registration
- Contracts in `src/Contracts/`, implementations in `src/Actions/`
- Action classes: one class, focused responsibility
- Queued jobs for async command execution
- Livewire 4 namespace registration via `Livewire::addNamespace()`
- Polymorphic `ran_by` for tracking who ran each command

### Directory Structure

```text
src/
├── Actions/
│   ├── DiscoverCommandsAction.php
│   ├── ResolveCommandsAction.php
│   └── RunCommandAction.php
├── Commands/
│   └── DiscoverCommandsCommand.php
├── Contracts/
│   └── CommandRunnerContract.php
├── Enums/
│   ├── CommandStatus.php
│   └── DiscoveryMode.php
├── Jobs/
│   └── RunArtisanCommandJob.php
├── Livewire/
│   └── CommandRunner.php
├── Models/
│   └── CommandLog.php
├── Notifications/
│   └── CommandCompletedNotification.php
└── ArtisanRunnerServiceProvider.php
```

### Database

- **Primary key:** Auto-increment `id` (internal) + UUID `uuid` (public)
- **UUID:** Generated on `creating` event via `Str::uuid()`
- **Status:** Backed enum cast (`CommandStatus`)
- **Parameters:** JSON column cast to `AsCollection`
- **Polymorphic:** `ran_by_type` / `ran_by_id` via `morphTo()`

### Discovery Pipeline

```text
Config (discovery_mode)
  ├── manual  → allowed_commands only
  ├── auto    → DiscoverCommandsAction - excluded → merge with manual
  └── selection → DiscoverCommandsAction (included only) → merge with manual
```

Manual entries always take precedence. Results cached via `discovery_cache_ttl`.

---

## DO / DON'T

- ✅ DO use `ResolveCommandsAction` to get available commands (not config directly)
- ❌ DON'T bypass allowlist — always go through `CommandRunnerContract`
- ✅ DO use index-based keys for Livewire `parameterValues` (not param names)
- ❌ DON'T use `wire:model="parameterValues.--force"` — dashes break Livewire binding
- ✅ DO register Livewire components via `Livewire::addNamespace()` (Livewire 4 way)
- ❌ DON'T use `Livewire::component()` — that's the Livewire 3 API
- ✅ DO pass `recentLogs` via `render()` return — not as computed property
- ❌ DON'T use computed properties for data that must refresh on poll
- ✅ DO use `config:clear` (not `cache:clear`) in tests — cache store may not exist
- ❌ DON'T run blocking commands in HTTP cycle — always dispatch to queue

---

## Preferences

### Code Style

- Return types declared explicitly
- Strict types not enforced (standard Laravel package convention)
- No docblocks unless adding non-obvious context

### Testing

- Pest with `it()` blocks (no `describe()` grouping currently)
- Test file mirrors src path: `tests/RunCommandActionTest.php`
- `beforeEach` for shared config setup
- `Queue::fake()` for dispatch tests
- `CommandLogFactory` with state methods: `pending()`, `running()`,
  `completed()`, `failed()`

### Git

- Descriptive commit messages (not conventional commits prefix)
- Co-authored-by Claude in commits
- Don't push until explicitly asked

### Livewire

- Use `Livewire::addNamespace()` for package component registration
- Views referenced as `artisan-runner::livewire.command-runner`
- Component tag: `<livewire:artisan-runner::command-runner />`

---

## Gotchas

- `cache:clear` fails in Orchestra Testbench because the `database` cache store
  (from testbench .env) doesn't exist. Use `config:clear` in tests instead.
- Livewire 4 computed properties are cached within a single request. Data that
  needs to refresh on `wire:poll` must be passed through `render()` return.
- `wire:model` with `--` prefixed keys (e.g., `parameterValues.--force`) breaks
  in Livewire. Use index-based keys and map back to param names in `run()`.
- `testbench serve` uses in-memory SQLite by default. Create a file-based
  `database.sqlite` and run `migrate:fresh` for persistence across requests.
- Artisan `migrate --step` is a boolean flag (run individually), not a number.
  Don't confuse with `migrate:rollback --step` which is a count.

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
    'middleware'  => ['web', 'auth'],
    'name'       => 'artisan-runner.',
],
```

---

## Environment Variables

```env
ARTISAN_RUNNER_NOTIFY_EMAIL=ops@yourdomain.com
```

---

## Changelog

| Date | Change |
|---|---|
| 2026-03-30 | Initial CLAUDE.md created with full architecture |
| 2026-03-30 | Added discovery modes (manual/auto/selection) |
| 2026-03-30 | Added gotchas for Livewire 4, testbench, and parameter binding |
| 2026-03-30 | Restructured to living document format with DO/DON'T and preferences |
