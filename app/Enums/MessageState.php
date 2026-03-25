<?php

namespace App\Enums;

enum MessageState: string
{
    case SENT = 'sent';           // Message queued for delivery
    case DELIVERED = 'delivered'; // Message received by recipient's device
    case READ = 'read';           // Message marked read by recipient
    case FAILED = 'failed';       // Delivery failed (transient)

    public function label(): string
    {
        return match ($this) {
            self::SENT => 'Enviado',
            self::DELIVERED => 'Entregado',
            self::READ => 'Leído',
            self::FAILED => 'Fallido',
        };
    }

    public function isTerminal(): bool
    {
        return $this === self::READ || $this === self::FAILED;
    }

    public function canTransition(self $to): bool
    {
        return match ([$this, $to]) {
            [self::SENT, self::DELIVERED] => true,
            [self::SENT, self::FAILED] => true,
            [self::DELIVERED, self::READ] => true,
            [self::DELIVERED, self::FAILED] => true,
            [self::FAILED, self::SENT] => true, // Retry
            [self::FAILED, self::DELIVERED] => true,
            default => false,
        };
    }
}
