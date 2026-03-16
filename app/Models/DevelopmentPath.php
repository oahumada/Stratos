<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\BelongsToOrganization;

class DevelopmentPath extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'action_title',
        'organization_id',
        'people_id',
        'target_role_id',
        'status',
        'estimated_duration_months',
        'started_at',
        'completed_at',
        'steps',
        'metadata',
    ];

    protected $casts = [
        'status' => 'string',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'steps' => 'array',
        'metadata' => 'array',
    ];


    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function people(): BelongsTo
    {
        return $this->belongsTo(People::class);
    }

    public function targetRole(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'target_role_id');
    }

    public function actions()
    {
        return $this->hasMany(DevelopmentAction::class)->orderBy('order');
    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }
}
