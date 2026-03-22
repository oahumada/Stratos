<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentInteraction extends Model
{
    protected $fillable = [
        'agent_name',
        'user_id',
        'organization_id',
        'prompt_hash',
        'latency_ms',
        'token_count',
        'status',
        'error_message',
        'input_length',
        'output_length',
        'provider',
        'model',
        'context',
    ];

    protected $casts = [
        'latency_ms' => 'integer',
        'token_count' => 'integer',
        'input_length' => 'integer',
        'output_length' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
