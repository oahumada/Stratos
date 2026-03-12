<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Builder;

use App\Traits\BelongsToOrganization;
use App\Traits\HasDigitalSeal;

class AssessmentSession extends Model
{
    use HasFactory, BelongsToOrganization, HasDigitalSeal;


    protected $fillable = [
        'organization_id',
        'people_id',
        'agent_id',
        'scenario_id',
        'type',
        'status',
        'metadata',
        'started_at',
        'completed_at',
        'digital_signature', 'signed_at', 'signature_version',
    ];

    protected $casts = [
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class, 'organization_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AssessmentMessage::class);
    }

    public function psychometricProfiles(): HasMany
    {
        return $this->hasMany(PsychometricProfile::class);
    }
}
