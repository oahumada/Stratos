<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsDiscussion extends Model
{
    protected $fillable = [
        'lms_course_id',
        'lms_lesson_id',
        'user_id',
        'organization_id',
        'title',
        'body',
        'parent_id',
        'likes_count',
        'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'likes_count' => 'integer',
            'is_pinned' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'lms_course_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(LmsLesson::class, 'lms_lesson_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(LmsDiscussionLike::class);
    }

    public function scopeRootPosts($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeForCourse($query, int $courseId)
    {
        return $query->where('lms_course_id', $courseId);
    }

    public function scopeForLesson($query, int $lessonId)
    {
        return $query->where('lms_lesson_id', $lessonId);
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }
}
