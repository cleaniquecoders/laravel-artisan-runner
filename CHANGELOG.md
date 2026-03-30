# Changelog

All notable changes to `laravel-artisan-runner` will be documented in this file.

## 1.2.0 - 2026-03-30

### Added

- `php artisan artisan-runner:install` command — publishes config, migrations, assets and prompts to run migrations in one step
- `artisan-runner:discover` now auto-publishes the config file when missing instead of erroring out

## 1.1.0 - 2026-03-30

### Added

- Livewire 3 support alongside Livewire 4 (`^3.0||^4.0`)
- Version-aware component registration: `addNamespace()` on LW4, `component()` fallback on LW3

### Infrastructure

- Added Laravel 11 + Livewire 3 + Testbench 9 to CI matrix
- Excluded PHP 8.5 from Laravel 11 matrix (unsupported)

## 1.0.0 - 2026-03-30

### 1.0.0 — Initial Release

#### Added

- Livewire 4 UI to browse, configure, and run Artisan commands from the browser
- Three discovery modes: **manual** (allowlist), **auto** (all commands minus exclusions), **selection** (include-only list)
- Async command execution via queued jobs with configurable timeout
- `CommandLog` model with UUID primary key, polymorphic `ran_by`, and status tracking (pending → running → completed/failed)
- Parameter schema support: text, boolean, number types with arguments and options rendered separately
- Expandable command output viewer in execution logs table
- Mail and database notifications on command completion/failure
- `artisan-runner:discover` CLI command for cache-warming discovered commands
- Configurable route prefix, middleware, log retention, and notification channels
- Brand logo assets with publishable service provider

#### Infrastructure

- Pest test suite
- GitHub Actions CI for PHP 8.3 / 8.4 / 8.5 on Linux
- PHPStan, Pint code style, and Dependabot workflows
