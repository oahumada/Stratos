<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OfflineQueue - Mobile offline request queue
 *
 * Stores requests that were made while device was offline for later sync.
 * Supports approval responses, webhook callbacks, and webhook registrations.
 *
 * Attributes:
 * - id: Primary key
 * - user_id: User who made the request
 * - organization_id: Multi-tenant scope
 * - request_type: 'approval_response', 'webhook_callback', 'webhook_registration'
 * - endpoint: Backend endpoint to call (e.g., '/api/mobile/approvals/{id}/approve')
 * - payload: JSON - Request body/data
 * - deduplication_key: Optional - Prevent duplicate submissions
 * - status: 'pending', 'synced', 'duplicate', 'failed', 'error'
 * - retry_count: Number of failed sync attempts
 * - last_error: Error message from last failed attempt
 * - response_data: JSON - Response from backend if successful
 * - queued_at: When request was queued
 * - synced_at: When successfully synced
 * - created_at, updated_at: Timestamps
 *
 * Relationships:
 * - user: User who made request
 * - organization: Organization scope
 *
 * Workflow:
 * offline → queue → pending
 * pending → synced (success) or failed (3 retries) or duplicate (already processed)
 *
 * Integration:
 * - Triggered automatically on app when offline
 * - Synced via /api/mobile/offline-queue/sync endpoint
 * - Watched by background job for automatic retry
 */
class OfflineQueue extends Model
{
    use HasFactory;

    protected $table = 'offline_queue';

    protected $fillable = [
        'user_id',
        'organization_id',
        'request_type',
        'endpoint',
        'payload',
        'deduplication_key',
        'status',
        'retry_count',
        'last_error',
        'response_data',
        'queued_at',
        'synced_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'response_data' => 'array',
        'queued_at' => 'datetime',
        'synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Request by User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Request in Organization
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope: Pending sync
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: For user
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
     * Scope: Failed requests
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: Approval responses
     */
    public function scopeApprovalResponses($query)
    {
        return $query->where('request_type', 'approval_response');
    }

    /**
     * Scope: Webhook callbacks
     */
    public function scopeWebhookCallbacks($query)
    {
        return $query->where('request_type', 'webhook_callback');
    }

    /**
     * Scope: Ready for retry
     */
    public function scopeReadyForRetry($query)
    {
        return $query->where('status', 'failed')
            ->where('retry_count', '<', 3);
    }

    /**
     * Check if request is still pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if request was synced
     */
    public function isSynced(): bool
    {
        return $this->status === 'synced';
    }

    /**
     * Check if request is a duplicate
     */
    public function isDuplicate(): bool
    {
        return $this->status === 'duplicate';
    }

    /**
     * Can this request be retried
     */
    public function canRetry(): bool
    {
        return $this->status === 'failed' && $this->retry_count < 3;
    }

    /**
     * Get time in queue
     */
    public function getTimeInQueueAttribute(): ?\DateTime
    {
        return $this->synced_at ?? now();
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Waiting to Sync',
            'synced' => 'Synced',
            'duplicate' => 'Duplicate',
            'failed' => 'Failed',
            'error' => 'Error',
            default => $this->status,
        };
    }

    /**
     * Extract approval ID from payload (if applicable)
     */
    public function getApprovalIdAttribute(): ?int
    {
        if ($this->request_type === 'approval_response') {
            return $this->payload['approval_id'] ?? null;
        }

        return null;
    }

    /**
     * Get endpoint name for display
     */
    public function getEndpointNameAttribute(): string
    {
        $parts = explode('/', trim($this->endpoint, '/'));

        return end($parts);
    }
}
