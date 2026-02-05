<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleVersion extends Model
{
    use HasFactory;

    protected $table = 'role_versions';

    protected $fillable = [
        'organization_id', 'role_id', 'version_group_id', 'name', 'description', 'effective_from', 'evolution_state', 'metadata', 'created_by'
    ];

    protected $casts = [
        'metadata' => 'array',
        'effective_from' => 'date',
    ];

    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }
}
