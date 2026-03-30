<?php

namespace CleaniqueCoders\ArtisanRunner\Jobs;

use CleaniqueCoders\ArtisanRunner\Contracts\CommandRunnerContract;
use CleaniqueCoders\ArtisanRunner\Models\CommandLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunArtisanCommandJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 300;

    public function __construct(
        public CommandLog $log
    ) {}

    public function handle(CommandRunnerContract $runner): void
    {
        $runner->execute($this->log);
    }
}
