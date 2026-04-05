<?php

namespace App\Console\Commands;

use App\Jobs\ProcessImprovementSignals;
use App\Models\Organization;
use Illuminate\Console\Command;

class ProcessFeedbackLoopCommand extends Command
{
    protected $signature = 'intelligence:process-feedback {--org= : Organization ID} {--all : Process all orgs}';

    protected $description = 'Dispatch ProcessImprovementSignals jobs for organizations';

    public function handle(): int
    {
        if ($this->option('all')) {
            $orgs = Organization::pluck('id');

            foreach ($orgs as $orgId) {
                ProcessImprovementSignals::dispatch($orgId);
                $this->info("Dispatched ProcessImprovementSignals for org #{$orgId}");
            }

            $this->info("Dispatched jobs for {$orgs->count()} organizations.");

            return self::SUCCESS;
        }

        $orgId = $this->option('org');
        if (! $orgId) {
            $this->error('Please specify --org=<id> or --all');

            return self::FAILURE;
        }

        ProcessImprovementSignals::dispatch((int) $orgId);
        $this->info("Dispatched ProcessImprovementSignals for org #{$orgId}");

        return self::SUCCESS;
    }
}
