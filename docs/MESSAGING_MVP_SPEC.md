# 📨 Messaging MVP Specification

**Status:** Phase 1 Development (2026-03-25 to 2026-03-31)  
**Developer:** Solo (Omar)  
**Branch:** `feature/messaging-mvp`  
**Objective:** Build minimal conversational messaging system for Stratos with multi-tenant isolation, audit trails, and read state tracking.

---

## 1. High-Level Architecture

```
┌──────────────────────────────────────────────┐
│ Messaging System (MVP)                       │
├──────────────────────────────────────────────┤
│                                              │
│ ┌─ Domain Layer ────────────────────────┐   │
│ │  • Conversation (aggregate root)      │   │
│ │  • ConversationParticipant            │   │
│ │  • Message                            │   │
│ │  • MessageState (enum)                │   │
│ │  • ContextType (enum)                 │   │
│ └────────────────────────────────────────┘   │
│                                              │
│ ┌─ API Layer ───────────────────────────┐   │
│ │  • GET /api/messaging/conversations   │   │
│ │  • POST /api/messaging/conversations  │   │
│ │  • GET /conversations/{id}/messages   │   │
│ │  • POST /conversations/{id}/messages  │   │
│ │  • POST /messages/{id}/read           │   │
│ └────────────────────────────────────────┘   │
│                                              │
│ ┌─ Service Layer ───────────────────────┐   │
│ │  • ConversationService                │   │
│ │  • MessagingService                   │   │
│ │  • ParticipantService                 │   │
│ └────────────────────────────────────────┘   │
│                                              │
│ ┌─ Traits / Middleware ──────────────────┐   │
│ │  • Loggable (audit trail)             │   │
│ │  • TenantScoped (multi-tenant)         │   │
│ │  • Authorization (policies)            │   │
│ └────────────────────────────────────────┘   │
└──────────────────────────────────────────────┘
```

---

## 2. Domain Model

### 2.1 Enums

#### `MessageState`

```php
enum MessageState: string {
    case SENT = 'sent';           // Message queued for delivery
    case DELIVERED = 'delivered'; // Message received by recipient's device
    case READ = 'read';           // Message marked read by recipient
    case FAILED = 'failed';       // Delivery failed (transient)
}
```

#### `ContextType` (for linking conversations to business processes)

```php
enum ContextType: string {
    case NONE = 'none';                           // Ad-hoc conversation
    case LEARNING_ASSIGNMENT = 'learning_assignment';
    case PERFORMANCE_REVIEW = 'performance_review';
    case INCIDENT = 'incident';
    case SURVEY = 'survey';
    case ONBOARDING = 'onboarding';
}
```

### 2.2 Table Schemas

#### `conversations` table

```sql
CREATE TABLE conversations (
    id UUID PRIMARY KEY,
    organization_id UUID NOT NULL,
    title VARCHAR(255) NOT NULL,

    -- Contextual linking (optional)
    context_type ENUM('none', ...) DEFAULT 'none',
    context_id VARCHAR(255) NULLABLE,

    -- Metadata
    is_active BOOLEAN DEFAULT TRUE,
    participant_count INT DEFAULT 0,
    last_message_at TIMESTAMP NULLABLE,

    -- Audit
    created_by UUID NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    archived_at TIMESTAMP NULLABLE,

    -- Indexes
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES people(id),
    INDEX (organization_id, is_active),
    INDEX (organization_id, last_message_at DESC),
    INDEX (context_type, context_id)
);
```

#### `conversation_participants` table

```sql
CREATE TABLE conversation_participants (
    id UUID PRIMARY KEY,
    conversation_id UUID NOT NULL,
    organization_id UUID NOT NULL,
    people_id UUID NOT NULL,

    -- Permissions
    can_send BOOLEAN DEFAULT TRUE,
    can_read BOOLEAN DEFAULT TRUE,

    -- Tracking
    joined_at TIMESTAMP,
    left_at TIMESTAMP NULLABLE,
    last_read_at TIMESTAMP NULLABLE,
    unread_count INT DEFAULT 0,

    -- Audit
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    -- Indexes
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (people_id) REFERENCES people(id) ON DELETE CASCADE,
    UNIQUE (conversation_id, people_id),
    INDEX (people_id, organization_id),
    INDEX (conversation_id, unread_count)
);
```

#### `messages` table

```sql
CREATE TABLE messages (
    id UUID PRIMARY KEY,
    conversation_id UUID NOT NULL,
    organization_id UUID NOT NULL,
    people_id UUID NOT NULL,

    -- Content
    body TEXT NOT NULL,

    -- State & delivery
    state ENUM('sent', 'delivered', 'read', 'failed') DEFAULT 'sent',

    -- Metadata
    reply_to_message_id UUID NULLABLE,

    -- Audit
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULLABLE,

    -- Indexes
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (people_id) REFERENCES people(id) ON DELETE CASCADE,
    FOREIGN KEY (reply_to_message_id) REFERENCES messages(id) ON DELETE SET NULL,
    INDEX (conversation_id, created_at DESC),
    INDEX (organization_id, created_at DESC),
    INDEX (people_id, created_at DESC),
    INDEX (state, created_at)
);
```

---

## 3. Eloquent Models

### 3.1 Conversation Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'created_by' => 'string',
        'is_active' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    // Boot
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
        return $query->where('is_active', true)->whereNull('archived_at');
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
```

### 3.2 ConversationParticipant Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ConversationParticipant extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'id' => 'string',
        'conversation_id' => 'string',
        'organization_id' => 'string',
        'people_id' => 'string',
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
```

### 3.3 Message Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'id' => 'string',
        'conversation_id' => 'string',
        'organization_id' => 'string',
        'people_id' => 'string',
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
```

---

## 4. Form Requests (Validation)

### 4.1 StoreConversationRequest

```php
<?php

namespace App\Http\Requests\Messaging;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'participant_ids' => ['required', 'array', 'min:1'],
            'participant_ids.*' => ['uuid', 'exists:people,id'],
            'context_type' => ['nullable', 'in:none,learning_assignment,performance_review,incident,survey,onboarding'],
            'context_id' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título de la conversación es obligatorio.',
            'participant_ids.required' => 'Se requiere al menos un participante.',
            'participant_ids.*.exists' => 'Uno o más participantes no existen.',
        ];
    }
}
```

### 4.2 StoreMessageRequest

```php
<?php

namespace App\Http\Requests\Messaging;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
            'reply_to_message_id' => ['nullable', 'uuid', 'exists:messages,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'El cuerpo del mensaje es obligatorio.',
            'body.max' => 'El mensaje no puede exceder 5000 caracteres.',
            'reply_to_message_id.exists' => 'El mensaje de respuesta no existe.',
        ];
    }
}
```

---

## 5. API Endpoints

### 5.1 Conversations

#### GET `/api/messaging/conversations`

**Purpose:** List all conversations for authenticated user  
**Query Params:**

- `page`: int (default 1)
- `per_page`: int (default 15)
- `include_archived`: boolean (default false)
- `sort`: 'recent' | 'oldest' (default 'recent')

**Response:**

```json
{
  "data": [
    {
      "id": "uuid",
      "title": "Sprint Planning Discussion",
      "participant_count": 5,
      "unread_count": 2,
      "last_message_at": "2026-03-25T14:30:00Z",
      "context_type": "none",
      "is_active": true
    }
  ],
  "links": {...},
  "meta": {"total": 42, "per_page": 15}
}
```

#### POST `/api/messaging/conversations`

**Purpose:** Create new conversation  
**Body:**

```json
{
    "title": "Q2 Planning",
    "participant_ids": ["uuid1", "uuid2"],
    "context_type": "none",
    "context_id": null
}
```

**Response:** 201 Created with full Conversation object

#### GET `/api/messaging/conversations/{id}`

**Purpose:** Get conversation with latest messages  
**Query Params:**

- `include_messages`: boolean (default true)
- `message_limit`: int (default 50)

**Response:** Full Conversation with participants and messages

---

### 5.2 Messages

#### GET `/api/messaging/conversations/{conversationId}/messages`

**Purpose:** Get messages in conversation with pagination  
**Query Params:**

- `page`: int
- `per_page`: int (default 30)
- `sort`: 'asc' | 'desc' (default 'desc')

**Response:**

```json
{
  "data": [
    {
      "id": "uuid",
      "body": "Hello team!",
      "state": "read",
      "sender": {"id": "uuid", "name": "Omar"},
      "created_at": "2026-03-25T14:00:00Z",
      "reply_to_message_id": null
    }
  ],
  "pagination": {...}
}
```

#### POST `/api/messaging/conversations/{conversationId}/messages`

**Purpose:** Send message to conversation  
**Body:**

```json
{
    "body": "This is my message",
    "reply_to_message_id": null
}
```

**Response:** 201 Created with Message object

---

### 5.3 Message State

#### POST `/api/messaging/messages/{messageId}/read`

**Purpose:** Mark message as read  
**Body:** (empty)

**Response:** 200 OK

---

## 6. Service Layer

### 6.1 ConversationService

```php
<?php

namespace App\Services\Messaging;

use App\Models\*;

class ConversationService
{
    public function createConversation(
        string $organizationId,
        string $createdBy,
        array $data
    ): Conversation {
        // Validate all participants in org
        // Create conversation
        // Add all participants
        // Return with participants eager loaded
    }

    public function getConversationWithMessages(
        string $conversationId,
        string $organizationId,
        ?int $messageLimit = 50
    ): Conversation {
        // Verify access
        // Load with messages
        // Mark as read for current user
    }

    public function archiveConversation(
        string $conversationId,
        string $organizationId
    ): void {
        // Soft delete
        // Update is_active
    }

    public function addParticipant(
        string $conversationId,
        string $organizationId,
        string $peopleId
    ): ConversationParticipant {
        // Verify not already participant
        // Add with default permissions
    }

    public function removeParticipant(
        string $conversationId,
        string $organizationId,
        string $peopleId
    ): void {
        // Set left_at timestamp
    }
}
```

### 6.2 MessagingService

```php
<?php

namespace App\Services\Messaging;

class MessagingService
{
    public function sendMessage(
        string $conversationId,
        string $organizationId,
        string $senderPeopleId,
        string $body,
        ?string $replyToMessageId = null
    ): Message {
        // Verify sender is active participant
        // Create message with SENT state
        // Update conversation.last_message_at
        // Broadcast event (optional Phase 2)
        // Increment unread count for other participants
    }

    public function markAsRead(
        string $messageId,
        string $organizationId
    ): void {
        // Update message.state = READ
        // Update participant.unread_count
    }

    public function getUnreadCount(
        string $conversationId,
        string $organizationId,
        string $peopleId
    ): int {
        // Query participant unread_count
    }
}
```

---

## 7. Policies (Authorization)

### ConversationPolicy

```php
<?php

namespace App\Policies;

use App\Models\{User, Conversation};

class ConversationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->organization_id !== null;
    }

    public function view(User $user, Conversation $conversation): bool
    {
        return $user->organization_id === $conversation->organization_id
            && $conversation->isParticipant($user->people_id);
    }

    public function create(User $user): bool
    {
        return $user->organization_id !== null;
    }

    public function sendMessage(User $user, Conversation $conversation): bool
    {
        $participant = $conversation->participants()
            ->where('people_id', $user->people_id)
            ->first();

        return $participant?->canSendMessages() ?? false;
    }

    public function update(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->created_by;
    }

    public function delete(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->created_by;
    }
}
```

---

## 8. Routes (API)

```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('api/messaging')->group(function () {
        // Conversations
        Route::get('conversations', [ConversationController::class, 'index']);
        Route::post('conversations', [ConversationController::class, 'store']);
        Route::get('conversations/{conversation}', [ConversationController::class, 'show']);
        Route::put('conversations/{conversation}', [ConversationController::class, 'update']);
        Route::delete('conversations/{conversation}', [ConversationController::class, 'destroy']);

        // Participants
        Route::post('conversations/{conversation}/participants', [ParticipantController::class, 'store']);
        Route::delete('conversations/{conversation}/participants/{participant}', [ParticipantController::class, 'destroy']);

        // Messages
        Route::get('conversations/{conversation}/messages', [MessageController::class, 'index']);
        Route::post('conversations/{conversation}/messages', [MessageController::class, 'store']);

        // Message state
        Route::post('messages/{message}/read', [MessageController::class, 'markRead']);

        // Settings (Admin)
        Route::get('settings', [SettingsController::class, 'show']);
        Route::put('settings', [SettingsController::class, 'update']);
    });
});
```

---

## 9. Tests (Pest)

### 9.1 Feature Tests

#### `tests/Feature/Messaging/ConversationApiTest.php`

```php
<?php

it('lists conversations for authenticated user', function () {
    $user = User::factory()->create();
    $org = Organization::factory()
        ->has(User::factory()->count(1), 'users')
        ->create();
    $user->organization_id = $org->id;
    $user->save();

    $conversation = Conversation::factory()
        ->for($org)
        ->for($user, 'creator')
        ->create();

    ConversationParticipant::factory()
        ->for($conversation)
        ->for($org)
        ->for($user->people, 'people')
        ->create();

    $response = $this->actingAs($user)
        ->get('/api/messaging/conversations');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $conversation->id);
});

it('prevents access to conversations from other organizations', function () {
    $user1 = User::factory()->for($org1 = Organization::factory()->create())->create();
    $org2 = Organization::factory()->create();
    $conversation = Conversation::factory()->for($org2)->create();

    $response = $this->actingAs($user1)
        ->get("/api/messaging/conversations/{$conversation->id}");

    $response->assertForbidden();
});

it('creates conversation with participants', function () {
    $user = User::factory()->for(Organization::factory()->create())->create();
    $participant = People::factory()->for($user->organization)->create();

    $response = $this->actingAs($user)
        ->post('/api/messaging/conversations', [
            'title' => 'New Discussion',
            'participant_ids' => [$participant->id],
            'context_type' => 'none',
        ]);

    $response->assertCreated()
        ->assertJsonPath('title', 'New Discussion');

    expect(Conversation::count())->toBe(1);
    expect(ConversationParticipant::count())->toBe(2); // user + participant
});
```

#### `tests/Feature/Messaging/MessageApiTest.php`

```php
<?php

it('sends message to conversation', function () {
    $user = User::factory()->for(Organization::factory()->create())->create();
    $conversation = Conversation::factory()
        ->for($user->organization)
        ->create();

    ConversationParticipant::factory()
        ->for($conversation)
        ->for($user->organization)
        ->for($user->people)
        ->create();

    $response = $this->actingAs($user)
        ->post("/api/messaging/conversations/{$conversation->id}/messages", [
            'body' => 'Hello team!',
        ]);

    $response->assertCreated()
        ->assertJsonPath('state', 'sent');

    expect(Message::count())->toBe(1);
});

it('marks message as read', function () {
    $user = User::factory()->for(Organization::factory()->create())->create();
    $message = Message::factory()->for($user->organization)->create();

    $response = $this->actingAs($user)
        ->post("/api/messaging/messages/{$message->id}/read");

    $response->assertSuccessful();
    expect($message->fresh()->state)->toBe(MessageState::READ);
});

it('prevents sending message if not active participant', function () {
    $user = User::factory()->for(Organization::factory()->create())->create();
    $conversation = Conversation::factory()->for($user->organization)->create();

    $response = $this->actingAs($user)
        ->post("/api/messaging/conversations/{$conversation->id}/messages", [
            'body' => 'Hello!',
        ]);

    $response->assertForbidden();
});
```

### 9.2 Unit Tests

#### `tests/Unit/Messaging/ConversationTest.php`

```php
<?php

it('determines if user is participant', function () {
    $org = Organization::factory()->create();
    $people = People::factory()->for($org)->create();
    $conversation = Conversation::factory()->for($org)->create();

    ConversationParticipant::factory()
        ->for($conversation)
        ->for($org)
        ->for($people)
        ->create();

    expect($conversation->isParticipant($people->id))->toBeTrue();
    expect($conversation->isParticipant(People::factory()->for($org)->create()->id))->toBeFalse();
});

it('adds participant successfully', function () {
    $org = Organization::factory()->create();
    $people = People::factory()->for($org)->create();
    $conversation = Conversation::factory()->for($org)->create();

    $participant = $conversation->addParticipant($people->id, true, true);

    expect($participant)->toBeInstanceOf(ConversationParticipant::class);
    expect($participant->can_send)->toBeTrue();
    expect($participant->can_read)->toBeTrue();
});

it('marks conversation as read', function () {
    $org = Organization::factory()->create();
    $people = People::factory()->for($org)->create();
    $conversation = Conversation::factory()->for($org)->create();

    $participant = ConversationParticipant::factory()
        ->for($conversation)
        ->for($org)
        ->for($people)
        ->state(['unread_count' => 5])
        ->create();

    $conversation->markAsRead($people->id);
    $participant->refresh();

    expect($participant->unread_count)->toBe(0);
});
```

---

## 10. Implementation Timeline (Phase 1)

| Day                     | Tasks                                       | Result           |
| :---------------------- | :------------------------------------------ | :--------------- |
| **Day 1-2 (Mar 25-26)** | ✅ Models + Migrations + Factories          | DB schema ready  |
| **Day 3-4 (Mar 27-28)** | 🔲 Services + Form Requests                 | Business logic   |
| **Day 5-6 (Mar 29-30)** | 🔲 Controllers + Routes + Base tests        | API working      |
| **Day 7 (Mar 31)**      | 🔲 Polish + Auth policies + Edge case tests | MVP ready for PR |

---

## 11. Success Criteria

- ✅ All 3 models with relationships working
- ✅ Multi-tenant isolation enforced (all queries scoped by org_id)
- ✅ 12+ Feature tests (conversations CRUD, messages CRUD, permissions)
- ✅ 6+ Unit tests (helpers, scopes, validations)
- ✅ Authorization policies preventing cross-org/cross-participant access
- ✅ Audit trail for all writes (via timestamps + creator tracking)
- ✅ Documentation with API examples

---

## 12. Dependencies & Assumptions

- ✅ Laravel 12 with Sanctum auth ready
- ✅ Organization + People models existing
- ✅ Multi-tenant middleware operational
- ✅ UUID primary keys pattern established
- ✅ Pest v4 and factories patterns known
- ✅ Form Request validation pattern established

**End of Messaging MVP Specification**
