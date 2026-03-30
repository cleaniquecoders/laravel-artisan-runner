# Architecture

## Overview

How Laravel Artisan Runner is designed: discovery pipeline, contract-based actions,
async job flow, data layer, and service provider responsibilities.

## Table of Contents

### [1. Overview](01-overview.md)

High-level architecture, component diagram, and request flow.

### [2. Contracts and Actions](02-contracts-and-actions.md)

The `CommandRunnerContract` interface, `RunCommandAction`, `ResolveCommandsAction`,
and `DiscoverCommandsAction`.

### [3. Data Layer](03-data-layer.md)

The `CommandLog` model, `CommandStatus` and `DiscoveryMode` enums, UUID primary keys,
and scopes.

## Related Documentation

- [Configuration](../03-configuration/README.md)
- [Testing](../05-testing/README.md)
