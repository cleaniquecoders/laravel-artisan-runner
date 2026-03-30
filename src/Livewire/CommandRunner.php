<?php

namespace CleaniqueCoders\ArtisanRunner\Livewire;

use CleaniqueCoders\ArtisanRunner\Actions\ResolveCommandsAction;
use CleaniqueCoders\ArtisanRunner\Contracts\CommandRunnerContract;
use CleaniqueCoders\ArtisanRunner\Models\CommandLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CommandRunner extends Component
{
    public string $selectedCommand = '';

    public array $parameterValues = [];

    public ?string $lastLogUuid = null;

    public ?int $viewingLogId = null;

    public function getCommandsProperty(): array
    {
        return app(ResolveCommandsAction::class)->resolve();
    }

    public function getGroupedCommandsProperty(): array
    {
        $grouped = [];

        foreach ($this->commands as $command => $config) {
            $group = $config['group'] ?? 'Other';
            $grouped[$group][$command] = $config;
        }

        ksort($grouped);

        return $grouped;
    }

    public function getSelectedCommandConfigProperty(): ?array
    {
        return $this->commands[$this->selectedCommand] ?? null;
    }

    public function getParametersProperty(): array
    {
        return $this->selectedCommandConfig['parameters'] ?? [];
    }

    public function getRecentLogs()
    {
        return CommandLog::latest()->take(10)->get();
    }

    public function updatedSelectedCommand(): void
    {
        $this->parameterValues = [];

        foreach ($this->parameters as $index => $param) {
            $default = $param['default'] ?? ($param['type'] === 'boolean' ? false : '');
            $this->parameterValues[$index] = $default;
        }
    }

    public function run(): void
    {
        if (! $this->selectedCommand) {
            return;
        }

        // Map index-based values back to param name keys
        $mapped = [];
        foreach ($this->parameters as $index => $param) {
            if (isset($this->parameterValues[$index])) {
                $mapped[$param['name']] = $this->parameterValues[$index];
            }
        }

        $runner = app(CommandRunnerContract::class);

        $log = $runner->dispatch(
            $this->selectedCommand,
            $mapped,
            Auth::user()
        );

        $this->lastLogUuid = $log->uuid;
        $this->selectedCommand = '';
        $this->parameterValues = [];
    }

    public function toggleOutput(int $logId): void
    {
        $this->viewingLogId = $this->viewingLogId === $logId ? null : $logId;
    }

    public function render()
    {
        return view('artisan-runner::livewire.command-runner', [
            'recentLogs' => $this->getRecentLogs(),
        ]);
    }
}
