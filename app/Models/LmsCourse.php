<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsCourse extends Model
{
    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'category',
        'level',
        'estimated_duration_minutes',
        'cost_per_seat',
        'currency',
        'xp_points',
        'is_active',
        'organization_id',
    ];

    public function modules()
    {
        return $this->hasMany(LmsModule::class)->orderBy('order');
    }

    public function lessons()
    {
        return $this->hasManyThrough(LmsLesson::class, LmsModule::class);
    }

    public function enrollments()
    {
        return $this->hasMany(LmsEnrollment::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
