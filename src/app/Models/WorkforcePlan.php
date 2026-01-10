<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkforcePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'description',
        'start_date',
        'end_date',
        'planning_horizon_months',
        'scope_type',
        'scope_notes',
        'strategic_context',
        'budget_constraints',
        'legal_constraints',
        'labor_relations_constraints',
        'status',
        'approved_at',
        'approved_by',
        'owner_user_id',
        'sponsor_user_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'planning_horizon_months' => 'integer',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeUnits(): HasMany
    {
        return $this->hasMany(WorkforcePlanScopeUnit::class);
    }

    public function scopeRoles(): HasMany
    {
        return $this->hasMany(WorkforcePlanScopeRole::class);
    }

    public function transformationProjects(): HasMany
    {
        return $this->hasMany(WorkforcePlanTransformationProject::class);
    }

    public function talentRisks(): HasMany
    {
        return $this->hasMany(WorkforcePlanTalentRisk::class);
    }

    public function stakeholders(): HasMany
    {
        return $this->hasMany(WorkforcePlanStakeholder::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(WorkforcePlanDocument::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isApproved(): bool
    {
        return in_array($this->status, ['approved', 'active']);
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'in_review']);
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);
    }

    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    public static function generateCode(int $orgId): string
    {
        $year = now()->year;
        $quarter = 'Q' . now()->quarter;
        $count = self::where('organization_id', $orgId)
            ->whereYear('created_at', $year)
            ->count() + 1;

        return "WFP-{$year}-{$quarter}-{$count}";
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($plan) {
            if (empty($plan->code)) {
                $plan->code = self::generateCode($plan->organization_id);
            }
        });
    }
}
