<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsSurveyResponse extends Model
{
    protected $fillable = [
        'survey_id',
        'user_id',
        'enrollment_id',
        'answers',
        'nps_score',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'nps_score' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    // ── Relations ──

    public function survey(): BelongsTo
    {
        return $this->belongsTo(LmsSurvey::class, 'survey_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(LmsEnrollment::class, 'enrollment_id');
    }
}
