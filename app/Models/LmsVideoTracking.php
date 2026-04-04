<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsVideoTracking extends Model
{
    protected $fillable = [
        'organization_id',
        'enrollment_id',
        'lesson_id',
        'user_id',
        'provider',
        'video_id',
        'duration_seconds',
        'watched_seconds',
        'last_position',
        'completed',
        'completion_threshold',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'watched_seconds' => 'integer',
            'last_position' => 'integer',
            'completed' => 'boolean',
            'completion_threshold' => 'decimal:2',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(LmsEnrollment::class, 'enrollment_id');
    }

    public function lesson()
    {
        return $this->belongsTo(LmsLesson::class, 'lesson_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->duration_seconds <= 0) {
            return 0;
        }

        return round(min(100, ($this->watched_seconds / $this->duration_seconds) * 100), 2);
    }

    public function markComplete(): bool
    {
        if ($this->duration_seconds <= 0) {
            return false;
        }

        if ($this->watched_seconds >= $this->duration_seconds * (float) $this->completion_threshold) {
            $this->update(['completed' => true]);

            return true;
        }

        return false;
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }
}
