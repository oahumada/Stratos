<?php

namespace App\Models;

use App\Enums\ContextType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Conversation extends Model
{
    /** @use HasFactory<\Database\Factories\ConversationFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $casts = [
        'id' => 'string',
        'organization_id' => 'integer',
        'created_by' => 'integer',
        'is_active' => 'boolean',
        'context_type' => ContextType::class,
        'last_message_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(People::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('deleted_at');
    }

    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    // Helpers
    public function isParticipant(string $peopleId): bool
    {
        return $this->participants()
            ->where('people_id', $peopleId)
            ->whereNull('left_at')
            ->exists();
    }

    public function addParticipant(string $peopleId, bool $canSend = true, bool $canRead = true): ConversationParticipant
    {
        return $this->participants()->create([
            'organization_id' => $this->organization_id,
            'people_id' => $peopleId,
            'can_send' => $canSend,
            'can_read' => $canRead,
            'joined_at' => now(),
        ]);
    }

    public function removeParticipant(string $peopleId): void
    {
        $this->participants()
            ->where('people_id', $peopleId)
            ->update(['left_at' => now()]);
    }

    public function getUnreadCount(string $peopleId): int
    {
        return $this->participants()
            ->where('people_id', $peopleId)
            ->first()?->unread_count ?? 0;
    }

    public function markAsRead(string $peopleId): void
    {
        $this->participants()
            ->where('people_id', $peopleId)
            ->update([
                'last_read_at' => now(),
                'unread_count' => 0,
            ]);
    }
}
