<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro de auditoría de seguridad y acceso.
 *
 * Tracked events: login, logout, login_failed, mfa_challenged, mfa_failed
 */
class SecurityAccessLog extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'organization_id',
        'event',
        'email',
        'ip_address',
        'user_agent',
        'role',
        'mfa_used',
        'metadata',
        'occurred_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'mfa_used' => 'boolean',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    // ─────────────────────────────────────────────────────── Relationships ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─────────────────────────────────────────────────────── Scopes ─────────

    /** @param \Illuminate\Database\Eloquent\Builder<SecurityAccessLog> $query */
    public function scopeForOrganization(\Illuminate\Database\Eloquent\Builder $query, int $orgId): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('organization_id', $orgId);
    }

    /** @param \Illuminate\Database\Eloquent\Builder<SecurityAccessLog> $query */
    public function scopeEvent(\Illuminate\Database\Eloquent\Builder $query, string $event): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('event', $event);
    }

    // ─────────────────────────────────────────────────────── Helpers ─────────

    public static function record(string $event, array $attributes = []): static
    {
        return static::create(array_merge(['event' => $event, 'occurred_at' => now()], $attributes));
    }
}
