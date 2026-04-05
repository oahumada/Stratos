<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmbeddingAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'embedding_id',
        'action',
        'changes',
        'triggered_by',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function embedding(): BelongsTo
    {
        return $this->belongsTo(Embedding::class);
    }

    /**
     * Record an audit entry for an embedding change.
     */
    public static function record(
        int $organizationId,
        ?int $embeddingId,
        string $action,
        ?array $changes = null,
        string $triggeredBy = 'system',
    ): self {
        return static::create([
            'organization_id' => $organizationId,
            'embedding_id' => $embeddingId,
            'action' => $action,
            'changes' => $changes,
            'triggered_by' => $triggeredBy,
        ]);
    }
}
