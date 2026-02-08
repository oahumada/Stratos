<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationUseCase extends Model
{
    use HasFactory;

    protected $table = 'organization_use_cases';

    protected $fillable = [
        'organization_id',
        'use_case_template_id',
        'is_active',
        'custom_config',
        'activated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'custom_config' => 'array',
        'activated_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class, 'organization_id');
    }

    public function template(): BelongsTo
    {
        // Reutilizamos ScenarioTemplate como catálogo de use cases
        return $this->belongsTo(ScenarioTemplate::class, 'use_case_template_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }
}
