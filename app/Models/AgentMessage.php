<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentMessage extends Model
{
    use BelongsToOrganization;
    use HasFactory;

    protected $fillable = [
        'execution_id',
        'task_id',
        'channel',
        'agent_name',
        'payload',
        'result',
        'status',
        'organization_id',
        'attempts',
    ];

    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
        'attempts' => 'integer',
    ];

    public function scopeForExecution($query, string $executionId)
    {
        return $query->where('execution_id', $executionId);
    }

    public function scopeForTask($query, string $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeForChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
