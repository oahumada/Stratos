<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforcePlanTalentRisk extends Model
{
    use HasFactory;

    protected $fillable = [
        'workforce_plan_id',
        'risk_type',
        'risk_description',
        'affected_unit_id',
        'affected_role_id',
        'severity',
        'likelihood',
        'mitigation_strategy',
    ];

    public function workforcePlan(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlan::class);
    }

    public function affectedRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'affected_role_id');
    }

    public function getRiskScore(): int
    {
        $severityScores = ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4];
        $likelihoodScores = ['low' => 1, 'medium' => 2, 'high' => 3];

        return ($severityScores[$this->severity] ?? 0) * ($likelihoodScores[$this->likelihood] ?? 0);
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical' || $this->getRiskScore() >= 9;
    }
}
