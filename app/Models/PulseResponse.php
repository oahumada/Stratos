<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PulseResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'pulse_survey_id',
        'people_id',
        'answers',
        'sentiment_score',
    ];

    protected $casts = [
        'answers' => 'array',
        'sentiment_score' => 'decimal:2',
    ];

    public function survey()
    {
        return $this->belongsTo(PulseSurvey::class, 'pulse_survey_id');
    }

    public function people()
    {
        return $this->belongsTo(People::class, 'people_id');
    }
}
