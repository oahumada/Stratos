<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Scenario;
use App\Models\Capability;
use App\Models\Competency;
use App\Models\CompetencySkill;
use App\Models\ScenarioCapability;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleSkill;
use App\Models\PersonRoleSkill;
use Illuminate\Support\Facades\DB;

class ScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $orgId = 1;

        // 1. Crear Escenario
        $scenario = Scenario::create([
            'organization_id' => $orgId,
            'name' => 'Adopción de IA Generativa 2026',
            'description' => 'Plan estratégico para integrar GenAI en el ciclo de vida de producto.',
            'horizon_months' => 18,
            'fiscal_year' => now()->year,
            'created_by' => 1,
            'updated_by' => 1,
            'owner_user_id' => 1,
            'status' => 'draft',
            'assumptions' => ['tech_stack' => 'OpenAI/Anthropic', 'budget_approved' => true]
        ]);

        // 2. Crear Capability (Incubada)
        $cap = Capability::create([
            'organization_id' => $orgId,
            'name' => 'AI-Enabled Product Development',
            'category' => 'technical',
            'discovered_in_scenario_id' => $scenario->id
        ]);

        ScenarioCapability::create([
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'strategic_role' => 'target',
            'strategic_weight' => 80,
            'priority' => 5
        ]);

        // 3. Crear Competencia
        $comp = Competency::create([
            'organization_id' => $orgId,
            'capability_id' => $cap->id,
            'name' => 'AI Technical Implementation'
        ]);

        // 4. Crear Skills (Una existente, una incubada)
        $skillExistente = 1; // Asumiendo ID 1 es Python
        $skillIncubada = DB::table('skills')->insertGetId([
            'name' => 'Prompt Engineering',
            'maturity_status' => 'emerging',
            'discovered_in_scenario_id' => $scenario->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        CompetencySkill::create(['competency_id' => $comp->id, 'skill_id' => $skillExistente, 'weight' => 40]);
        CompetencySkill::create(['competency_id' => $comp->id, 'skill_id' => $skillIncubada, 'weight' => 60]);

        // 5. Definir Demanda para un Rol (Fase 2)
        $roleId = 10; // Asumiendo Product Manager
        ScenarioRole::create([
            'scenario_id' => $scenario->id,
            'role_id' => $roleId,
            'role_change' => 'evolve'
        ]);

        ScenarioRoleSkill::create([
            'scenario_id' => $scenario->id,
            'role_id' => $roleId,
            'skill_id' => $skillIncubada,
            'required_level' => 4,
            'change_type' => 'new'
        ]);

        // 6. Simular Oferta Real (Fase 3)
        PersonRoleSkill::create([
            'person_id' => 1,
            'role_id' => $roleId,
            'skill_id' => $skillIncubada,
            'current_level' => 2, // Genera un GAP
            'evidence_source' => 'self_assessment'
        ]);
    }
}
