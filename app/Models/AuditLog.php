<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AuditLog - Track all CRUD operations for compliance and debugging
 *
 * Auto-tracked via Eloquent Observers for models that use HasAudit trait.
 * Multi-tenant by organization_id. Immutable (no updates after creation).
 */
class AuditLog extends Model
{
    public $timestamps = true;

    // Only created_at is used (immutable log)
    const UPDATED_AT = 'updated_at';

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'changes',
        'metadata',
        'triggered_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'changes' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ─────────────────────────────────────────────────────── Relationships ──

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─────────────────────────────────────────────────────── Scopes ─────────

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }

    public function scopeForEntity($query, string $entityType, string $entityId)
    {
        return $query->where('entity_type', $entityType)->where('entity_id', $entityId);
    }

    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeCreatedBy($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeTriggeredBy($query, string $triggeredBy)
    {
        return $query->where('triggered_by', $triggeredBy);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ─────────────────────────────────────────────────────── Helper Methods ─

    public function isCreation(): bool
    {
        return $this->action === 'created';
    }

    public function isUpdate(): bool
    {
        return $this->action === 'updated';
    }

    public function isDeletion(): bool
    {
        return $this->action === 'deleted';
    }

    /**
     * Get human-readable summary of changes
     */
    public function getChangeSummary(): string
    {
        if (! $this->changes) {
            return 'No changes recorded';
        }

        $parts = [];
        foreach ($this->changes as $field => $values) {
            $parts[] = "{$field}: {$values[0]} → {$values[1]}";
        }

        return implode(', ', $parts);
    }
}

