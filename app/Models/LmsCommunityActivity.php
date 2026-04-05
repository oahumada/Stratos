<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsCommunityActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_id',
        'user_id',
        'activity_type',
        'title',
        'content',
        'metadata',
        'presence_type',
        'engagement_score',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'engagement_score' => 'integer',
        ];
    }

    // --- Relationships ---

    public function community(): BelongsTo
    {
        return $this->belongsTo(LmsCommunity::class, 'community_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // --- Scopes ---

    public function scopeByType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeByPresence($query, string $presenceType)
    {
        return $query->where('presence_type', $presenceType);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
