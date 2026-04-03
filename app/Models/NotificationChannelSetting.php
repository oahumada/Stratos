<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationChannelSetting extends Model
{
    protected $fillable = ['organization_id', 'channel_type', 'is_enabled', 'global_config'];

    protected $casts = [
        'global_config' => 'array',
        'is_enabled' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
