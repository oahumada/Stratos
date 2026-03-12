<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasDigitalSeal;

class PsychometricProfile extends Model
{
    use HasFactory, HasDigitalSeal;

    protected $fillable = [
        'people_id',
        'assessment_session_id',
        'trait_name',
        'score',
        'rationale',
        'evidence',
        'metadata',
        'digital_signature', 'signed_at', 'signature_version',
    ];

    protected $casts = [
        'score' => 'float',
        'metadata' => 'array',
        'signed_at' => 'datetime',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    /**
     * Alias for person() — used by CultureSentinelService.
     */
    public function people(): BelongsTo
    {
        return $this->person();
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AssessmentSession::class, 'assessment_session_id');
    }
}
