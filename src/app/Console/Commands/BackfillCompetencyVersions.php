<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Capability;
use App\Models\CompetencyVersion;

class BackfillCompetencyVersions extends Command
{
    protected $signature = 'backfill:competency-versions {--apply : Actually create records instead of dry-run}';
    protected $description = 'Dry-run backfill for competency_versions from discovered_in_scenario_id (option --apply to execute)';

    public function handle(): int
    {
        $apply = $this->option('apply');
        $this->info('Scanning capabilities with discovered_in_scenario_id...');

        $capabilities = Capability::whereNotNull('discovered_in_scenario_id')->with('competencies')->get();
        if ($capabilities->isEmpty()) {
            $this->info('No incubated capabilities found.');
            return 0;
        }

        $created = 0;
        foreach ($capabilities as $cap) {
            $scenarioId = $cap->discovered_in_scenario_id;
            $this->line("Capability {$cap->id} ({$cap->name}) — scenario={$scenarioId} — competencies={$cap->competencies->count()}");
            foreach ($cap->competencies as $comp) {
                $exists = CompetencyVersion::where('competency_id', $comp->id)->exists();
                if ($exists) {
                    $this->line("  - Competency {$comp->id} ({$comp->name}) already has versions — skipping");
                    continue;
                }
                $this->line("  - WOULD CREATE CompetencyVersion for competency {$comp->id} ({$comp->name}) [version_group_id: will be new uuid] (source scenario: {$scenarioId})");
                if ($apply) {
                    $cv = CompetencyVersion::create([
                        'organization_id' => $comp->organization_id ?? $cap->organization_id ?? null,
                        'competency_id' => $comp->id,
                        'version_group_id' => (string) Str::uuid(),
                        'name' => $comp->name,
                        'description' => $comp->description ?? null,
                        'effective_from' => now()->toDateString(),
                        'evolution_state' => 'new_embryo',
                        'metadata' => ['source' => 'backfill', 'scenario_id' => $scenarioId],
                        'created_by' => auth()->id() ?? null,
                    ]);
                    $created++;
                    $this->line("    -> Created CompetencyVersion id={$cv->id}");
                }
            }
        }

        if ($apply) {
            $this->info("Finished: created {$created} competency_versions.");
        } else {
            $this->info('Dry-run complete. Re-run with --apply to create records.');
        }

        return 0;
    }
}
