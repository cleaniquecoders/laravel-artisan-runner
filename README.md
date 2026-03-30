# Laravel Artisan Runner

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cleaniquecoders/laravel-artisan-runner?style=flat-square)](https://packagist.org/packages/cleaniquecoders/laravel-artisan-runner)
[![License](https://img.shields.io/github/license/cleaniquecoders/laravel-artisan-runner?style=flat-square)](LICENSE.md)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/cleaniquecoders/laravel-artisan-runner/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/cleaniquecoders/laravel-artisan-runner/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/cleaniquecoders/laravel-artisan-runner/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/cleaniquecoders/laravel-artisan-runner/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/cleaniquecoders/laravel-artisan-runner?style=flat-square)](https://packagist.org/packages/cleaniquecoders/laravel-artisan-runner)

Run allowlisted Artisan commands from a Livewire UI. Every execution is logged to the database
and notifications are sent on completion or failure.

## Features

- **Allowlist-only execution** - Commands must be explicitly configured, no arbitrary execution
- **Livewire 3 UI** - Select commands, configure parameters, and view results in a web interface
- **Async by default** - Commands run via queued jobs, never blocking HTTP requests
- **Full audit trail** - Every execution logged with who, what, when, and result
- **Notifications** - Mail and database notifications on completion or failure
- **Dynamic parameters** - Boolean, text, and number inputs rendered from config

## Installation

```bash
composer require cleaniquecoders/laravel-artisan-runner
```

Publish and run migrations:

```bash
php artisan artisan-runner:install
php artisan migrate
```

## Quick Start

Add a command to your allowlist in `config/artisan-runner.php`:

```php
'allowed_commands' => [
    'cache:clear' => [
        'label'       => 'Clear Cache',
        'description' => 'Flush the application cache.',
        'group'       => 'Cache',
        'parameters'  => [],
    ],
],
```

Include the Livewire component in any Blade view:

```html
<livewire:artisan-runner::command-runner />
```

Start your queue worker and run commands from the UI.

## Documentation

Full documentation is available in the [docs](docs/README.md) directory:

- [Getting Started](docs/01-getting-started/README.md) - Installation, quick start, basic usage
- [Architecture](docs/02-architecture/README.md) - Design, contracts, data layer
- [Configuration](docs/03-configuration/README.md) - Config reference, environment variables
- [Livewire UI](docs/04-livewire-ui/README.md) - Component usage and customization
- [Testing](docs/05-testing/README.md) - Pest test conventions and examples
- [Advanced](docs/06-advanced/README.md) - Log pruning, notifications, extending

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Nasrul Hazim](https://github.com/nasrulhazim)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
