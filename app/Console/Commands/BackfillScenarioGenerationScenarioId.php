<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillScenarioGenerationScenarioId extends Command
{
    protected $signature = 'backfill:scenario-generation-scenario-id';

    protected $description = 'Backfill scenario_generations.scenario_id from scenarios.source_generation_id (idempotent)';

    public function handle(): int
    {
        $this->info('Starting backfill: scenario_generations.scenario_id <- scenarios.source_generation_id');

        $total = DB::table('scenarios')->whereNotNull('source_generation_id')->count();
        $this->info("Scenarios with source_generation_id: {$total}");

        $updated = 0;

        DB::table('scenarios')
            ->whereNotNull('source_generation_id')
            ->orderBy('id')
            ->chunkById(100, function ($rows) use (&$updated) {
                foreach ($rows as $row) {
                    $affected = DB::table('scenario_generations')
                        ->where('id', $row->source_generation_id)
                        ->whereNull('scenario_id')
                        ->update(['scenario_id' => $row->id]);

                    $updated += $affected;
                }
            });

        $this->info("Backfilled {$updated} scenario_generations rows.");
        $this->info('Done.');

        return 0;
    }
}
