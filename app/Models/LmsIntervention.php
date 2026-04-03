<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsIntervention extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'lms_enrollment_id',
        'lms_course_id',
        'user_id',
        'status',
        'source',
        'metadata',
        'started_at',
        'cleared_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'started_at' => 'datetime',
        'cleared_at' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(LmsEnrollment::class, 'lms_enrollment_id');
    }

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'lms_course_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
