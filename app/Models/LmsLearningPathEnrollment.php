<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsLearningPathEnrollment extends Model
{
    protected $fillable = [
        'lms_learning_path_id',
        'user_id',
        'organization_id',
        'status',
        'progress_percentage',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'float',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function learningPath()
    {
        return $this->belongsTo(LmsLearningPath::class, 'lms_learning_path_id');
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
