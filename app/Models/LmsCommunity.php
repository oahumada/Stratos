<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsCommunity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'type',
        'practice_domain',
        'domain_skills',
        'learning_goals',
        'status',
        'max_members',
        'health_score',
        'social_presence',
        'cognitive_presence',
        'teaching_presence',
        'facilitator_id',
        'course_id',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'domain_skills' => 'array',
            'learning_goals' => 'array',
            'max_members' => 'integer',
            'health_score' => 'decimal:2',
            'social_presence' => 'decimal:2',
            'cognitive_presence' => 'decimal:2',
            'teaching_presence' => 'decimal:2',
        ];
    }

    // --- Relationships ---

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function facilitator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'facilitator_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(LmsCommunityMember::class, 'community_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LmsCommunityActivity::class, 'community_id');
    }

    public function knowledgeArticles(): HasMany
    {
        return $this->hasMany(LmsCommunityKnowledge::class, 'community_id');
    }

    // --- Scopes ---

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActiveMembers($query)
    {
        return $query->whereHas('members', function ($q) {
            $q->where('last_active_at', '>=', now()->subDays(30));
        });
    }

    // --- Helpers ---

    public function getMemberCount(): int
    {
        return $this->members()->count();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isFull(): bool
    {
        if ($this->max_members === null) {
            return false;
        }

        return $this->getMemberCount() >= $this->max_members;
    }
}
