<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutiveAggregate extends Model
{
    protected $table = 'executive_aggregates';

    protected $fillable = [
        'organization_id',
        'scenario_id',
        'headcount',
        'total_scenarios',
        'avg_gap',
        'avg_readiness',
        'ready_now',
        'level_0_count',
        'level_1_count',
        'level_2_count',
        'level_3_count',
        'level_4_count',
        'level_5_count',
        'upskilled_count',
        'bot_strategies',
        'total_pivot_rows',
        'total_roles',
        'augmented_roles',
        'avg_turnover_risk',
    ];

    protected $casts = [
        'headcount' => 'integer',
        'total_scenarios' => 'integer',
        'avg_gap' => 'float',
        'avg_readiness' => 'float',
        'avg_turnover_risk' => 'float',
        'ready_now' => 'integer',
        'upskilled_count' => 'integer',
        'bot_strategies' => 'integer',
        'total_pivot_rows' => 'integer',
        'total_roles' => 'integer',
        'augmented_roles' => 'integer',
        'level_0_count' => 'integer',
        'level_1_count' => 'integer',
        'level_2_count' => 'integer',
        'level_3_count' => 'integer',
        'level_4_count' => 'integer',
        'level_5_count' => 'integer',
    ];
}
