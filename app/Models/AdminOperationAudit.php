<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminOperationAudit extends Model
{
    protected $table = 'admin_operations_audit';
    protected $guarded = [];
    
    protected $casts = [
        'parameters' => 'array',
        'dry_run_preview' => 'array',
        'result' => 'array',
        'duration_seconds' => 'float',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helpers
    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function markAsDryRun(array $preview): void
    {
        $this->update([
            'status' => 'dry_run',
            'dry_run_preview' => $preview,
        ]);
    }

    public function markAsRunning(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    public function markAsSuccess(array $result, int $recordsProcessed = 0, int $recordsAffected = 0): void
    {
        $this->update([
            'status' => 'success',
            'result' => $result,
            'records_processed' => $recordsProcessed,
            'records_affected' => $recordsAffected,
            'completed_at' => now(),
            'duration_seconds' => $this->calculateDuration(),
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'completed_at' => now(),
            'duration_seconds' => $this->calculateDuration(),
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => 'cancelled',
            'completed_at' => now(),
            'duration_seconds' => $this->calculateDuration(),
        ]);
    }

    private function calculateDuration(): float
    {
        if (!$this->started_at) {
            return 0;
        }
        return $this->started_at->diffInSeconds(now());
    }
}

