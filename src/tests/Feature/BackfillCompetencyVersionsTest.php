<?php

namespace Tests\Feature;

use App\Models\Organizations;
use App\Models\User;
use App\Models\Scenario;
use App\Models\Capability;
use App\Models\Competency;
use App\Models\CompetencyVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackfillCompetencyVersionsTest extends TestCase
{
    use RefreshDatabase;

    protected Organizations $organization;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organization = Organizations::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
    }

    public function test_backfill_dry_run_and_apply_creates_versions()
    {
        $scenario = Scenario::create([
            'organization_id' => $this->organization->id,
            'name' => 'Backfill Scenario',
            'horizon_months' => 12,
            'fiscal_year' => now()->year,
            'created_by' => $this->user->id,
        ]);

        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap BF', 'discovered_in_scenario_id' => $scenario->id]);

        $comp = Competency::create(['organization_id' => $this->organization->id, 'name' => 'Comp BF']);
        // attach via pivot
        $cap->competencies()->attach($comp->id, ['scenario_id' => $scenario->id, 'required_level' => 3]);

        // Dry-run: should not create records
        $this->artisan('backfill:competency-versions')->assertExitCode(0);
        $this->assertDatabaseCount('competency_versions', 0);

        // Apply: should create records
        $this->artisan('backfill:competency-versions --apply')->assertExitCode(0);
        $this->assertDatabaseHas('competency_versions', [
            'competency_id' => $comp->id,
            'organization_id' => $this->organization->id,
            'evolution_state' => 'new_embryo',
        ]);
    }
}
