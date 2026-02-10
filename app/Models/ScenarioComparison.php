<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScenarioComparison extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'scenario_ids',
        'comparison_criteria',
        'comparison_results',
        'created_by',
    ];

    protected $casts = [
        'scenario_ids' => 'array',
        'comparison_criteria' => 'array',
        'comparison_results' => 'array',
    ];

    // Relaciones
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Método para obtener escenarios comparados
    public function getScenarios()
    {
        return Scenario::whereIn('id', $this->scenario_ids ?? [])->get();
    }

    // Scope multi-tenant
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }
}
