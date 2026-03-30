# Environment Variables

Environment-specific settings for Laravel Artisan Runner.

## Available Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `ARTISAN_RUNNER_NOTIFY_EMAIL` | `''` | Email address of the user to notify on command completion/failure |

## Usage

Add to your `.env` file:

```env
ARTISAN_RUNNER_NOTIFY_EMAIL=ops@yourdomain.com
```

This value is used by the notification system to resolve which user receives notifications.
It matches against the `identifier` column (default: `email`) on the `notifiable.model`
(default: `App\Models\User`).

## Example

With the default config, setting `ARTISAN_RUNNER_NOTIFY_EMAIL=ops@example.com` will:

1. Query `User::where('email', 'ops@example.com')->first()`
2. Send notifications to that user via the configured channels

> **Note**: If no user matches the configured email, notifications will silently fail.
> Ensure the email exists in your users table.

## Next Steps

- [Config File Reference](01-config-file.md)
- [Advanced: Custom Notifications](../06-advanced/02-custom-notifications.md)
