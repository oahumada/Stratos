<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsEnrollment extends Model
{
protected $fillable = [
        'lms_course_id',
        'user_id',
        'progress_percentage',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'lms_course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
