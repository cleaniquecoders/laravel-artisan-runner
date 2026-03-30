# Custom Notifications

Customize how notifications are sent when commands complete or fail.

## Default Behavior

The package ships with `CommandCompletedNotification` that supports `mail` and `database` channels.
The notification target is a single user resolved from config:

```php
'notification' => [
    'enabled'    => true,
    'channels'   => ['database', 'mail'],
    'notifiable' => [
        'model'      => \App\Models\User::class,
        'identifier' => 'email',
        'value'      => env('ARTISAN_RUNNER_NOTIFY_EMAIL', ''),
    ],
],
```

## Disabling Notifications

Set `enabled` to `false`:

```php
'notification' => [
    'enabled' => false,
    // ...
],
```

## Changing Channels

Add or remove channels as needed:

```php
'channels' => ['database', 'mail', 'slack'],
```

Ensure your notifiable model supports the channels you configure.

## Changing the Notifiable

Point to any model and identifier column:

```php
'notifiable' => [
    'model'      => \App\Models\Admin::class,
    'identifier' => 'id',
    'value'      => '1',
],
```

## Next Steps

- [Extending](03-extending.md) - Replace the entire runner implementation
- [Configuration](../03-configuration/01-config-file.md)
