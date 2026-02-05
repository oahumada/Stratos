<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\WorkforcePlan;
use App\Policies\WorkforcePlanPolicy;
use App\Models\CompetencyVersion;
use App\Policies\CompetencyVersionPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        WorkforcePlan::class => WorkforcePlanPolicy::class,
        CompetencyVersion::class => CompetencyVersionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
