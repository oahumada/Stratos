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
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssessmentRequest::class, 'assessment_request_id');
    }
}
