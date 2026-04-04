<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsSurvey extends Model
{
    protected $fillable = [
        'organization_id',
        'course_id',
        'title',
        'description',
        'questions',
        'is_mandatory',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'questions' => 'array',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // ── Relations ──

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(LmsSurveyResponse::class, 'survey_id');
    }

    // ── Scopes ──

    public function scopeForOrganization(Builder $query, int $orgId): Builder
    {
        return $query->where('lms_surveys.organization_id', $orgId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
