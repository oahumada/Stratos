<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScenarioTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'scenario_type',
        'industry',
        'icon',
        'config',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
    ];

    // Relaciones
    public function scenarios()
    {
        return $this->hasMany(WorkforceScenario::class, 'template_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByIndustry($query, $industry)
    {
        return $query->where('industry', $industry)
            ->orWhere('industry', 'general');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('scenario_type', $type);
    }

    // Métodos auxiliares
    public function getPredefinedSkills()
    {
        return $this->config['predefined_skills'] ?? [];
    }

    public function getSuggestedStrategies()
    {
        return $this->config['suggested_strategies'] ?? [];
    }

    public function getKpis()
    {
        return $this->config['kpis'] ?? [];
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
