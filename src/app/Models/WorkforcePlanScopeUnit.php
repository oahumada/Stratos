<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforcePlanScopeUnit extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'workforce_plan_id',
        'unit_type',
        'unit_id',
        'unit_name',
        'inclusion_reason',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function workforcePlan(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlan::class);
    }
}
