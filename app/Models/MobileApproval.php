<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MobileApproval - Mobile approval workflow requests
 *
 * Manages approval workflows for escalated auto-remediation actions that
 * require manual intervention (approvals requiring user decision).
 *
 * Attributes:
 * - id: Primary key
 * - organization_id: Multi-tenant scope
 * - user_id: Who needs to approve
 * - requester_id: Who requested the approval
 * - request_type: 'escalated_action', 'manual_approval', 'policy_exception'
 * - title: Approval title
 * - description: What needs to be approved
 * - context: JSON - additional details (anomaly data, action details)
 * - severity: 'info', 'warning', 'critical'
 * - status: 'pending', 'approved', 'rejected', 'escalated', 'expired'
 * - requested_at: When approval was requested
 * - expires_at: When approval expires (24h default)
 * - approved_at: When approved
 * - rejected_at: When rejected
 * - approver_notes: Reason for approval
 * - rejection_reason: Why it was rejected
 * - approval_data: JSON - additional data from approver
 * - archived_at: When archived (for retention)
 * - created_at, updated_at: Timestamps
 *
 * Relationships:
 * - user: User who must approve
 * - requester: User who requested approval
 * - organization: Organization scope
 *
 * Workflow:
 * pending → approved/rejected
 * pending + expired → escalated
 *
 * Integration:
 * - Triggered by RemediationService for escalations
 * - Notifications via PushNotificationService
 * - Sync via OfflineQueueService (approval_response)
 */
class MobileApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requester_id',
        'organization_id',
        'request_type',
        'title',
        'description',
        'context',
        'severity',
        'status',
        'requested_at',
        'expires_at',
        'approved_at',
        'rejected_at',
        'approver_notes',
        'rejection_reason',
        'approval_data',
        'archived_at',
    ];

    protected $casts = [
        'context' => 'array',
        'approval_data' => 'array',
        'requested_at' => 'datetime',
        'expires_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Approval assigned to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Approval requested by User
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Relationship: Approval belongs to Organization
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope: Pending approvals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '>', now());
    }

    /**
     * Scope: For specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: For organization
     */
    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope: Critical severity
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope: Approved only
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Rejected only
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if approval is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending' && $this->expires_at > now();
    }

    /**
     * Check if approval has expired
     */
    public function hasExpired(): bool
    {
        return $this->expires_at < now();
    }

    /**
     * Check if approval can still be acted upon
     */
    public function isActionable(): bool
    {
        return $this->status === 'pending' && ! $this->hasExpired();
    }

    /**
     * Get human-readable status
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'escalated' => 'Escalated',
            'expired' => 'Expired',
            default => $this->status,
        };
    }

    /**
     * Get time remaining for approval
     */
    public function getTimeRemainingAttribute(): ?\DateTime
    {
        if ($this->status !== 'pending') {
            return null;
        }

        return $this->expires_at > now() ? $this->expires_at : null;
    }

    /**
     * Get seconds until expiration
     */
    public function getSecondsUntilExpirationAttribute(): ?int
    {
        if (! $this->getTimeRemainingAttribute()) {
            return null;
        }

        return $this->expires_at->diffInSeconds(now());
    }
}
