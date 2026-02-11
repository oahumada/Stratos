<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model {
    protected $fillable = ['provider', 'model', 'is_active', 'expertise_areas', 'capabilities_config', 'organization_id', 'name'];

    protected $casts = [
        'expertise_areas' => 'array',
        'capabilities_config' => 'json',
    ];

    // Relación con los roles que este agente puede potenciar
    public function supportedRoles() {
        return $this->belongsToMany(Role::class, 'role_ai_leverage');
    }
}
