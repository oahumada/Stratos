<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsComplianceRecord extends Model
{
    protected $fillable = [
        'lms_enrollment_id',
        'lms_course_id',
        'user_id',
        'organization_id',
        'due_date',
        'completed_date',
        'status',
        'recertification_due_date',
        'escalation_level',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_date' => 'date',
            'recertification_due_date' => 'date',
            'escalation_level' => 'integer',
        ];
    }

    // ── Relations ──

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(LmsEnrollment::class, 'lms_enrollment_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'lms_course_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    // ── Scopes ──

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', Carbon::today())
            ->where('status', '!=', 'completed');
    }

    public function scopeUpcoming(Builder $query, int $days = 30): Builder
    {
        return $query->where('status', 'pending')
            ->whereBetween('due_date', [Carbon::today(), Carbon::today()->addDays($days)]);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeForOrganization(Builder $query, int $orgId): Builder
    {
        return $query->where('lms_compliance_records.organization_id', $orgId);
    }

    // ── Methods ──

    public function isOverdue(): bool
    {
        return $this->status !== 'completed' && $this->due_date->lt(Carbon::today());
    }

    public function daysUntilDue(): int
    {
        return (int) Carbon::today()->diffInDays($this->due_date, false);
    }

    public function markCompleted(): void
    {
        $this->completed_date = Carbon::today();
        $this->status = 'completed';

        $course = $this->course;
        if ($course && $course->recertification_interval_months) {
            $this->recertification_due_date = Carbon::today()
                ->addMonths($course->recertification_interval_months);
        }

        $this->save();
    }
}
