<?php

namespace App\Console\Commands;

use App\Models\BusinessMetric;
use App\Models\Departments;
use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IngestBusinessMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stratos:ingest-metrics {file : Path to the CSV file} {--org= : Organization ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest business metrics from a CSV file following the Stratos Data Dictionary specification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $orgId = $this->option('org') ?? Organization::first()?->id;

        if (!$orgId) {
            $this->error('No organization found. Please provide --org ID.');
            return 1;
        }

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Starting ingestion for Organization #{$orgId}...");

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        if (!$header) {
            $this->error("Empty or invalid CSV file.");
            return 1;
        }

        $rowCount = 0;
        $successCount = 0;
        $errorCount = 0;

        while (($row = fgetcsv($file)) !== false) {
            $rowCount++;
            $data = array_combine($header, $row);

            // Validation according to Data Dictionary
            $validator = Validator::make($data, [
                'metric_name'  => 'required|string',
                'metric_value' => 'required|numeric',
                'period_date'  => 'required|date_format:Y-m-d',
                'source'       => 'required|string',
                'unit'         => 'nullable|string',
                'department'   => 'nullable|string', // Can be ID or Alias
                'metadata'     => 'nullable|string', // Expecting JSON string or empty
            ]);

            if ($validator->fails()) {
                $this->warn("Row #{$rowCount} failed validation: " . implode(', ', $validator->errors()->all()));
                $errorCount++;
                continue;
            }

            try {
                DB::transaction(function () use ($data, $orgId, &$successCount) {
                    $departmentId = $this->resolveDepartment($data['department'] ?? null, $orgId);

                    $metadata = [];
                    if (!empty($data['metadata'])) {
                        $metadata = json_decode($data['metadata'], true) ?? [];
                    }

                    BusinessMetric::updateOrCreate(
                        [
                            'organization_id' => $orgId,
                            'metric_name'     => $data['metric_name'],
                            'period_date'     => $data['period_date'],
                            'department_id'   => $departmentId,
                        ],
                        [
                            'metric_value' => $data['metric_value'],
                            'unit'         => $data['unit'] ?? null,
                            'source'       => $data['source'],
                            'metadata'     => $metadata,
                        ]
                    );

                    $successCount++;
                });
            } catch (\Exception $e) {
                $this->error("Row #{$rowCount} error: " . $e->getMessage());
                $errorCount++;
            }
        }

        fclose($file);

        $this->info("Ingestion complete!");
        $this->table(
            ['Total Rows', 'Success', 'Errors'],
            [[$rowCount, $successCount, $errorCount]]
        );

        return 0;
    }

    /**
     * Resolves department ID using ID or "Gravitational Node Unification" via Aliases.
     */
    protected function resolveDepartment($identifier, $orgId)
    {
        if (empty($identifier)) {
            return null;
        }

        // 1. Try resolving by direct ID
        if (is_numeric($identifier)) {
            $dept = Departments::where('organization_id', $orgId)->find($identifier);
            if ($dept) {
                return $dept->id;
            }
        }

        // 2. Gravitational Node Unification: Resolve by Name or Alias (JSON column)
        // Search by exact name first
        $dept = Departments::where('organization_id', $orgId)
            ->where('name', $identifier)
            ->first();

        if ($dept) {
            return $dept->id;
        }

        // Search within aliases JSON array
        $dept = Departments::where('organization_id', $orgId)
            ->whereJsonContains('aliases', $identifier)
            ->first();

        return $dept?->id;
    }
}
