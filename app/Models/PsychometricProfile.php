<?php

namespace App\Models;

use App\Traits\HasDigitalSeal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class PsychometricProfile extends Model
{
    use HasDigitalSeal, HasFactory;

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

    public function setRationaleAttribute(?string $value): void
    {
        $this->attributes['rationale'] = $this->encryptAtRest($value);
    }

    public function getRationaleAttribute(?string $value): ?string
    {
        return $this->decryptAtRest($value);
    }

    public function setEvidenceAttribute(?string $value): void
    {
        $this->attributes['evidence'] = $this->encryptAtRest($value);
    }

    public function getEvidenceAttribute(?string $value): ?string
    {
        return $this->decryptAtRest($value);
    }

    private function encryptAtRest(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return Crypt::encryptString($value);
    }

    private function decryptAtRest(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return $value;
        }
    }
}
