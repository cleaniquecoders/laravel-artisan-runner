<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed Commands
    |--------------------------------------------------------------------------
    |
    | Only commands listed here can be executed from the UI. Each entry maps
    | an Artisan command signature to its label, description, group, and
    | parameter schema.
    |
    */

    'allowed_commands' => [
        'cache:clear' => [
            'label' => 'Clear Cache',
            'description' => 'Flush the application cache.',
            'group' => 'Cache',
            'parameters' => [],
        ],
        'config:clear' => [
            'label' => 'Clear Config',
            'description' => 'Remove the configuration cache file.',
            'group' => 'Cache',
            'parameters' => [],
        ],
        'route:clear' => [
            'label' => 'Clear Routes',
            'description' => 'Remove the route cache file.',
            'group' => 'Cache',
            'parameters' => [],
        ],
        'view:clear' => [
            'label' => 'Clear Views',
            'description' => 'Clear all compiled view files.',
            'group' => 'Cache',
            'parameters' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    |
    | Configure who gets notified when a command completes or fails.
    | Set enabled to false to disable notifications entirely.
    |
    */

    'notification' => [
        'enabled' => true,
        'channels' => ['database', 'mail'],
        'notifiable' => [
            'model' => 'App\\Models\\User',
            'identifier' => 'email',
            'value' => env('ARTISAN_RUNNER_NOTIFY_EMAIL', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Retention
    |--------------------------------------------------------------------------
    |
    | Number of days to keep command logs. Set to null to keep forever.
    |
    */

    'log_retention_days' => 30,

    /*
    |--------------------------------------------------------------------------
    | Route
    |--------------------------------------------------------------------------
    |
    | Customize the route prefix, middleware, and name for the package routes.
    |
    */

    'route' => [
        'prefix' => 'artisan-runner',
        'middleware' => ['web', 'auth'],
        'name' => 'artisan-runner.',
    ],
];
