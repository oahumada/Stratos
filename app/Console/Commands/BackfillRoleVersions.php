<?php

namespace App\Console\Commands;

use App\Models\RoleVersion;
use App\Models\ScenarioRole;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BackfillRoleVersions extends Command
{
    protected $signature = 'backfill:role-versions {--apply : Actually create records instead of dry-run}';

    protected $description = 'Dry-run backfill for role_versions from scenario_roles (option --apply to execute)';

    public function handle(): int
    {
        $apply = $this->option('apply');
        $this->info('Scanning scenario_roles for roles without versions...');

        $rows = ScenarioRole::with('role')->get();
        if ($rows->isEmpty()) {
            $this->info('No scenario roles found.');

            return 0;
        }

        $created = 0;
        foreach ($rows as $sr) {
            $role = $sr->role;
            if (! $role) {
                continue;
            }

            $exists = RoleVersion::where('role_id', $role->id)->exists();
            if ($exists) {
                $this->line(" - Role {$role->id} ({$role->name}) already has versions — skipping");

                continue;
            }

            $this->line(" - WOULD CREATE RoleVersion for role {$role->id} ({$role->name}) [version_group_id: will be new uuid] (source scenario: {$sr->scenario_id})");
            if ($apply) {
                $rv = RoleVersion::create([
                    'organization_id' => $role->organization_id ?? null,
                    'role_id' => $role->id,
                    'version_group_id' => (string) Str::uuid(),
                    'name' => $role->name,
                    'description' => $role->description ?? null,
                    'effective_from' => now()->toDateString(),
                    'evolution_state' => 'new_embryo',
                    'metadata' => ['source' => 'backfill', 'scenario_id' => $sr->scenario_id],
                    'created_by' => auth()->id() ?? null,
                ]);
                $created++;
                $this->line("    -> Created RoleVersion id={$rv->id}");
            }
        }

        if ($apply) {
            $this->info("Finished: created {$created} role_versions.");
        } else {
            $this->info('Dry-run complete. Re-run with --apply to create records.');
        }

        return 0;
    }
}
