<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsPeerReview extends Model
{
    protected $fillable = [
        'organization_id',
        'assignment_id',
        'reviewer_id',
        'reviewee_id',
        'submission_url',
        'submission_text',
        'review_score',
        'review_feedback',
        'rubric_scores',
        'status',
        'submitted_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'review_score' => 'float',
            'rubric_scores' => 'array',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(LmsLesson::class, 'assignment_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function isOverdue(): bool
    {
        if ($this->status !== 'pending_submission' && $this->status !== 'under_review') {
            return false;
        }

        return $this->created_at && $this->created_at->diffInDays(now()) > 7;
    }

    public function averageRubricScore(): ?float
    {
        if (empty($this->rubric_scores)) {
            return null;
        }

        $scores = collect($this->rubric_scores);
        if ($scores->isEmpty()) {
            return null;
        }

        $totalPercent = $scores->sum(fn ($item) => ($item['max'] ?? 1) > 0
            ? ($item['score'] ?? 0) / ($item['max'] ?? 1)
            : 0);

        return round($totalPercent / $scores->count() * 100, 2);
    }
}
