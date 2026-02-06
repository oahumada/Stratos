<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\WorkforcePlan;
use App\Policies\WorkforcePlanPolicy;
use App\Models\CompetencyVersion;
use App\Policies\CompetencyVersionPolicy;
use App\Models\ChangeSet;
use App\Policies\ChangeSetPolicy;

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
