<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use BelongsToOrganization;
    use HasFactory;

    protected $fillable = [
        'name',
        'role_description',
        'persona', // description of behavior
        'type', // system, support, analyst
        'provider',
        'model',
        'is_active',
        'expertise_areas',
        'capabilities_config',
        'organization_id',
    ];

    protected $casts = [
        'expertise_areas' => 'array',
        'capabilities_config' => 'json',
        'is_active' => 'boolean',
    ];

    // Relación con los roles que este agente puede potenciar
    public function supportedRoles()
    {
        return $this->belongsToMany(Roles::class, 'role_ai_leverage');
    }
}
