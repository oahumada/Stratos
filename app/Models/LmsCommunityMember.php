<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsCommunityMember extends Model
{
    use HasFactory;

    // Role constants matching CommunityFacilitatorAgent
    const ROLE_NOVICE = 'novice';
    const ROLE_MEMBER = 'member';
    const ROLE_CONTRIBUTOR = 'contributor';
    const ROLE_MENTOR = 'mentor';
    const ROLE_EXPERT = 'expert';
    const ROLE_LEADER = 'leader';

    const ROLE_PROGRESSION = [
        self::ROLE_NOVICE,
        self::ROLE_MEMBER,
        self::ROLE_CONTRIBUTOR,
        self::ROLE_MENTOR,
        self::ROLE_EXPERT,
        self::ROLE_LEADER,
    ];

    // LPP stage constants
    const LPP_PERIPHERAL = 'peripheral';
    const LPP_ACTIVE = 'active';
    const LPP_CORE = 'core';

    protected $fillable = [
        'community_id',
        'user_id',
        'role',
        'lpp_stage',
        'contribution_score',
        'discussions_count',
        'ugc_count',
        'peer_reviews_count',
        'mentorships_count',
        'joined_at',
        'last_active_at',
    ];

    protected function casts(): array
    {
        return [
            'contribution_score' => 'decimal:2',
            'discussions_count' => 'integer',
            'ugc_count' => 'integer',
            'peer_reviews_count' => 'integer',
            'mentorships_count' => 'integer',
            'joined_at' => 'datetime',
            'last_active_at' => 'datetime',
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

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->where('last_active_at', '>=', now()->subDays(30));
    }

    // --- Helpers ---

    public function isLeader(): bool
    {
        return $this->role === self::ROLE_LEADER;
    }

    public function isMentor(): bool
    {
        return $this->role === self::ROLE_MENTOR;
    }

    public function isNovice(): bool
    {
        return $this->role === self::ROLE_NOVICE;
    }

    /**
     * Whether this member can serve as a mentor (role >= mentor).
     */
    public function canMentor(): bool
    {
        $roleIndex = array_search($this->role, self::ROLE_PROGRESSION);

        return $roleIndex !== false && $roleIndex >= array_search(self::ROLE_MENTOR, self::ROLE_PROGRESSION);
    }
}
