<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsAdaptiveRule extends Model
{
    protected $fillable = [
        'organization_id',
        'course_id',
        'rule_name',
        'condition_type',
        'condition_value',
        'action_type',
        'action_config',
        'priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'action_config' => 'array',
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }
}
