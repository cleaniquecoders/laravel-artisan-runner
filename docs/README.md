# Documentation

## Overview

Laravel Artisan Runner provides an allowlist-based Livewire UI for executing Artisan commands safely.
Every execution is logged to the database and notifications are sent on completion or failure.

This documentation covers architecture, configuration, the Livewire component, testing,
and advanced topics like log pruning and extending the package.

## Documentation Structure

### [01. Getting Started](01-getting-started/README.md)

Installation, publishing assets, and running your first command from the UI.

### [02. Architecture](02-architecture/README.md)

System design, async job flow, contracts, and data layer.

### [03. Configuration](03-configuration/README.md)

Allowed commands, notification settings, route customization, and environment variables.

### [04. Livewire UI](04-livewire-ui/README.md)

The CommandRunner component, dynamic parameter rendering, and UI customization.

### [05. Testing](05-testing/README.md)

Pest test conventions, testing allowlist enforcement, and log lifecycle.

### [06. Advanced](06-advanced/README.md)

Log pruning, custom notifications, and extending the package.

## Quick Start

New to the package? Start with [Getting Started](01-getting-started/01-installation.md).

## Finding Information

- **How it works**: Check [Architecture](02-architecture/README.md)
- **Setup & config**: Check [Configuration](03-configuration/README.md)
- **UI component**: Check [Livewire UI](04-livewire-ui/README.md)
- **Writing tests**: Check [Testing](05-testing/README.md)
