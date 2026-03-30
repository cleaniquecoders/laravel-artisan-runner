<?php

namespace CleaniqueCoders\ArtisanRunner\Notifications;

use CleaniqueCoders\ArtisanRunner\Enums\CommandStatus;
use CleaniqueCoders\ArtisanRunner\Models\CommandLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommandCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public CommandLog $log
    ) {}

    public function via(object $notifiable): array
    {
        return config('artisan-runner.notification.channels', ['database', 'mail']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->log->status === CommandStatus::Completed ? 'Completed' : 'Failed';

        return (new MailMessage)
            ->subject("Artisan Command {$status}: {$this->log->command}")
            ->line("The command `{$this->log->command}` has {$status}.")
            ->line("Exit code: {$this->log->exit_code}")
            ->line('Duration: '.($this->log->formattedDuration() ?? 'N/A'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'command_log_id' => $this->log->id,
            'command_log_uuid' => $this->log->uuid,
            'command' => $this->log->command,
            'status' => $this->log->status->value,
            'exit_code' => $this->log->exit_code,
            'duration' => $this->log->formattedDuration(),
        ];
    }
}
