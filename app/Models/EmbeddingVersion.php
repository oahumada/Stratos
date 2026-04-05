<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmbeddingVersion extends Model
{
    /** @use HasFactory<\Database\Factories\EmbeddingVersionFactory> */
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'version_tag',
        'snapshot_count',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
