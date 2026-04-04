<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsUserContent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'author_id',
        'title',
        'description',
        'content_type',
        'content_body',
        'content_url',
        'course_id',
        'status',
        'approved_by',
        'approved_at',
        'views_count',
        'likes_count',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'views_count' => 'integer',
            'likes_count' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }
}
