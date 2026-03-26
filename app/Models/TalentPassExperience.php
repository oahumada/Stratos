<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TalentPassExperience extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'talent_pass_id',
        'job_title',
        'company',
        'description',
        'start_date',
        'end_date',
        'is_current',
        'location',
        'employment_type',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Relationships
    public function talentPass(): BelongsTo
    {
        return $this->belongsTo(TalentPass::class);
    }

    // Methods
    public function getDurationInMonths(): int
    {
        $end = $this->is_current ? now() : $this->end_date;
        return $this->start_date->diffInMonths($end);
    }

    public function getDurationFormatted(): string
    {
        $months = $this->getDurationInMonths();
        $years = intdiv($months, 12);
        $remainingMonths = $months % 12;

        $parts = [];
        if ($years > 0) $parts[] = $years . ' year' . ($years > 1 ? 's' : '');
        if ($remainingMonths > 0) $parts[] = $remainingMonths . ' month' . ($remainingMonths > 1 ? 's' : '');

        return implode(' ', $parts) ?: '0 months';
    }
}
