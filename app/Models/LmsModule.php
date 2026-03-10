<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsModule extends Model
{
protected $fillable = [
        'lms_course_id',
        'title',
        'order',
    ];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'lms_course_id');
    }

    public function lessons()
    {
        return $this->hasMany(LmsLesson::class)->orderBy('order');
    }
}
