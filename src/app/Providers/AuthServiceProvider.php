<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\WorkforcePlan;
use App\Policies\WorkforcePlanPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        WorkforcePlan::class => WorkforcePlanPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
