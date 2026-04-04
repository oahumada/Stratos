<?php

namespace App\Console\Commands;

use App\Services\Lms\ScheduledReportService;
use Illuminate\Console\Command;

class SendScheduledReportsCommand extends Command
{
    protected $signature = 'lms:send-scheduled-reports';

    protected $description = 'Process and send all due LMS scheduled reports';

    public function handle(ScheduledReportService $service): int
    {
        $this->info('Processing due scheduled reports...');

        $count = $service->processDueReports();

        $this->info("Processed {$count} scheduled report(s).");

        return self::SUCCESS;
    }
}
