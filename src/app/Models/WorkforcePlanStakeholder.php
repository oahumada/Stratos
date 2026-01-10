<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforcePlanStakeholder extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'workforce_plan_id',
        'user_id',
        'role',
        'represents',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function workforcePlan(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
