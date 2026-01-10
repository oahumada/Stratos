<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforcePlanTransformationProject extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'workforce_plan_id',
        'project_name',
        'project_type',
        'expected_impact',
        'estimated_fte_impact',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'estimated_fte_impact' => 'integer',
        'created_at' => 'datetime',
    ];

    public function workforcePlan(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlan::class);
    }
}
