<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforceActionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'scenario_id',
        'organization_id',
        'action_title',
        'description',
        'owner_user_id',
        'status',
        'priority',
        'due_date',
        'progress_pct',
        'budget',
        'actual_cost',
        'unit_name',
        'lever',
        'hybrid_coverage_pct',
    ];

    protected function casts(): array
    {
        return [
            'due_date'            => 'date',
            'progress_pct'        => 'integer',
            'hybrid_coverage_pct' => 'integer',
            'budget'              => 'decimal:2',
            'actual_cost'         => 'decimal:2',
        ];
    }

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
