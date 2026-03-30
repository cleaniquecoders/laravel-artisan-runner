# Architecture

## Overview

How Laravel Artisan Runner is designed: the contract-based approach, async job flow, data layer, and service provider responsibilities.

## Table of Contents

### [1. Overview](01-overview.md)

High-level architecture, component diagram, and request flow.

### [2. Contracts and Actions](02-contracts-and-actions.md)

The `CommandRunnerContract` interface and `RunCommandAction` implementation.

### [3. Data Layer](03-data-layer.md)

The `CommandLog` model, UUID primary keys, enum casting, and scopes.

## Related Documentation

- [Configuration](../03-configuration/README.md)
- [Testing](../05-testing/README.md)
