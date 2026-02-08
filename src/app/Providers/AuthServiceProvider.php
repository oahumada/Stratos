<?php

namespace App\Providers;

use App\Models\ChangeSet;
use App\Models\CompetencyVersion;
use App\Models\WorkforcePlan;
use App\Policies\ChangeSetPolicy;
use App\Policies\CompetencyVersionPolicy;
use App\Policies\WorkforcePlanPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        WorkforcePlan::class => WorkforcePlanPolicy::class,
        CompetencyVersion::class => CompetencyVersionPolicy::class,
        ChangeSet::class => ChangeSetPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
