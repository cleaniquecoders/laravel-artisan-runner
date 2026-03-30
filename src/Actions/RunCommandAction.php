<?php

namespace CleaniqueCoders\ArtisanRunner\Actions;

use CleaniqueCoders\ArtisanRunner\Contracts\CommandRunnerContract;
use CleaniqueCoders\ArtisanRunner\Enums\CommandStatus;
use CleaniqueCoders\ArtisanRunner\Jobs\RunArtisanCommandJob;
use CleaniqueCoders\ArtisanRunner\Models\CommandLog;
use CleaniqueCoders\ArtisanRunner\Notifications\CommandCompletedNotification;
use Illuminate\Support\Facades\Artisan;
use InvalidArgumentException;

class RunCommandAction implements CommandRunnerContract
{
    public function isAllowed(string $command): bool
    {
        return array_key_exists($command, app(ResolveCommandsAction::class)->resolve());
    }

    public function dispatch(string $command, array $parameters = [], $ranBy = null): CommandLog
    {
        if (! $this->isAllowed($command)) {
            throw new InvalidArgumentException("Command [{$command}] is not in the allowed commands list.");
        }

        $log = CommandLog::create([
            'command' => $command,
            'parameters' => $parameters,
            'status' => CommandStatus::Pending,
            'ran_by_type' => $ranBy ? get_class($ranBy) : null,
            'ran_by_id' => $ranBy?->getKey(),
        ]);

        RunArtisanCommandJob::dispatch($log);

        return $log;
    }

    public function execute(CommandLog $log): CommandLog
    {
        $log->markAsRunning();

        try {
            $params = $this->buildArtisanParameters($log);

            $exitCode = Artisan::call($log->command, $params);
            $output = Artisan::output();

            if ($exitCode === 0) {
                $log->markAsCompleted($output, $exitCode);
            } else {
                $log->markAsFailed($output, $exitCode);
            }
        } catch (\Throwable $e) {
            $log->markAsFailed($e->getMessage(), 1);
        }

        $this->sendNotification($log);

        return $log;
    }

    protected function buildArtisanParameters(CommandLog $log): array
    {
        $params = [];
        $commands = app(ResolveCommandsAction::class)->resolve();
        $schema = $commands[$log->command]['parameters'] ?? [];
        $values = $log->parameters ?? collect();

        foreach ($schema as $paramDef) {
            $name = $paramDef['name'];
            $type = $paramDef['type'] ?? 'text';

            if (! $values->has($name)) {
                continue;
            }

            $value = $values->get($name);

            if ($type === 'boolean') {
                if ($value) {
                    $params[$name] = true;
                }
            } else {
                $params[$name] = $value;
            }
        }

        return $params;
    }

    protected function sendNotification(CommandLog $log): void
    {
        $config = config('artisan-runner.notification');

        if (! ($config['enabled'] ?? false)) {
            return;
        }

        $modelClass = $config['notifiable']['model'] ?? null;
        $identifier = $config['notifiable']['identifier'] ?? 'email';
        $value = $config['notifiable']['value'] ?? '';

        if (! $modelClass || ! $value) {
            return;
        }

        $notifiable = $modelClass::where($identifier, $value)->first();

        if ($notifiable) {
            $notifiable->notify(new CommandCompletedNotification($log));
        }
    }
}
