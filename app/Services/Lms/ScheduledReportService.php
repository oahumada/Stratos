<?php

namespace App\Services\Lms;

use App\Mail\ScheduledReportMail;
use App\Models\LmsScheduledReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ScheduledReportService
{
    public function __construct(
        protected LmsReportService $reportService,
    ) {}

    public function create(int $orgId, int $userId, array $data): LmsScheduledReport
    {
        return LmsScheduledReport::create([
            'organization_id' => $orgId,
            'created_by' => $userId,
            'report_type' => $data['report_type'],
            'filters' => $data['filters'] ?? null,
            'frequency' => $data['frequency'],
            'recipients' => $data['recipients'],
            'next_send_at' => $this->calculateNextSend($data['frequency'], Carbon::now()),
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(int $reportId, array $data): LmsScheduledReport
    {
        $report = LmsScheduledReport::findOrFail($reportId);
        $report->update($data);

        if (isset($data['frequency'])) {
            $report->update([
                'next_send_at' => $this->calculateNextSend($data['frequency'], Carbon::now()),
            ]);
        }

        return $report->fresh();
    }

    public function processDueReports(): int
    {
        $dueReports = LmsScheduledReport::due()->get();
        $processed = 0;

        foreach ($dueReports as $report) {
            $reportData = $this->generateReportData($report);
            $html = $this->reportService->generateReportHtml(
                $report->organization_id,
                $report->report_type,
                $report->filters ?? [],
            );

            foreach ($report->recipients as $email) {
                Mail::to($email)->send(new ScheduledReportMail(
                    $report->report_type,
                    $html,
                ));
            }

            $report->update([
                'last_sent_at' => Carbon::now(),
                'next_send_at' => $this->calculateNextSend($report->frequency, Carbon::now()),
            ]);

            $processed++;
        }

        return $processed;
    }

    public function calculateNextSend(string $frequency, Carbon $from): Carbon
    {
        return match ($frequency) {
            'daily' => $from->copy()->addDay()->startOfDay()->addHours(8),
            'weekly' => $from->copy()->addWeek()->startOfWeek()->addHours(8),
            'monthly' => $from->copy()->addMonth()->startOfMonth()->addHours(8),
            default => $from->copy()->addDay(),
        };
    }

    public function getForOrganization(int $orgId): mixed
    {
        return LmsScheduledReport::forOrganization($orgId)
            ->with('creator:id,name,email')
            ->orderByDesc('created_at')
            ->get();
    }

    protected function generateReportData(LmsScheduledReport $report): array
    {
        $filters = $report->filters ?? [];

        return match ($report->report_type) {
            'completion' => $this->reportService->completionReport($report->organization_id, $filters),
            'compliance' => $this->reportService->complianceStatusReport($report->organization_id),
            'engagement' => $this->reportService->engagementTrendsReport($report->organization_id),
            'time_to_complete' => $this->reportService->timeToCompleteReport($report->organization_id),
            default => [],
        };
    }
}
