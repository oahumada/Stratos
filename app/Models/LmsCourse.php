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
        'cert_min_resource_completion_ratio',
        'cert_require_assessment_score',
        'cert_min_assessment_score',
        'is_active',
        'organization_id',
    ];

    protected function casts(): array
    {
        return [
            'cost_per_seat' => 'float',
            'cert_min_resource_completion_ratio' => 'float',
            'cert_require_assessment_score' => 'boolean',
            'cert_min_assessment_score' => 'float',
            'is_active' => 'boolean',
        ];
    }

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
