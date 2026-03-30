# Command Runner Component

The Livewire component that provides the web UI for running Artisan commands.

## Usage

Include the component in any Blade view:

```html
<livewire:artisan-runner::command-runner />
```

Ensure the page uses middleware that includes `web` and `auth` (or whatever you configure in `config/artisan-runner.php`).

## How It Works

1. Reads `config('artisan-runner.allowed_commands')` to populate the command dropdown
2. Groups commands by their `group` field
3. When a command is selected, renders parameter inputs dynamically based on the `parameters` schema
4. On submission, calls `RunCommandAction::dispatch()` with the command and parameter values
5. Displays the `CommandLog` status in real-time

## Dynamic Parameter Rendering

The component renders different input types based on the parameter `type`:

| Parameter Type | Rendered Input | Notes |
|----------------|---------------|-------|
| `boolean` | Checkbox | Checked = flag present |
| `text` | Text input | Free-form string |
| `number` | Number input | Integer value |

Required parameters show validation errors if left empty.

## Customizing the View

Publish the views to customize the UI:

```bash
php artisan vendor:publish --tag="artisan-runner-views"
```

Published to `resources/views/vendor/artisan-runner/livewire/command-runner.blade.php`.

## Next Steps

- [Configuration](../03-configuration/01-config-file.md) - Route and middleware settings
- [Basic Usage](../01-getting-started/03-basic-usage.md) - Adding commands
