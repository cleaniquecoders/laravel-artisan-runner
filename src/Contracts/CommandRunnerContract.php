<?php

namespace CleaniqueCoders\ArtisanRunner\Contracts;

use CleaniqueCoders\ArtisanRunner\Models\CommandLog;

interface CommandRunnerContract
{
    public function isAllowed(string $command): bool;

    public function dispatch(string $command, array $parameters = [], $ranBy = null): CommandLog;

    public function execute(CommandLog $log): CommandLog;
}
