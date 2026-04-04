<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsScormTracking extends Model
{
    protected $table = 'lms_scorm_tracking';

    protected $fillable = [
        'lms_scorm_package_id',
        'user_id',
        'organization_id',
        'lms_enrollment_id',
        'cmi_data',
        'lesson_status',
        'score_raw',
        'score_min',
        'score_max',
        'total_time',
        'session_count',
        'suspend_data',
        'lesson_location',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'cmi_data' => 'array',
            'score_raw' => 'float',
            'score_min' => 'float',
            'score_max' => 'float',
            'session_count' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function package()
    {
        return $this->belongsTo(LmsScormPackage::class, 'lms_scorm_package_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(LmsEnrollment::class, 'lms_enrollment_id');
    }

    public function isCompleted(): bool
    {
        return in_array($this->lesson_status, ['completed', 'passed']);
    }

    public function addSessionTime(string $sessionTime): void
    {
        $totalSeconds = $this->timeToSeconds($this->total_time);
        $sessionSeconds = $this->timeToSeconds($sessionTime);
        $newTotal = $totalSeconds + $sessionSeconds;

        $hours = intdiv($newTotal, 3600);
        $minutes = intdiv($newTotal % 3600, 60);
        $seconds = $newTotal % 60;

        $this->total_time = sprintf('%04d:%02d:%02d', $hours, $minutes, $seconds);
    }

    private function timeToSeconds(string $time): int
    {
        $parts = explode(':', $time);
        if (count($parts) !== 3) {
            return 0;
        }

        return (int) $parts[0] * 3600 + (int) $parts[1] * 60 + (int) $parts[2];
    }
}
