<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsLessonAuditLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'organization_id',
        'user_id',
        'enrollment_id',
        'lesson_id',
        'module_id',
        'action',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(LmsEnrollment::class, 'enrollment_id');
    }

    public function lesson()
    {
        return $this->belongsTo(LmsLesson::class, 'lesson_id');
    }

    public function module()
    {
        return $this->belongsTo(LmsModule::class, 'module_id');
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }
}
