# Command Runner Component

The Livewire component that provides the web UI for running Artisan commands.

## Usage

Include the component in any Blade view:

```html
<livewire:artisan-runner::command-runner />
```

Ensure the page uses middleware that includes `web` and `auth`
(or whatever you configure in `config/artisan-runner.php`).

## How It Works

1. Resolves available commands via `ResolveCommandsAction` (respects discovery mode)
2. Groups commands by their `group` field in the dropdown
3. When a command is selected, renders parameter inputs from the schema
4. On submission, calls `RunCommandAction::dispatch()` with the command and values
5. Displays the `CommandLog` status in real-time via 5-second polling

## Arguments vs Options

The UI separates parameters into two sections:

- **Arguments** (no `--` prefix) — displayed as text inputs in an "Arguments" section
- **Options** (`--` prefix) — displayed in an "Options" section as checkboxes
  (boolean) or text/number inputs (value options)

## Dynamic Parameter Rendering

| Parameter Type | Rendered Input | Notes |
|----------------|---------------|-------|
| `boolean` | Checkbox | For `--flag` style options |
| `text` | Text input | For arguments and string options |
| `number` | Number input | For numeric options |

Required parameters show a "required" badge.
Each parameter displays its name in a code badge for reference.

## Output Viewer

Click any row in the Recent Executions table to expand the command output panel:

- Terminal-style dark background with monospace font
- Shows the command UUID
- Scrollable with max height for long output
- Contextual message for pending/running commands

## Auto-Refresh

The component polls every 5 seconds (`wire:poll.5s`), so status changes
from `pending` to `completed`/`failed` appear automatically without
manual refresh.

## Customizing the View

Publish the views to customize the UI:

```bash
php artisan vendor:publish --tag="artisan-runner-views"
```

Published to `resources/views/vendor/artisan-runner/livewire/command-runner.blade.php`.

## Next Steps

- [Configuration](../03-configuration/01-config-file.md) - Route and middleware settings
- [Basic Usage](../01-getting-started/03-basic-usage.md) - Adding commands
