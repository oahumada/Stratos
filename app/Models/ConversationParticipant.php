<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ConversationParticipant extends Model
{
    /** @use HasFactory<\Database\Factories\ConversationParticipantFactory> */
    use HasFactory;

    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $casts = [
        'id' => 'string',
        'conversation_id' => 'string',
        'organization_id' => 'integer',
        'people_id' => 'integer',
        'can_send' => 'boolean',
        'can_read' => 'boolean',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'last_read_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    // Relationships
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function people(): BelongsTo
    {
        return $this->belongsTo(People::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->left_at === null;
    }

    public function canSendMessages(): bool
    {
        return $this->can_send && $this->isActive();
    }

    public function canReadMessages(): bool
    {
        return $this->can_read;
    }

    public function markLeft(): void
    {
        $this->update(['left_at' => now()]);
    }
}
