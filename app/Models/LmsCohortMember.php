<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsCohortMember extends Model
{
    protected $fillable = [
        'cohort_id',
        'user_id',
        'role',
        'joined_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(LmsCohort::class, 'cohort_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
