<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventStore extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_store';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'event_name',
        'aggregate_type',
        'aggregate_id',
        'organization_id',
        'actor_id',
        'payload',
        'occurred_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];
}
