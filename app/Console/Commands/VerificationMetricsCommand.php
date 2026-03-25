<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class VerificationMetricsCommand extends Command
{
    protected $signature = 'verification:metrics {--phase= : Filter by specific phase} {--json : Output as JSON} {--window=24 : Last N hours}';

    protected $description = 'Display verification metrics and statistics';

    public function handle(): int
    {
        $window = $this->option('window');
        $phase = config('verification.phase');
        $isJson = $this->option('json');

        $startTime = now()->subHours($window);

        // Collect violations from event store
        $violations = DB::table('event_store')
            ->where('event_name', 'like', 'verification%')
            ->where('created_at', '>=', $startTime)
            ->get();

        // Calculate metrics
        $metrics = [
            'period_hours' => $window,
            'current_phase' => $phase,
            'timestamp' => now(),
            'total_events' => $violations->count(),
            'events_by_type' => $violations->groupBy('event_name')->map->count(),
        ];

        if ($isJson) {
            $this->line(json_encode($metrics, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return 0;
        }

        // Display as table
        $this->info('════════════════════════════════════════════');
        $this->info('Verification Metrics - Last '.$window.' hours');
        $this->info('════════════════════════════════════════════');
        $this->line('');
        $this->line('Current Phase: <info>'.strtoupper($phase).'</info>');
        $this->line('Timestamp: <fg=gray>'.now().'</>');
        $this->line('');

        $this->info('Summary');
        $this->table([
            'Metric',
            'Value',
        ], [
            ['Total Events', number_format($metrics['total_events'])],
            ['Period', $metrics['period_hours'].' hours'],
        ]);

        if (! empty($metrics['events_by_type'])) {
            $this->info('');
            $this->info('Events by Type');
            $eventRows = collect($metrics['events_by_type'])
                ->map(fn ($count, $type) => [$type, number_format($count)])
                ->values()
                ->all();
            $this->table(['Event Type', 'Count'], $eventRows);
        }

        $this->line('');

        return 0;
    }
}
