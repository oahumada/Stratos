# Multi-Channel Notifications System

## Overview

Users can configure multiple notification channels (Slack, Telegram, Email) at the user level. Notifications route automatically to all enabled channels for each user.

## Architecture

### Core Components

**NotificationChannelInterface** (`app/Services/Notifications/Contracts/`)
- Contract all channels must implement
- Methods: `send()`, `getChannelType()`, `validateConfig()`

**NotificationDispatcher** (`app/Services/Notifications/NotificationDispatcher.php`)
- Central service that routes notifications
- `dispatchToUser($user, $title, $message, $context)`: Send to user's enabled channels
- `dispatchToOrganization($orgId, $title, $message, $context)`: Broadcast to org-level enabled channels
- Supports custom channel registration via `registerChannel()`

**Channel Implementations**

1. **SlackNotificationChannel**
   - Requires webhook URL
   - Sends formatted message blocks
   - Config: `{ webhook_url: "https://hooks.slack.com/..." }`

2. **TelegramNotificationChannel**
   - Requires bot token + chat_id
   - Sends markdown-formatted messages
   - Config: `{ bot_token: "...", chat_id: "..." }`

3. **EmailNotificationChannel**
   - Uses Laravel Mail facade
   - Config: `{ email: "user@example.com" }`

### Database

**user_notification_channels**
```sql
id | user_id | organization_id | channel_type | channel_config | is_active | created_at | updated_at
```
- Stores user's channel preferences
- `channel_config`: JSON with channel-specific settings
- Unique constraint: (user_id, channel_type)

**notification_channel_settings**
```sql
id | organization_id | channel_type | is_enabled | global_config | created_at | updated_at
```
- Organization-level channel availability
- `is_enabled`: whether this channel is available for users in this org
- `global_config`: shared settings (e.g., org Slack webhook)

## Usage

### User Sets Preferences (API)

#### Rate Limiting (aplica a estos endpoints)

Los endpoints de `/api/notification-preferences` pasan por el middleware global
`ApiRateLimiter` y siempre devuelven headers de límite:

- `X-RateLimit-Limit`
- `X-RateLimit-Remaining`
- `X-RateLimit-Reset` (unix timestamp)

Límites actuales:

- **Public route:** 30 req/min
- **Guest (no auth):** 60 req/min
- **Authenticated:** 300 req/min

Si se excede el límite, la API responde `429`:

```json
{
  "message": "Too many requests. Please try again later.",
  "retry_after": 42
}
```

**Get current preferences**
```bash
GET /api/notification-preferences
```
Response:
```json
{
  "preferences": [
    { "channel_type": "email", "is_active": true, "created_at": "..." },
    { "channel_type": "telegram", "is_active": true, "created_at": "..." }
  ],
  "available_channels": ["slack", "telegram", "email"]
}
```

**Add or update channel**
```bash
POST /api/notification-preferences
Content-Type: application/json

{
  "channel_type": "telegram",
  "channel_config": {
    "bot_token": "123:ABC...",
    "chat_id": "987654321"
  },
  "is_active": true
}
```

`channel_config` es obligatorio y depende del canal:

- `email`: `{ "address": "user@example.com" }`
- `telegram`: `{ "bot_token": "...", "chat_id": "..." }`
- `slack`: `{ "webhook_url": "https://hooks.slack.com/..." }`

**Toggle channel on/off**
```bash
POST /api/notification-preferences/telegram/toggle
```

**Remove channel**
```bash
DELETE /api/notification-preferences/slack
```

### Backend Service Usage

```php
use App\Services\Notifications\NotificationDispatcher;

class YourService {
    public function __construct(protected NotificationDispatcher $dispatcher) {}
    
    public function notifyUser($user) {
        $results = $this->dispatcher->dispatchToUser(
            user: $user,
            title: "Action Completed",
            message: "Your training course has been completed!",
            context: ['course_id' => 123]
        );
        
        // $results = ['email' => true, 'telegram' => true, 'slack' => false]
    }
}
```

### Broadcast to Organization

```php
$this->dispatcher->dispatchToOrganization(
    organizationId: $org->id,
    title: "System Maintenance",
    message: "Scheduled maintenance tonight at 2 AM UTC",
    context: ['severity' => 'high']
);
```

### Integration: LMS Course Completion

When course is completed:
```php
$lmsService->notifyCourseCompletion($enrollment);
```

This sends to user via their configured channels:
- Default: Email (if no preferences set)
- Configured: Slack + Telegram + Email (as per user preferences)

## Configuration

### Environment Variables

```env
# For org-wide defaults (optional)
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/...
TELEGRAM_BOT_TOKEN=123:ABC...
```

### Per-User Setup

Users configure channels via API `/api/notification-preferences`.

Admin can seed initial settings via:
```php
use App\Models\NotificationChannelSetting;

NotificationChannelSetting::create([
    'organization_id' => $org->id,
    'channel_type' => 'slack',
    'is_enabled' => true,
    'global_config' => ['webhook_url' => env('SLACK_WEBHOOK_URL')],
]);
```

## Extending: Add New Channel

1. **Create channel class**
```php
namespace App\Services\Notifications\Channels;

use App\Services\Notifications\Contracts\NotificationChannelInterface;

class WhatsAppNotificationChannel implements NotificationChannelInterface {
    public function send(string $title, string $message, array $data = []): bool {
        // Implementation
    }
    
    public function getChannelType(): string {
        return 'whatsapp';
    }
    
    public function validateConfig(array $config): array {
        return []; // Return errors or empty if valid
    }
}
```

2. **Register in dispatcher** (e.g., in AppServiceProvider)
```php
app(NotificationDispatcher::class)->registerChannel(
    'whatsapp',
    new WhatsAppNotificationChannel()
);
```

3. **Update validation** in `NotificationPreferencesController`:
```php
'channel_type' => 'required|string|in:slack,telegram,email,whatsapp',
```

4. **Add tests** and run migration if DB config needed.

## Error Handling

All channel implementations catch exceptions and return `false` on failure. Errors are logged with `Log::error()`.

If a channel fails:
- Other channels still execute
- Failure doesn't block notification dispatch
- Error logged for debugging

Example:
```php
$results = $dispatcher->dispatchToUser($user, 'Test', 'Test message');
// ['email' => true, 'telegram' => false] // telegram API error, but email sent
```

## Security Considerations

- Channel credentials stored in encrypted JSON columns (Laravel Casts)
- User can only view/modify their own preferences
- Organization admin can enable/disable channels
- Multi-tenancy: all queries enforce organization_id scoping

## Testing

```bash
php artisan test tests/Feature/NotificationMultiChannelTest.php
```

Coverage:
- User CRUD operations on channels
- Dispatcher logic (multi-channel send)
- Model relationships
- Default-to-email fallback

## Future Enhancements

- **SMS**: Twilio integration
- **Push Notifications**: Browser/mobile push
- **WhatsApp**: WhatsApp Business API
- **Discord**: Discord webhook integration
- **Scheduled Notifications**: Delay delivery (timezone-aware)
- **Notification Preferences UI**: Vue component for user preferences
- **Org Admin Panel**: Channel management interface
