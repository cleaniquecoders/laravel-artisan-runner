<?php

namespace CleaniqueCoders\ArtisanRunner\Commands;

use Illuminate\Console\Command;

class ArtisanRunnerCommand extends Command
{
    public $signature = 'laravel-artisan-runner';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
