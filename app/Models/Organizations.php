<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organizations extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'subdomain', 'industry', 'size', 'active_modules'];

    protected $casts = [
        'size' => 'string',
        'active_modules' => 'array',
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
}
