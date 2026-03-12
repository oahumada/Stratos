<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\BelongsToOrganization;
use App\Traits\HasDigitalSeal;

class AssessmentRequest extends Model
{
    use HasFactory, BelongsToOrganization, HasDigitalSeal;

    protected $fillable = [
        'organization_id',
        'assessment_cycle_id',
        'evaluator_id',
        'subject_id',
        'relationship',
        'status',
        'token',
        'completed_at',
        'digital_signature', 'signed_at', 'signature_version',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(AssessmentCycle::class, 'assessment_cycle_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(People::class, 'evaluator_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(People::class, 'subject_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(AssessmentFeedback::class);
    }
}
