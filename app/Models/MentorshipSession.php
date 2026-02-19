<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorshipSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'development_action_id',
        'session_date',
        'summary',
        'next_steps',
        'duration_minutes',
        'status',
    ];

    protected $casts = [
        'session_date' => 'datetime',
    ];

    public function developmentAction(): BelongsTo
    {
        return $this->belongsTo(DevelopmentAction::class);
    }
}
