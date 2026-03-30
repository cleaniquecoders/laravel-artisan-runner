# Quick Start

Get a command running from the Livewire UI in under five minutes.

## Step 1: Add an Allowed Command

Open `config/artisan-runner.php` and add a command to the allowlist:

```php
'allowed_commands' => [
    'cache:clear' => [
        'label'       => 'Clear Cache',
        'description' => 'Flush the application cache.',
        'group'       => 'Cache',
        'parameters'  => [],
    ],
],
```

## Step 2: Include the Livewire Component

Add the component to any Blade view behind authentication:

```html
<livewire:artisan-runner::command-runner />
```

## Step 3: Start the Queue Worker

```bash
php artisan queue:work
```

## Step 4: Run the Command

Visit the page containing the component, select **Clear Cache**, and click **Run**.
The UI will show real-time status updates as the command moves through
`pending`, `running`, and `completed` states.

## What Happens Behind the Scenes

1. The Livewire component calls `RunCommandAction::dispatch('cache:clear')`
2. A `CommandLog` record is created with status `pending`
3. `RunArtisanCommandJob` is pushed to the queue
4. The job executes the command and updates the log to `completed` or `failed`
5. A notification is sent to the configured notifiable

## Next Steps

- [Basic Usage](03-basic-usage.md) - Learn about parameters and command groups
- [Configuration](../03-configuration/01-config-file.md) - Full config reference
