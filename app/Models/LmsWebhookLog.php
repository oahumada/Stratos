<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsWebhookLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'webhook_id',
        'event',
        'payload',
        'response_code',
        'response_body',
        'status',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'response_code' => 'integer',
            'attempted_at' => 'datetime',
        ];
    }

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(LmsWebhook::class, 'webhook_id');
    }
}
