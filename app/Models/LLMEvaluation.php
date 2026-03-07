<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LLMEvaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'llm_evaluations';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }
        });
    }

    protected $fillable = [
        'organization_id',
        'evaluable_type',
        'evaluable_id',
        'llm_provider',
        'llm_model',
        'llm_version',
        'input_content',
        'output_content',
        'context_content',
        'faithfulness_score',
        'relevance_score',
        'context_alignment_score',
        'coherence_score',
        'hallucination_rate',
        'composite_score',
        'quality_level',
        'normalized_score',
        'metric_details',
        'issues_detected',
        'recommendations',
        'status',
        'error_message',
        'retry_count',
        'started_at',
        'completed_at',
        'created_by',
        'evaluation_config',
        'processing_ms',
        'tokens_used',
        'is_latest',
        'superseded_by_id',
    ];

    protected $casts = [
        'metric_details' => 'array',
        'issues_detected' => 'array',
        'recommendations' => 'array',
        'evaluation_config' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'faithfulness_score' => 'decimal:2',
        'relevance_score' => 'decimal:2',
        'context_alignment_score' => 'decimal:2',
        'coherence_score' => 'decimal:2',
        'hallucination_rate' => 'decimal:2',
        'composite_score' => 'decimal:2',
        'normalized_score' => 'decimal:2',
    ];

    /**
     * Get the organization that owns this evaluation
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user who created this evaluation
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the evaluable model (polymorphic relationship)
     */
    public function evaluable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the evaluation that superseded this one (if any)
     */
    public function supersededBy(): BelongsTo
    {
        return $this->belongsTo(LLMEvaluation::class, 'superseded_by_id');
    }

    /**
     * Scope: Filter by organization (multi-tenant)
     */
    public function scopeForOrganization(Builder $query, int|string $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope: Filter by provider
     */
    public function scopeByProvider(Builder $query, string $provider): Builder
    {
        return $query->where('llm_provider', $provider);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by quality level
     */
    public function scopeByQualityLevel(Builder $query, string $qualityLevel): Builder
    {
        return $query->where('quality_level', $qualityLevel);
    }

    /**
     * Scope: Get only latest evaluations per evaluable
     */
    public function scopeLatestOnly(Builder $query): Builder
    {
        return $query->where('is_latest', true);
    }

    /**
     * Scope: Get completed evaluations
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Get failed evaluations
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->whereIn('status', ['failed', 'critical']);
    }

    /**
     * Scope: Filter by composite score above threshold
     */
    public function scopeAboveThreshold(Builder $query, float $threshold = 0.80): Builder
    {
        return $query->where('composite_score', '>=', $threshold)->where('status', 'completed');
    }

    /**
     * Scope: Filter by composite score below threshold
     */
    public function scopeBelowThreshold(Builder $query, float $threshold = 0.80): Builder
    {
        return $query->where('composite_score', '<', $threshold)->where('status', 'completed');
    }

    /**
     * Scope: Get recent evaluations (last N days)
     */
    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Check if evaluation is passing quality standards
     */
    public function isPassing(): bool
    {
        if ($this->status !== 'completed') {
            return false;
        }

        $config = config('ragas');
        $threshold = $config['min_composite_score'] ?? 0.80;

        return (float) $this->composite_score >= $threshold;
    }

    /**
     * Get quality level badge color
     */
    public function getQualityBadgeColor(): string
    {
        return match ($this->quality_level) {
            'excellent' => 'success',
            'good' => 'info',
            'acceptable' => 'warning',
            'poor' => 'error',
            'critical' => 'error',
            default => 'default',
        };
    }

    /**
     * Mark evaluation as superseded by a new one
     */
    public function markSuperseded(LLMEvaluation $newEvaluation): void
    {
        $this->update([
            'is_latest' => false,
            'superseded_by_id' => $newEvaluation->id,
        ]);
    }

    /**
     * Calculate composite score from individual metrics
     */
    public function calculateCompositeScore(): float
    {
        if (! $this->faithfulness_score || ! $this->relevance_score || ! $this->context_alignment_score) {
            return 0.0;
        }

        $config = config('ragas.thresholds');
        $weights = [
            'faithfulness' => $config['faithfulness']['weight'] ?? 0.30,
            'relevance' => $config['relevance']['weight'] ?? 0.25,
            'context_alignment' => $config['context_alignment']['weight'] ?? 0.20,
            'coherence' => $config['coherence']['weight'] ?? 0.15,
            'hallucination' => $config['hallucination']['weight'] ?? 0.10,
        ];

        $hallucination_inverse = 1 - (float) $this->hallucination_rate;

        $composite = (
            ((float) $this->faithfulness_score * $weights['faithfulness']) +
            ((float) $this->relevance_score * $weights['relevance']) +
            ((float) $this->context_alignment_score * $weights['context_alignment']) +
            ((float) $this->coherence_score * $weights['coherence']) +
            ($hallucination_inverse * $weights['hallucination'])
        );

        return round(min(1.0, max(0.0, $composite)), 2);
    }

    /**
     * Determine quality level based on composite score
     */
    public function determineQualityLevel(float $score): string
    {
        return match (true) {
            $score >= 0.90 => 'excellent',
            $score >= 0.80 => 'good',
            $score >= 0.70 => 'acceptable',
            $score >= 0.50 => 'poor',
            default => 'critical',
        };
    }

    /**
     * Normalize score based on provider baseline
     */
    public function normalizeScore(): float
    {
        if (! $this->composite_score) {
            return 0.0;
        }

        $config = config('ragas.providers');
        $providerConfig = $config[$this->llm_provider] ?? $config['mock'];
        $baseline = $providerConfig['baseline_score'] ?? 0.90;

        // Adjust composite score relative to provider baseline
        // (Scores above baseline get boosted, below baseline get penalized)
        $normalized = (float) $this->composite_score / $baseline;

        return round(min(1.0, $normalized), 2);
    }
}
