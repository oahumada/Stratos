<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonMovement extends Model
{
    use HasFactory;

    protected $table = 'person_movements';

    protected $fillable = [
        'person_id',
        'organization_id',
        'type',
        'from_department_id',
        'to_department_id',
        'from_role_id',
        'to_role_id',
        'movement_date',
        'change_set_id',
        'metadata',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'metadata' => 'array',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'person_id');
    }

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Departments::class, 'from_department_id');
    }

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Departments::class, 'to_department_id');
    }

    public function fromRole(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'from_role_id');
    }

    public function toRole(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'to_role_id');
    }

    public function changeSet(): BelongsTo
    {
        return $this->belongsTo(ChangeSet::class);
    }
}
