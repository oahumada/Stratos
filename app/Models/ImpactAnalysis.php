<?php

namespace App\Models;

use App\Traits\HasDigitalSeal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImpactAnalysis extends Model
{
    use HasDigitalSeal;

    protected $fillable = [
        'organization_id',
        'type',
        'target_engine',
        'correlations',
        'logic_narrative',
        'insight_summary',
        'recommendations',
        'digital_signature',
        'signed_at',
        'signature_version'
    ];

    protected $casts = [
        'correlations' => 'array',
        'recommendations' => 'array',
        'signed_at' => 'datetime'
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class, 'organization_id');
    }
}
