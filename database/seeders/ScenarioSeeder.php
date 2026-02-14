<?php

namespace Database\Seeders;

use App\Models\Capability;
use App\Models\Competency;
use App\Models\CompetencySkill;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\ScenarioCapability;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleSkill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $orgId = 1;

        // 1. Crear Escenario (idempotente)
        $scenario = Scenario::firstOrCreate(
            ['organization_id' => $orgId, 'name' => 'Adopción de IA Generativa 2026'],
            [
                'description' => 'Plan estratégico para integrar GenAI en el ciclo de vida de producto.',
                'horizon_months' => 18,
                'fiscal_year' => now()->year,
                'created_by' => 1,
                'updated_by' => 1,
                'owner_user_id' => 1,
                'status' => 'draft',
                'assumptions' => ['tech_stack' => 'OpenAI/Anthropic', 'budget_approved' => true],
            ]
        );

        // 2. Crear Capability (Incubada)
        // 2. Crear/obtener Capability (idempotente)
        $cap = Capability::firstOrCreate(
            ['organization_id' => $orgId, 'name' => 'AI-Enabled Product Development'],
            ['category' => 'technical', 'discovered_in_scenario_id' => $scenario->id]
        );

        // Asociar capability al scenario si no existe
        $scenarioCap = ScenarioCapability::firstOrCreate(
            ['scenario_id' => $scenario->id, 'capability_id' => $cap->id],
            ['strategic_role' => 'target', 'strategic_weight' => 80, 'priority' => 5]
        );

        // 3. Crear Competencia
        $comp = Competency::firstOrCreate(
            ['organization_id' => $orgId, 'name' => 'AI Technical Implementation'],
            ['description' => 'Competency for AI technical implementation']
        );

        // 4. Crear Skills (Una existente, una incubada)
        $skillExistente = 1; // Asumiendo ID 1 es Python
        $skillIncubadaId = DB::table('skills')->where('name', 'Prompt Engineering')->value('id');
        if (! $skillIncubadaId) {
            $skillData = [
                'name' => 'Prompt Engineering',
                'organization_id' => $orgId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            // Add optional columns only if present in schema
            if (\Illuminate\Support\Facades\Schema::hasColumn('skills', 'maturity_status')) {
                $skillData['maturity_status'] = 'emerging';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('skills', 'complexity_level')) {
                $skillData['complexity_level'] = 'tactical';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('skills', 'lifecycle_status')) {
                $skillData['lifecycle_status'] = 'active';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('skills', 'category')) {
                $skillData['category'] = 'technical';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('skills', 'scope_type')) {
                $skillData['scope_type'] = 'domain';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('skills', 'is_critical')) {
                $skillData['is_critical'] = false;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('skills', 'discovered_in_scenario_id')) {
                $skillData['discovered_in_scenario_id'] = $scenario->id;
            }
            $skillIncubadaId = DB::table('skills')->insertGetId($skillData);
        }

        // Asociar skills a competency si no existen
        if (! CompetencySkill::where(['competency_id' => $comp->id, 'skill_id' => $skillExistente])->exists()) {
            CompetencySkill::create(['competency_id' => $comp->id, 'skill_id' => $skillExistente, 'weight' => 40]);
        }
        if (! CompetencySkill::where(['competency_id' => $comp->id, 'skill_id' => $skillIncubadaId])->exists()) {
            CompetencySkill::create(['competency_id' => $comp->id, 'skill_id' => $skillIncubadaId, 'weight' => 60]);
        }

        // 5. Definir Demanda para un Rol (Fase 2)
        // Ensure a role exists (Product Manager) and use its id
        $role = Roles::firstOrCreate(
            ['name' => 'Product Manager', 'organization_id' => $orgId],
            ['description' => 'Auto-seeded role']
        );
        ScenarioRole::firstOrCreate([
            'scenario_id' => $scenario->id,
            'role_id' => $role->id,
        ], [
            'role_change' => 'evolve',
        ]);

        ScenarioRoleSkill::create([
            'scenario_id' => $scenario->id,
            'role_id' => $role->id,
            'skill_id' => $skillIncubadaId,
            'required_level' => 4,
            'change_type' => 'new',
        ]);

        // 6. Simular Oferta Real (Fase 3)
        // Ensure there is a People row to reference (link to admin user created by E2ESeeder)
        $adminEmail = env('E2E_ADMIN_EMAIL', 'admin@example.com');
        $person = People::firstOrCreate(
            ['organization_id' => $orgId, 'email' => $adminEmail],
            [
                'first_name' => 'E2E',
                'last_name' => 'Admin',
                'organization_id' => $orgId,
            ]
        );

        // Intenta vincular con el usuario E2E Admin si existe
        $adminUser = \App\Models\User::where('email', $adminEmail)->first();
        if ($adminUser && !$person->user_id) {
            $person->update(['user_id' => $adminUser->id]);
        }

        // Determinar quién evalúa: preferiblemente un usuario válido
        // people_role_skills.evaluated_by tiene FK a users.id
        $evaluatorId = $adminUser ? $adminUser->id : ($person->user_id ?? 1);

        PeopleRoleSkills::firstOrCreate([
            'people_id' => $person->id,
            'role_id' => $role->id,
            'skill_id' => $skillIncubadaId,
        ], [
            'current_level' => 2, // Genera un GAP
            'evaluated_by' => $evaluatorId,
            'evidence_source' => 'self_assessment',
        ]);
    }
}
