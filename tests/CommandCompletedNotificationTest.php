<?php

use CleaniqueCoders\ArtisanRunner\Enums\CommandStatus;
use CleaniqueCoders\ArtisanRunner\Models\CommandLog;
use CleaniqueCoders\ArtisanRunner\Notifications\CommandCompletedNotification;
use Illuminate\Notifications\Messages\MailMessage;

it('returns configured channels', function () {
    config(['artisan-runner.notification.channels' => ['database', 'mail']]);

    $log = CommandLog::factory()->completed()->create();
    $notification = new CommandCompletedNotification($log);

    expect($notification->via(new \stdClass))->toBe(['database', 'mail']);
});

it('generates mail message for completed command', function () {
    $log = CommandLog::factory()->completed()->create(['command' => 'cache:clear']);
    $notification = new CommandCompletedNotification($log);

    $mail = $notification->toMail(new \stdClass);

    expect($mail)->toBeInstanceOf(MailMessage::class)
        ->and($mail->subject)->toContain('Completed')
        ->and($mail->subject)->toContain('cache:clear');
});

it('generates mail message for failed command', function () {
    $log = CommandLog::factory()->failed()->create(['command' => 'migrate']);
    $notification = new CommandCompletedNotification($log);

    $mail = $notification->toMail(new \stdClass);

    expect($mail->subject)->toContain('Failed')
        ->and($mail->subject)->toContain('migrate');
});

it('generates array representation', function () {
    $log = CommandLog::factory()->completed()->create(['command' => 'cache:clear']);
    $notification = new CommandCompletedNotification($log);

    $array = $notification->toArray(new \stdClass);

    expect($array)->toHaveKeys(['command_log_id', 'command_log_uuid', 'command', 'status', 'exit_code', 'duration'])
        ->and($array['command'])->toBe('cache:clear')
        ->and($array['status'])->toBe('completed');
});
