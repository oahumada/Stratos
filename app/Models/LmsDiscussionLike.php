<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsDiscussionLike extends Model
{
    protected $fillable = [
        'lms_discussion_id',
        'user_id',
    ];

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(LmsDiscussion::class, 'lms_discussion_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
