<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImprovementFeedback extends Model
{
    /** @use HasFactory<\Database\Factories\ImprovementFeedbackFactory> */
    use HasFactory;

    protected $table = 'improvement_feedback';

    protected $fillable = [
        'organization_id',
        'user_id',
        'agent_id',
        'evaluation_id',
        'intelligence_metric_id',
        'rating',
        'feedback_text',
        'tags',
        'context',
        'status',
        'processed_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'context' => 'array',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopeNegative(Builder $query): Builder
    {
        return $query->where('rating', '<=', 2);
    }

    public function scopePositive(Builder $query): Builder
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeByAgent(Builder $query, string $agent): Builder
    {
        return $query->where('agent_id', $agent);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessed(Builder $query): Builder
    {
        return $query->where('status', 'processed');
    }
}
