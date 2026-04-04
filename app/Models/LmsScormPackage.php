<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsScormPackage extends Model
{
    protected $fillable = [
        'organization_id',
        'lms_course_id',
        'lms_lesson_id',
        'title',
        'filename',
        'version',
        'manifest_data',
        'entry_point',
        'identifier',
        'storage_path',
        'status',
        'file_size_bytes',
    ];

    protected function casts(): array
    {
        return [
            'manifest_data' => 'array',
            'file_size_bytes' => 'integer',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'lms_course_id');
    }

    public function lesson()
    {
        return $this->belongsTo(LmsLesson::class, 'lms_lesson_id');
    }

    public function trackings()
    {
        return $this->hasMany(LmsScormTracking::class, 'lms_scorm_package_id');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }

    public function getLaunchUrl(): string
    {
        return "/storage/scorm/{$this->organization_id}/{$this->id}/{$this->entry_point}";
    }
}
