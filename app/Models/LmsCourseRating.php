<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsCourseRating extends Model
{
    protected $fillable = [
        'lms_course_id',
        'user_id',
        'organization_id',
        'rating',
        'review',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'lms_course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
