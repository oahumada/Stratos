<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessMetric extends Model
{
    protected $fillable = [
        'organization_id',
        'metric_name',
        'metric_value',
        'unit',
        'period_date',
        'source',
        'department_id',
        'people_id',
        'metadata'
    ];

    protected $casts = [
        'metric_value' => 'decimal:2',
        'period_date' => 'date',
        'metadata' => 'array'
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class, 'organization_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Departments::class, 'department_id');
    }
}
