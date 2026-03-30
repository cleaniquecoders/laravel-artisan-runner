<?php

namespace CleaniqueCoders\ArtisanRunner\Enums;

enum CommandStatus: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';
}
