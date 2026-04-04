<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsLearningPath extends Model
{
    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'level',
        'estimated_duration_minutes',
        'is_mandatory',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
            'estimated_duration_minutes' => 'integer',
        ];
    }

    public function items()
    {
        return $this->hasMany(LmsLearningPathItem::class)->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(LmsLearningPathEnrollment::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }

    public function calculateEstimatedDuration(): int
    {
        return (int) $this->items()
            ->join('lms_courses', 'lms_courses.id', '=', 'lms_learning_path_items.lms_course_id')
            ->sum('lms_courses.estimated_duration_minutes');
    }
}
