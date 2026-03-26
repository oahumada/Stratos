<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TalentPassCredential extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'talent_pass_id',
        'credential_name',
        'issuer',
        'issued_date',
        'expiry_date',
        'credential_url',
        'credential_id',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
            'expiry_date' => 'date',
            'is_featured' => 'boolean',
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
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return now()->isAfter($this->expiry_date);
    }

    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return now()->addMonth()->isAfter($this->expiry_date) && !$this->isExpired();
    }
}
