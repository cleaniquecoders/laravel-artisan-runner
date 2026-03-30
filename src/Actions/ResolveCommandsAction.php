<?php

namespace CleaniqueCoders\ArtisanRunner\Actions;

use CleaniqueCoders\ArtisanRunner\Enums\DiscoveryMode;
use Illuminate\Support\Facades\Cache;

class ResolveCommandsAction
{
    public function __construct(
        protected DiscoverCommandsAction $discoverer,
    ) {}

    /**
     * @return array<string, array{label: string, description: string, group: string, parameters: array}>
     */
    public function resolve(): array
    {
        $mode = DiscoveryMode::tryFrom(config('artisan-runner.discovery_mode', 'manual'))
            ?? DiscoveryMode::Manual;

        if ($mode === DiscoveryMode::Manual) {
            return config('artisan-runner.allowed_commands', []);
        }

        $ttl = config('artisan-runner.discovery_cache_ttl');

        if ($ttl) {
            return Cache::remember(
                'artisan-runner:discovered-commands',
                $ttl,
                fn () => $this->resolveByMode($mode),
            );
        }

        return $this->resolveByMode($mode);
    }

    public function flush(): void
    {
        Cache::forget('artisan-runner:discovered-commands');
    }

    protected function resolveByMode(DiscoveryMode $mode): array
    {
        $discovered = match ($mode) {
            DiscoveryMode::Auto => $this->discoverer->discover(),
            DiscoveryMode::Selection => $this->discoverer->discoverSelected(
                config('artisan-runner.included_commands', []),
            ),
            default => [],
        };

        return $this->mergeWithManual($discovered);
    }

    protected function mergeWithManual(array $discovered): array
    {
        $manual = config('artisan-runner.allowed_commands', []);

        return array_merge($discovered, $manual);
    }
}
