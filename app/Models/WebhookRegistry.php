<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookRegistry extends Model
{
    protected $table = 'webhook_registry';

    protected $fillable = [
        'organization_id',
        'webhook_url',
        'event_filters',
        'signing_secret',
        'raw_secret',
        'is_active',
        'last_triggered_at',
        'failure_count',
        'metadata',
    ];

    protected $casts = [
        'event_filters' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'failure_count' => 'integer',
        'last_triggered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = ['raw_secret'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class, 'organization_id');
    }

    public function getHealthAttribute(): string
    {
        return match (true) {
            $this->failure_count < 10 => 'healthy',
            $this->failure_count < 50 => 'degraded',
            default => 'critical',
        };
    }
}
