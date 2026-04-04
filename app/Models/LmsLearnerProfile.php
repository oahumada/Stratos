<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsLearnerProfile extends Model
{
    protected $fillable = [
        'organization_id',
        'user_id',
        'proficiency_level',
        'learning_pace',
        'preferred_content_type',
        'strengths',
        'weaknesses',
        'completed_assessments',
        'average_score',
        'last_calibrated_at',
    ];

    protected function casts(): array
    {
        return [
            'strengths' => 'array',
            'weaknesses' => 'array',
            'completed_assessments' => 'integer',
            'average_score' => 'float',
            'last_calibrated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
