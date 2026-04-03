<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organizations extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'subdomain', 'industry', 'size', 'active_modules', 'workforce_thresholds'];

    protected $casts = [
        'size' => 'string',
        'active_modules' => 'array',
        'workforce_thresholds' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Roles::class);
    }

    public function People(): HasMany
    {
        return $this->hasMany(People::class, 'organization_id');
    }

    public function developmentPaths(): HasMany
    {
        return $this->hasMany(DevelopmentPath::class);
    }

    public function jobOpenings(): HasMany
    {
        return $this->hasMany(JobOpening::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Departments::class, 'organization_id');
    }

    public function culturalBlueprint(): HasOne
    {
        return $this->hasOne(CulturalBlueprint::class, 'organization_id');
    }

    /**
     * Alerts and Notifications (Phase 6)
     */
    public function smartAlerts(): HasMany
    {
        return $this->hasMany(SmartAlert::class, 'organization_id');
    }

    /**
     * Check if the organization has access to a specific module.
     * Always returns true for 'core'.
     */
    public function hasModule(string $module): bool
    {
        if ($module === 'core') {
            return true;
        }

        $active_modules = is_array($this->active_modules) ? $this->active_modules : [];

        return in_array($module, $active_modules);
    }

    /**
     * Snapshots for Stratos Radar (Event Sourcing)
     */
    public function snapshots(): HasMany
    {
        return $this->hasMany(OrganizationSnapshot::class, 'organizations_id');
    }

    /**
     * Impact Engine (LAMP Framework) Relationships
     */
    public function businessMetrics(): HasMany
    {
        return $this->hasMany(BusinessMetric::class, 'organization_id');
    }

    public function impactAnalyses(): HasMany
    {
        return $this->hasMany(ImpactAnalysis::class, 'organization_id');
    }

    public function financialIndicators(): HasMany
    {
        return $this->hasMany(FinancialIndicator::class, 'organization_id');
    }
}
