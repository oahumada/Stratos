<?php

namespace App\Models;

use App\Traits\HasDigitalSeal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasDigitalSeal;

    protected $fillable = [
        'job_opening_id',
        'people_id',
        'status',
        'message',
        'ai_analysis',
        'match_score',
        'applied_at',
        'digital_signature', 'signed_at', 'signature_version',
    ];

    protected $casts = [
        'status' => 'string',
        'ai_analysis' => 'array',
        'match_score' => 'integer',
        'applied_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope('people_org', function (Builder $builder) {
            if (auth()->check() && auth()->user()->organization_id) {
                $builder->whereHas('jobOpening', function (Builder $q) {
                    $q->where('organization_id', auth()->user()->organization_id);
                });
            }
        });
    }

    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }

    public function people(): BelongsTo
    {
        return $this->belongsTo(People::class);
    }
}
