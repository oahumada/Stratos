<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScenarioStatusEvent extends Model
{
    protected $table = 'scenario_status_events';

    public $timestamps = false; // Solo created_at

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'scenario_id',
        'from_decision_status',
        'to_decision_status',
        'from_execution_status',
        'to_execution_status',
        'changed_by',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(StrategicPlanningScenarios::class, 'scenario_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Helper methods
    public function isDecisionStatusChange(): bool
    {
        return $this->from_decision_status !== null || $this->to_decision_status !== null;
    }

    public function isExecutionStatusChange(): bool
    {
        return $this->from_execution_status !== null || $this->to_execution_status !== null;
    }
}
