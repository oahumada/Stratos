<?php

namespace App\Console\Commands;

use App\Models\BusinessMetric;
use App\Models\Departments;
use App\Models\Organization;
use App\Models\People;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StratosDataIntegrityCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stratos:check-integrity {--org= : Organization ID to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a sanity check on Stratos data structures before/after mass ingestion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orgId = $this->option('org') ?? Organization::first()?->id;

        if (! $orgId) {
            $this->error('No organization specified and none found in DB.');

            return 1;
        }

        $this->info("=== Stratos Data Integrity Check: Organization #{$orgId} ===");

        $this->checkOrphanedPeople($orgId);
        $this->checkFinancialGaps($orgId);
        $this->checkSkillGaps($orgId);
        $this->checkGravitationalNodes($orgId);

        $this->info('=== End of Check ===');

        return 0;
    }

    protected function checkOrphanedPeople($orgId)
    {
        $orphaned = People::where('organization_id', $orgId)
            ->where(function ($q) {
                $q->whereNull('department_id')
                    ->orWhereNull('role_id');
            })->count();

        if ($orphaned > 0) {
            $this->warn("⚠️ Found {$orphaned} people without Department or Role assigned. These will not contribute to HCVA accurately.");
        } else {
            $this->info('✅ All people are correctly linked to Departments and Roles.');
        }
    }

    protected function checkFinancialGaps($orgId)
    {
        $requiredMetrics = ['revenue', 'opex', 'payroll_cost', 'headcount'];
        $missing = [];

        foreach ($requiredMetrics as $metric) {
            $exists = BusinessMetric::where('organization_id', $orgId)
                ->where('metric_name', $metric)
                ->exists();

            if (! $exists) {
                $missing[] = $metric;
            }
        }

        if (count($missing) > 0) {
            $this->error('❌ Missing critical financial metrics: '.implode(', ', $missing).'. Impact Engine will use fallback/zero values.');
        } else {
            $this->info('✅ Found all core financial metrics needed for HCVA calculation.');
        }
    }

    protected function checkSkillGaps($orgId)
    {
        $peopleWithoutSkills = People::where('organization_id', $orgId)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('people_role_skills')
                    ->whereColumn('people_role_skills.people_id', 'people.id');
            })->count();

        if ($peopleWithoutSkills > 0) {
            $this->warn("⚠️ Found {$peopleWithoutSkills} people without skill assessments. Competency Mesh analysis will be incomplete.");
        } else {
            $this->info('✅ All people have at least one skill assessment recorded.');
        }
    }

    protected function checkGravitationalNodes($orgId)
    {
        $emptyNodes = Departments::where('organization_id', $orgId)
            ->where(function ($q) {
                $q->whereNull('aliases')
                    ->orWhere(DB::raw('CAST(aliases AS TEXT)'), '[]')
                    ->orWhere(DB::raw('CAST(aliases AS TEXT)'), '');
            })->count();

        if ($emptyNodes > 0) {
            $this->line("ℹ️ Note: {$emptyNodes} departments have no aliases defined. Future ERP uploads using different names will fail to match these nodes.");
        }
    }
}
