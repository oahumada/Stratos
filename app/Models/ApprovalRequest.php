<?php

namespace App\Models;

use App\Traits\HasDigitalSeal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ApprovalRequest extends Model
{
    use HasFactory, HasDigitalSeal, SoftDeletes;

    protected $fillable = [
        'token',
        'approvable_type',
        'approvable_id',
        'approver_id',
        'status',
        'data',
        'digital_signature',
        'signed_at',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'signed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->token)) {
                $model->token = (string) Str::uuid();
            }
        });
    }

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function approver()
    {
        return $this->belongsTo(People::class, 'approver_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
