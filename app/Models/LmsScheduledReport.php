<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsScheduledReport extends Model
{
    protected $fillable = [
        'organization_id',
        'created_by',
        'report_type',
        'filters',
        'frequency',
        'recipients',
        'last_sent_at',
        'next_send_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'recipients' => 'array',
            'last_sent_at' => 'datetime',
            'next_send_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // ── Relations ──

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ──

    public function scopeForOrganization(Builder $query, int $orgId): Builder
    {
        return $query->where('lms_scheduled_reports.organization_id', $orgId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('next_send_at', '<=', now());
    }
}
