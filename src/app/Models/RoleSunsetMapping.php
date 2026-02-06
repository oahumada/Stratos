<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleSunsetMapping extends Model
{
    use HasFactory;

    protected $table = 'role_sunset_mappings';

    protected $fillable = [
        'organization_id',
        'scenario_id',
        'role_id',
        'mapped_role_id',
        'sunset_reason',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    public function mappedRole()
    {
        return $this->belongsTo(Roles::class, 'mapped_role_id');
    }
}
