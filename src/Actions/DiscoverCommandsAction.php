<?php

namespace CleaniqueCoders\ArtisanRunner\Actions;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class DiscoverCommandsAction
{
    protected const GLOBAL_OPTIONS = [
        'help', 'quiet', 'verbose', 'version', 'ansi', 'no-ansi', 'no-interaction', 'env',
    ];

    /**
     * @return array<string, array{label: string, description: string, group: string, parameters: array}>
     */
    public function discover(): array
    {
        $excludedCommands = config('artisan-runner.excluded_commands', []);
        $excludedNamespaces = config('artisan-runner.excluded_namespaces', []);

        $commands = [];

        foreach (Artisan::all() as $name => $command) {
            if ($command->isHidden()) {
                continue;
            }

            if (in_array($name, $excludedCommands, true)) {
                continue;
            }

            if ($this->isNamespaceExcluded($name, $excludedNamespaces)) {
                continue;
            }

            $commands[$name] = $this->mapCommand($command);
        }

        ksort($commands);

        return $commands;
    }

    /**
     * @return array<string, array{label: string, description: string, group: string, parameters: array}>
     */
    public function discoverSelected(array $includedCommands): array
    {
        $all = $this->discover();

        return array_intersect_key($all, array_flip($includedCommands));
    }

    /**
     * @return array{label: string, description: string, group: string, parameters: array}
     */
    public function mapCommand(Command $command): array
    {
        return [
            'label' => Str::headline(str_replace(':', ' ', $command->getName())),
            'description' => $command->getDescription(),
            'group' => $this->resolveGroup($command->getName()),
            'parameters' => $this->mapDefinition($command->getDefinition()),
        ];
    }

    public function mapDefinition(InputDefinition $definition): array
    {
        $parameters = [];

        foreach ($definition->getArguments() as $argument) {
            $parameters[] = $this->mapArgument($argument);
        }

        foreach ($definition->getOptions() as $option) {
            if (in_array($option->getName(), self::GLOBAL_OPTIONS, true)) {
                continue;
            }

            $parameters[] = $this->mapOption($option);
        }

        return $parameters;
    }

    protected function mapArgument(InputArgument $argument): array
    {
        return [
            'name' => $argument->getName(),
            'type' => 'text',
            'label' => Str::headline($argument->getName()),
            'default' => $argument->getDefault(),
            'required' => $argument->isRequired(),
        ];
    }

    protected function mapOption(InputOption $option): array
    {
        if (! $option->acceptValue()) {
            return [
                'name' => '--'.$option->getName(),
                'type' => 'boolean',
                'label' => Str::headline($option->getName()),
                'default' => false,
                'required' => false,
            ];
        }

        return [
            'name' => '--'.$option->getName(),
            'type' => is_numeric($option->getDefault()) ? 'number' : 'text',
            'label' => Str::headline($option->getName()),
            'default' => $option->getDefault(),
            'required' => $option->isValueRequired(),
        ];
    }

    protected function resolveGroup(string $commandName): string
    {
        if (! str_contains($commandName, ':')) {
            return 'General';
        }

        return Str::headline(Str::before($commandName, ':'));
    }

    protected function isNamespaceExcluded(string $commandName, array $excludedNamespaces): bool
    {
        $namespace = Str::before($commandName, ':');

        return in_array($namespace, $excludedNamespaces, true);
    }
}
