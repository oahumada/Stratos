<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychometricProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'people_id',
        'assessment_session_id',
        'trait_name',
        'score',
        'rationale',
        'metadata'
    ];

    protected $casts = [
        'score' => 'float',
        'metadata' => 'array'
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AssessmentSession::class, 'assessment_session_id');
    }
}
