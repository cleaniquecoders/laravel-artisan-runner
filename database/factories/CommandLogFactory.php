<?php

namespace CleaniqueCoders\ArtisanRunner\Database\Factories;

use CleaniqueCoders\ArtisanRunner\Enums\CommandStatus;
use CleaniqueCoders\ArtisanRunner\Models\CommandLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommandLogFactory extends Factory
{
    protected $model = CommandLog::class;

    public function definition(): array
    {
        return [
            'command' => $this->faker->randomElement(['cache:clear', 'config:clear', 'route:clear', 'view:clear']),
            'parameters' => collect([]),
            'status' => CommandStatus::Pending,
        ];
    }

    public function pending(): self
    {
        return $this->state(['status' => CommandStatus::Pending]);
    }

    public function running(): self
    {
        return $this->state([
            'status' => CommandStatus::Running,
            'started_at' => now(),
        ]);
    }

    public function completed(): self
    {
        return $this->state([
            'status' => CommandStatus::Completed,
            'output' => 'Command completed successfully.',
            'exit_code' => 0,
            'started_at' => now()->subSeconds(5),
            'finished_at' => now(),
        ]);
    }

    public function failed(): self
    {
        return $this->state([
            'status' => CommandStatus::Failed,
            'output' => 'Command failed with error.',
            'exit_code' => 1,
            'started_at' => now()->subSeconds(3),
            'finished_at' => now(),
        ]);
    }
}
