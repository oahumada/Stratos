<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsCommunityKnowledge extends Model
{
    use HasFactory;

    protected $table = 'lms_community_knowledge';

    protected $fillable = [
        'community_id',
        'author_id',
        'title',
        'content',
        'category',
        'tags',
        'views_count',
        'likes_count',
        'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_pinned' => 'boolean',
        ];
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(LmsCommunity::class, 'community_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('likes_count');
    }
}
