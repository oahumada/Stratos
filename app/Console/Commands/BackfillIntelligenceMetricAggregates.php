<?php

namespace App\Console\Commands;

use App\Models\IntelligenceMetric;
use App\Services\IntelligenceMetricsAggregator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class BackfillIntelligenceMetricAggregates extends Command
{
    protected $signature = 'backfill:intelligence-metric-aggregates
        {--from= : Start date (Y-m-d)}
        {--to= : End date (Y-m-d)}
        {--organization_id= : Optional organization_id scope}
        {--apply : Persist changes (default is dry-run)}';

    protected $description = 'Backfill intelligence_metric_aggregates from intelligence_metrics historically (idempotent)';

    /**
     * Ejecuta backfill histórico de agregados diarios desde métricas crudas.
     *
     * Flujo:
     * - Valida rango de fechas y organization_id opcional.
     * - Informa volumen de métricas fuente en el rango.
     * - En modo dry-run no persiste cambios.
     * - En modo apply recalcula y upsertea agregados por día.
     */
    public function handle(IntelligenceMetricsAggregator $aggregator): int
    {
        $isInvalid = false;

        $dateRange = $this->resolveDateRange(
            $this->option('from'),
            $this->option('to')
        );

        if (! $dateRange) {
            $isInvalid = true;
        }

        $fromDate = $dateRange[0] ?? null;
        $toDate = $dateRange[1] ?? null;

        $orgResolution = $this->resolveOrganizationId($this->option('organization_id'));
        if (! $orgResolution['valid']) {
            $isInvalid = true;
        }

        $orgId = $orgResolution['organization_id'];
        $apply = (bool) $this->option('apply');

        if ($isInvalid || ! $fromDate || ! $toDate) {
            return self::INVALID;
        }

        $period = CarbonPeriod::create($fromDate->copy()->startOfDay(), $toDate->copy()->startOfDay());
        $dates = collect($period)->map(fn (Carbon $date) => $date->copy());

        $this->info('Starting intelligence aggregates historical backfill');
        $this->line('Date range: '.$fromDate->toDateString().' -> '.$toDate->toDateString());
        $this->line('Organization: '.($orgId === null ? 'all (+ global)' : $orgId));
        $this->line('Mode: '.($apply ? 'apply' : 'dry-run'));

        $totalSourceMetrics = IntelligenceMetric::query()
            ->when($orgId !== null, fn ($query) => $query->where('organization_id', $orgId))
            ->whereBetween('created_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
            ->count();

        $this->line('Source metrics in range: '.$totalSourceMetrics);

        if (! $apply) {
            $this->warn('Dry-run mode: no aggregates were persisted. Use --apply to write results.');

            return self::SUCCESS;
        }

        $processedDays = 0;
        foreach ($dates as $date) {
            if ($orgId === null) {
                $aggregator->aggregateAllMetricsForDate($date);
            } else {
                $aggregates = $aggregator->aggregateMetricsForDate($orgId, $date);
                $aggregator->storeAggregates($aggregates);
            }

            $processedDays++;
            $this->line('Processed '.$date->toDateString());
        }

        $this->info("Done. Processed {$processedDays} day(s).");

        return self::SUCCESS;
    }

    /**
     * Resuelve y valida el rango [from, to] usado por el backfill.
     *
     * @return array{0: Carbon, 1: Carbon}|null
     */
    protected function resolveDateRange(?string $fromInput, ?string $toInput): ?array
    {
        $fromDate = $this->resolveDate($fromInput, true);
        $toDate = $this->resolveDate($toInput, false);

        if (! $fromDate || ! $toDate) {
            return null;
        }

        if ($fromDate->greaterThan($toDate)) {
            $this->error('Invalid range: --from must be less than or equal to --to.');

            return null;
        }

        return [$fromDate, $toDate];
    }

    /**
     * Valida y normaliza el organization_id opcional.
     *
     * @return array{valid: bool, organization_id: int|null}
     */
    protected function resolveOrganizationId(mixed $organizationId): array
    {
        if ($organizationId === null) {
            return ['valid' => true, 'organization_id' => null];
        }

        if (! ctype_digit((string) $organizationId)) {
            $this->error('Invalid --organization_id. It must be an integer.');

            return ['valid' => false, 'organization_id' => null];
        }

        return ['valid' => true, 'organization_id' => (int) $organizationId];
    }

    /**
     * Resuelve una fecha desde input explícito o desde límites de datos existentes.
     */
    protected function resolveDate(?string $value, bool $isStart): ?Carbon
    {
        $resolvedDate = null;

        if (blank($value)) {
            $boundary = IntelligenceMetric::query()
                ->selectRaw($isStart ? 'MIN(created_at) as boundary' : 'MAX(created_at) as boundary')
                ->value('boundary');

            if (! $boundary) {
                $this->error('No intelligence metrics found. Provide explicit --from/--to or create source data first.');
            } else {
                $resolvedDate = Carbon::parse($boundary);
            }
        } else {
            try {
                $resolvedDate = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
            } catch (\Throwable) {
                $this->error("Invalid date format '{$value}'. Expected Y-m-d.");
            }
        }

        return $resolvedDate;
    }
}
