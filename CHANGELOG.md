# Changelog

All notable changes to `laravel-artisan-runner` will be documented in this file.

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
