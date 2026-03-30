<?php

namespace CleaniqueCoders\ArtisanRunner\Models;

use CleaniqueCoders\ArtisanRunner\Database\Factories\CommandLogFactory;
use CleaniqueCoders\ArtisanRunner\Enums\CommandStatus;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class CommandLog extends Model
{
    /** @use HasFactory<CommandLogFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'command',
        'parameters',
        'status',
        'output',
        'exit_code',
        'ran_by_type',
        'ran_by_id',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => CommandStatus::class,
            'parameters' => AsCollection::class,
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CommandLog $log) {
            if (empty($log->uuid)) {
                $log->uuid = (string) Str::uuid();
            }
        });
    }

    protected static function newFactory(): CommandLogFactory
    {
        return CommandLogFactory::new();
    }

    public function ranBy(): MorphTo
    {
        return $this->morphTo('ran_by');
    }

    public function markAsRunning(): self
    {
        $this->update([
            'status' => CommandStatus::Running,
            'started_at' => now(),
        ]);

        return $this;
    }

    public function markAsCompleted(string $output, int $exitCode): self
    {
        $this->update([
            'status' => CommandStatus::Completed,
            'output' => $output,
            'exit_code' => $exitCode,
            'finished_at' => now(),
        ]);

        return $this;
    }

    public function markAsFailed(string $output, int $exitCode): self
    {
        $this->update([
            'status' => CommandStatus::Failed,
            'output' => $output,
            'exit_code' => $exitCode,
            'finished_at' => now(),
        ]);

        return $this;
    }

    public function formattedDuration(): ?string
    {
        if (! $this->started_at || ! $this->finished_at) {
            return null;
        }

        $seconds = $this->started_at->diffInSeconds($this->finished_at);

        if ($seconds < 60) {
            return $seconds.'s';
        }

        $minutes = intdiv($seconds, 60);
        $remaining = $seconds % 60;

        return $minutes.'m '.$remaining.'s';
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', CommandStatus::Completed);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', CommandStatus::Failed);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
