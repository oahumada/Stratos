<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsQuiz extends Model
{
    protected $fillable = [
        'lms_lesson_id',
        'organization_id',
        'title',
        'description',
        'passing_score',
        'max_attempts',
        'time_limit_minutes',
        'shuffle_questions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'shuffle_questions' => 'boolean',
            'is_active' => 'boolean',
            'passing_score' => 'integer',
            'max_attempts' => 'integer',
            'time_limit_minutes' => 'integer',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(LmsLesson::class, 'lms_lesson_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(LmsQuizQuestion::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(LmsQuizAttempt::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }
}
