<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = [
        'job_opening_id',
        'person_id',
        'status',
        'message',
        'applied_at',
    ];

    protected $casts = [
        'status' => 'string',
        'applied_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope('person_org', function (Builder $builder) {
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

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
