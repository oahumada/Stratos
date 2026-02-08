<?php

namespace Tests\Feature;

use App\Models\Capability;
use App\Models\CapabilityCompetency;
use App\Models\Competency;
use App\Models\Organizations;
use App\Models\Scenario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapabilityCompetencyTest extends TestCase
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

    // ============================================================================
    // CREATE: Vincular competencia existente
    // ============================================================================

    public function test_attach_existing_competency_creates_pivot()
    {
        $scenario = Scenario::create(['organization_id' => $this->organization->id, 'name' => 'S1', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap A']);
        $competency = Competency::create(['organization_id' => $this->organization->id, 'capability_id' => $cap->id, 'name' => 'Existing Comp']);

        $response = $this->actingAs($this->user)
            ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", [
                'competency_id' => $competency->id,
                'required_level' => 4,
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('capability_competencies', [
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'competency_id' => $competency->id,
            'required_level' => 4,
        ]);
    }

    // ============================================================================
    // CREATE: Nueva competencia desde capability
    // ============================================================================

    public function test_create_new_competency_and_pivot_in_transaction()
    {
        $scenario = Scenario::create(['organization_id' => $this->organization->id, 'name' => 'S2', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap B']);

        $payload = [
            'competency' => ['name' => 'New Comp', 'description' => 'Created via API'],
            'required_level' => 2,
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", $payload);

        $response->assertStatus(201)->assertJson(['success' => true]);

        $this->assertDatabaseHas('competencies', [
            'organization_id' => $this->organization->id,
            'name' => 'New Comp',
        ]);

        $comp = Competency::where('name', 'New Comp')->where('organization_id', $this->organization->id)->first();
        $this->assertNotNull($comp);

        $this->assertDatabaseHas('capability_competencies', [
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'competency_id' => $comp->id,
            'required_level' => 2,
        ]);
    }

    // ============================================================================
    // CREATE: Todos los campos se guardan correctamente
    // ============================================================================

    public function test_all_fields_saved_when_creating_competency()
    {
        $scenario = Scenario::create(['organization_id' => $this->organization->id, 'name' => 'S3', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap C']);

        $response = $this->actingAs($this->user)
            ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", [
                'competency' => [
                    'name' => 'Cloud Architecture',
                    'description' => 'Design and implement cloud solutions',
                ],
                'required_level' => 5,
                'weight' => 95,
                'rationale' => 'Critical for modern infrastructure',
                'is_required' => true,
            ]);

        $response->assertStatus(201);

        // Verificar competencia se creó
        $competency = Competency::where('name', 'Cloud Architecture')
            ->where('organization_id', $this->organization->id)
            ->first();
        $this->assertNotNull($competency);
        $this->assertEquals('Design and implement cloud solutions', $competency->description);

        // Verificar pivot se creó con TODOS los campos
        $pivot = CapabilityCompetency::where([
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'competency_id' => $competency->id,
        ])->first();

        $this->assertNotNull($pivot);
        $this->assertEquals(5, $pivot->required_level);
        $this->assertEquals(95, $pivot->weight);
        $this->assertEquals('Critical for modern infrastructure', $pivot->rationale);
        $this->assertTrue($pivot->is_required);
    }

    // ============================================================================
    // CREATE: Valores por defecto
    // ============================================================================

    public function test_default_values_when_fields_omitted()
    {
        $scenario = Scenario::create(['organization_id' => $this->organization->id, 'name' => 'S4', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap D']);

        // Enviar payload mínimo
        $response = $this->actingAs($this->user)
            ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", [
                'competency' => ['name' => 'API Design'],
                // required_level es requerido, pero weight, rationale, is_required son opcionales
            ]);

        $response->assertStatus(201);

        $competency = Competency::where('name', 'API Design')
            ->where('organization_id', $this->organization->id)
            ->first();

        $pivot = CapabilityCompetency::where('competency_id', $competency->id)->first();

        // Validar valores por defecto
        $this->assertEquals(3, $pivot->required_level, 'required_level should default to 3');
        $this->assertNull($pivot->weight, 'weight should be null by default');
        $this->assertNull($pivot->rationale, 'rationale should be null by default');
        $this->assertFalse($pivot->is_required, 'is_required should be false by default');
    }

    // ============================================================================
    // CREATE: Prevenir duplicados
    // ============================================================================

    public function test_prevent_duplicate_relationship()
    {
        $scenario = Scenario::create(['organization_id' => $this->organization->id, 'name' => 'S5', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap E']);
        $comp = Competency::create(['organization_id' => $this->organization->id, 'capability_id' => $cap->id, 'name' => 'DevOps']);

        // Primer intento: crear
        $response1 = $this->actingAs($this->user)
            ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", [
                'competency_id' => $comp->id,
                'required_level' => 3,
            ]);
        $response1->assertStatus(201);

        // Segundo intento: mismo competency_id
        $response2 = $this->actingAs($this->user)
            ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", [
                'competency_id' => $comp->id,
                'required_level' => 3,
            ]);
        $response2->assertStatus(200)
            ->assertJsonPath('note', 'already_exists');

        // Verificar que solo existe UNA relación
        $count = CapabilityCompetency::where([
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'competency_id' => $comp->id,
        ])->count();
        $this->assertEquals(1, $count);
    }

    // ============================================================================
    // MULTI-TENANCY: Prevenir acceso a otra organización
    // ============================================================================

    public function test_cannot_create_competency_in_different_org()
    {
        $otherOrg = Organizations::factory()->create();
        $otherOrgScenario = Scenario::create(['organization_id' => $otherOrg->id, 'name' => 'Other Org Scenario', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $otherOrgCap = Capability::create(['organization_id' => $otherOrg->id, 'name' => 'Other Org Cap']);

        // User de org A intenta acceder a escenario de org B
        $response = $this->actingAs($this->user)
            ->postJson("/api/strategic-planning/scenarios/{$otherOrgScenario->id}/capabilities/{$otherOrgCap->id}/competencies", [
                'competency' => ['name' => 'Hacked Competency'],
            ]);

        $response->assertStatus(403);
    }

    // ============================================================================
    // UPDATE: Modificar relación capability_competencies
    // ============================================================================

    public function test_update_capability_competency_fields()
    {
        $scenario = Scenario::create(['organization_id' => $this->organization->id, 'name' => 'S6', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap F']);
        $comp = Competency::create(['organization_id' => $this->organization->id, 'capability_id' => $cap->id, 'name' => 'Initial Comp']);

        // Crear relación con valores iniciales
        CapabilityCompetency::create([
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'competency_id' => $comp->id,
            'required_level' => 2,
            'weight' => 40,
            'rationale' => 'Initial rationale',
            'is_required' => false,
        ]);

        // Actualizar
        $response = $this->actingAs($this->user)
            ->patchJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies/{$comp->id}", [
                'required_level' => 5,
                'weight' => 90,
                'rationale' => 'Updated rationale',
                'is_required' => true,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verificar cambios
        $pivot = CapabilityCompetency::where('competency_id', $comp->id)->first();
        $this->assertEquals(5, $pivot->required_level);
        $this->assertEquals(90, $pivot->weight);
        $this->assertEquals('Updated rationale', $pivot->rationale);
        $this->assertTrue($pivot->is_required);
    }

    // ============================================================================
    // DELETE: Eliminar relación (sin eliminar la competencia)
    // ============================================================================

    public function test_delete_capability_competency_relationship()
    {
        $scenario = Scenario::create(['organization_id' => $this->organization->id, 'name' => 'S7', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap G']);
        $comp = Competency::create(['organization_id' => $this->organization->id, 'capability_id' => $cap->id, 'name' => 'Delete Me Comp']);

        // Crear relación
        CapabilityCompetency::create([
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'competency_id' => $comp->id,
            'required_level' => 3,
        ]);

        $this->assertDatabaseHas('capability_competencies', [
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'competency_id' => $comp->id,
        ]);

        // Eliminar relación
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies/{$comp->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verificar que relación se eliminó
        $this->assertDatabaseMissing('capability_competencies', [
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'competency_id' => $comp->id,
        ]);

        // Pero competencia sigue existiendo
        $this->assertDatabaseHas('competencies', [
            'id' => $comp->id,
            'name' => 'Delete Me Comp',
        ]);
    }

    // ============================================================================
    // SECURITY: No se puede eliminar relación de otra org
    // ============================================================================

    public function test_cannot_delete_other_org_relationship()
    {
        $otherOrg = Organizations::factory()->create();
        $otherOrgUser = User::factory()->create(['organization_id' => $otherOrg->id]);

        $scenario = Scenario::create(['organization_id' => $this->organization->id, 'name' => 'S8', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap H']);
        $comp = Competency::create(['organization_id' => $this->organization->id, 'capability_id' => $cap->id, 'name' => 'Secure Comp']);

        CapabilityCompetency::create([
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'competency_id' => $comp->id,
        ]);

        // User de otra org intenta eliminar
        $response = $this->actingAs($otherOrgUser)
            ->deleteJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies/{$comp->id}");

        $response->assertStatus(403);
    }
}
