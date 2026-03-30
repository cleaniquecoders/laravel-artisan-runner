# Installation

This guide walks through installing Laravel Artisan Runner and publishing its assets.

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13
- Livewire 4
- Tailwind CSS 4
- A queue worker (Redis, database, SQS, etc.)

## Install via Composer

```bash
composer require cleaniquecoders/laravel-artisan-runner
```

## Publish Assets

Publish the configuration file, migrations, and views:

```bash
php artisan artisan-runner:install
```

Or publish individually:

```bash
# Config only
php artisan vendor:publish --tag="artisan-runner-config"

# Migrations only
php artisan vendor:publish --tag="artisan-runner-migrations"

# Views only
php artisan vendor:publish --tag="artisan-runner-views"
```

## Run Migrations

```bash
php artisan migrate
```

This creates the `command_logs` table used to track every command execution.

## Queue Setup

Artisan Runner dispatches commands to the queue by default. Ensure you have a queue worker running:

```bash
php artisan queue:work
```

> **Note**: Without a running queue worker, dispatched commands will remain in `pending` status indefinitely.

## Next Steps

- [Quick Start](02-quick-start.md) - Run your first command
- [Configuration](../03-configuration/01-config-file.md) - Customize allowed commands and notifications
