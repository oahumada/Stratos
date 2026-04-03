<?php

namespace App\Models\Concerns;

trait HasWorkforceWorkflow
{
    public const WORKFORCE_STATUS_DRAFT = 'draft';

    public const WORKFORCE_STATUS_IN_REVIEW = 'in_review';

    public const WORKFORCE_STATUS_APPROVED = 'approved';

    public const WORKFORCE_STATUS_ACTIVE = 'active';

    public const WORKFORCE_STATUS_COMPLETED = 'completed';

    public const WORKFORCE_STATUS_ARCHIVED = 'archived';

    /** @var array<int, string> */
    public const WORKFORCE_ALLOWED_STATUSES = [
        self::WORKFORCE_STATUS_DRAFT,
        self::WORKFORCE_STATUS_IN_REVIEW,
        self::WORKFORCE_STATUS_APPROVED,
        self::WORKFORCE_STATUS_ACTIVE,
        self::WORKFORCE_STATUS_COMPLETED,
        self::WORKFORCE_STATUS_ARCHIVED,
    ];

    /** @var array<string, array<int, string>> */
    public const WORKFORCE_STATUS_TRANSITIONS = [
        self::WORKFORCE_STATUS_DRAFT => [self::WORKFORCE_STATUS_IN_REVIEW],
        self::WORKFORCE_STATUS_IN_REVIEW => [self::WORKFORCE_STATUS_APPROVED, self::WORKFORCE_STATUS_DRAFT],
        self::WORKFORCE_STATUS_APPROVED => [self::WORKFORCE_STATUS_ACTIVE, self::WORKFORCE_STATUS_IN_REVIEW],
        self::WORKFORCE_STATUS_ACTIVE => [self::WORKFORCE_STATUS_COMPLETED, self::WORKFORCE_STATUS_ARCHIVED],
        self::WORKFORCE_STATUS_COMPLETED => [self::WORKFORCE_STATUS_ARCHIVED],
        self::WORKFORCE_STATUS_ARCHIVED => [],
    ];

    public function canMutateWorkforceExecutionData(): bool
    {
        return in_array($this->status, [
            self::WORKFORCE_STATUS_DRAFT,
            self::WORKFORCE_STATUS_IN_REVIEW,
            self::WORKFORCE_STATUS_APPROVED,
            self::WORKFORCE_STATUS_ACTIVE,
        ], true);
    }

    public function canTransitionWorkforceStatusTo(string $nextStatus): bool
    {
        if (! in_array($nextStatus, self::WORKFORCE_ALLOWED_STATUSES, true)) {
            return false;
        }

        $currentStatus = (string) $this->status;
        if (! in_array($currentStatus, self::WORKFORCE_ALLOWED_STATUSES, true)) {
            return false;
        }

        if ($currentStatus === $nextStatus) {
            return true;
        }

        return in_array(
            $nextStatus,
            self::WORKFORCE_STATUS_TRANSITIONS[$currentStatus] ?? [],
            true,
        );
    }
}
