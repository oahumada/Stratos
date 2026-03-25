<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OperationCompletedNotification extends Notification
{
    use Queueable;

    protected array $operation;

    public function __construct(array $operation)
    {
        $this->operation = $operation;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable): MailMessage
    {
        $op = $this->operation;

        return (new MailMessage)
            ->subject("Operation {$op['operation_name']} completed: {$op['status']}")
            ->greeting('Hello')
            ->line("Operation: {$op['operation_name']} ({$op['operation_type']})")
            ->line("Status: {$op['status']}")
            ->line("Records affected: " . ($op['records_affected'] ?? 0))
            ->line('See the Admin Operations dashboard for details.');
    }

    public function toDatabase($notifiable): array
    {
        $op = $this->operation;

        return [
            'operation_id' => $op['id'] ?? null,
            'operation_name' => $op['operation_name'] ?? null,
            'operation_type' => $op['operation_type'] ?? null,
            'status' => $op['status'] ?? null,
            'records_affected' => $op['records_affected'] ?? 0,
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'operation' => $this->operation,
        ]);
    }
}

