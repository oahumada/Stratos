<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentFeedback extends Model
{
    use HasFactory;

    protected $table = 'assessment_feedback';

    protected $fillable = [
        'assessment_request_id',
        'question',
        'answer',
        'metadata',
        'skill_id',
        'score',
        'evidence_url',
        'confidence_level',
    ];

    protected $casts = [
        'metadata' => 'array',
        'score' => 'integer',
        'confidence_level' => 'integer',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssessmentRequest::class, 'assessment_request_id');
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
