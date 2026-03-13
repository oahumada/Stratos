<?php

namespace App\Models;

use App\Traits\HasDigitalSeal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CulturalBlueprint extends Model
{
    use HasFactory, HasDigitalSeal;

    protected $fillable = [
        'organization_id',
        'mission',
        'vision',
        'values',
        'principles',
        'digital_signature',
        'signed_at',
        'signature_version'
    ];

    protected $casts = [
        'values' => 'json',
        'principles' => 'json',
        'signed_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class, 'organization_id');
    }
}
