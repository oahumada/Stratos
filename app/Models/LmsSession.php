<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsSession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'course_id',
        'instructor_id',
        'title',
        'description',
        'session_type',
        'location',
        'meeting_url',
        'starts_at',
        'ends_at',
        'timezone',
        'max_attendees',
        'is_recording_available',
        'recording_url',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'max_attendees' => 'integer',
            'is_recording_available' => 'boolean',
        ];
    }

    // ── Relations ──

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(LmsSessionAttendance::class, 'session_id');
    }

    public function attendees(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            LmsSessionAttendance::class,
            'session_id',
            'id',
            'id',
            'user_id',
        );
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    // ── Scopes ──

    public function scopeForOrganization(Builder $query, int $orgId): Builder
    {
        return $query->where('lms_sessions.organization_id', $orgId);
    }

    // ── Helpers ──

    public function isFull(): bool
    {
        if ($this->max_attendees === null) {
            return false;
        }

        return $this->attendances()->whereNotIn('status', ['cancelled'])->count() >= $this->max_attendees;
    }

    public function isUpcoming(): bool
    {
        return $this->starts_at->isFuture();
    }

    public function isPast(): bool
    {
        return $this->ends_at->isPast();
    }
}
