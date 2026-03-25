<?php

namespace App\Models;

use App\Enums\MessageState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Message extends Model
{
    /** @use HasFactory<\Database\Factories\MessageFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $casts = [
        'id' => 'string',
        'conversation_id' => 'string',
        'organization_id' => 'integer',
        'people_id' => 'integer',
        'reply_to_message_id' => 'string',
        'state' => MessageState::class,
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = Str::uuid();
            $model->state = MessageState::SENT;
        });
    }

    // Relationships
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_message_id');
    }

    // Scopes
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeNotDeleted($query)
    {
        return $query->whereNull('deleted_at');
    }

    // Helpers
    public function markAsDelivered(): void
    {
        $this->update(['state' => MessageState::DELIVERED]);
    }

    public function markAsRead(): void
    {
        $this->update(['state' => MessageState::READ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['state' => MessageState::FAILED]);
    }

    public function isRead(): bool
    {
        return $this->state === MessageState::READ;
    }
}
