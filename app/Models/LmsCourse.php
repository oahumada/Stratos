<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'cert_template_id',
        'is_active',
        'organization_id',
        'tags',
        'featured',
        'enrollment_type',
    ];

    protected function casts(): array
    {
        return [
            'cost_per_seat' => 'float',
            'cert_min_resource_completion_ratio' => 'float',
            'cert_require_assessment_score' => 'boolean',
            'cert_min_assessment_score' => 'float',
            'cert_template_id' => 'integer',
            'is_active' => 'boolean',
            'tags' => 'array',
            'featured' => 'boolean',
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

    public function ratings()
    {
        return $this->hasMany(LmsCourseRating::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function certificateTemplate(): BelongsTo
    {
        return $this->belongsTo(LmsCertificateTemplate::class, 'cert_template_id');
    }
}
