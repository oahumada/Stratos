<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RedactionAuditTrail extends Model
{
    protected $fillable = [
        'redaction_types',
        'count',
        'original_hash',
        'context',
        'user_id',
        'organization_id',
    ];

    protected $casts = [
        'redaction_types' => 'array',
        'count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who triggered the redaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the organization
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
