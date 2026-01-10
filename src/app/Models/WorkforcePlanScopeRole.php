<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforcePlanScopeRole extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'workforce_plan_id',
        'role_id',
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

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
