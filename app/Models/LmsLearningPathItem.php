<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsLearningPathItem extends Model
{
    protected $fillable = [
        'lms_learning_path_id',
        'lms_course_id',
        'order',
        'is_required',
        'unlock_after_item_id',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function learningPath()
    {
        return $this->belongsTo(LmsLearningPath::class, 'lms_learning_path_id');
    }

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'lms_course_id');
    }

    public function prerequisite()
    {
        return $this->belongsTo(self::class, 'unlock_after_item_id');
    }

    public function dependents()
    {
        return $this->hasMany(self::class, 'unlock_after_item_id');
    }

    /**
     * Check if this item is unlocked for a given user.
     * Unlocked if no prerequisite, or prerequisite's course is completed by user.
     */
    public function isUnlocked(int $userId): bool
    {
        if ($this->unlock_after_item_id === null) {
            return true;
        }

        $prerequisite = $this->prerequisite()->with('course')->first();

        if (! $prerequisite) {
            return true;
        }

        return LmsEnrollment::where('lms_course_id', $prerequisite->lms_course_id)
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->exists();
    }
}
