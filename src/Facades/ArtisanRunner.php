<?php

namespace CleaniqueCoders\ArtisanRunner\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CleaniqueCoders\ArtisanRunner\ArtisanRunner
 */
class ArtisanRunner extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \CleaniqueCoders\ArtisanRunner\ArtisanRunner::class;
    }
}
