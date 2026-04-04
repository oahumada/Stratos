<?php

namespace App\Console\Commands;

use App\Services\Lms\ComplianceTrackingService;
use Illuminate\Console\Command;

class CheckComplianceCommand extends Command
{
    protected $signature = 'lms:check-compliance {--org= : Specific organization ID}';

    protected $description = 'Check for overdue compliance records, process escalations, and handle recertifications';

    public function handle(ComplianceTrackingService $service): int
    {
        $orgIds = $this->option('org')
            ? [(int) $this->option('org')]
            : \App\Models\Organization::where('is_active', true)->pluck('id')->all();

        $totalOverdue = 0;
        $totalEscalations = ['reminders' => 0, 'urgent' => 0, 'escalated' => 0];
        $totalRecertifications = 0;

        foreach ($orgIds as $orgId) {
            $overdue = $service->checkOverdueRecords($orgId);
            $totalOverdue += $overdue;

            $escalations = $service->processEscalations($orgId);
            $totalEscalations['reminders'] += $escalations['reminders'];
            $totalEscalations['urgent'] += $escalations['urgent'];
            $totalEscalations['escalated'] += $escalations['escalated'];

            $recertifications = $service->processRecertifications($orgId);
            $totalRecertifications += $recertifications;
        }

        $this->info("Compliance check completed for ".count($orgIds)." organization(s).");
        $this->info("  Newly overdue: {$totalOverdue}");
        $this->info("  Reminders sent: {$totalEscalations['reminders']}");
        $this->info("  Urgent reminders: {$totalEscalations['urgent']}");
        $this->info("  Manager escalations: {$totalEscalations['escalated']}");
        $this->info("  Recertifications created: {$totalRecertifications}");

        return self::SUCCESS;
    }
}
