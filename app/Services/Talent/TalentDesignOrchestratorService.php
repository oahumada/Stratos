<?php

namespace App\Services\Talent;

use App\Models\Competency;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use App\Services\AiOrchestratorService;
use App\Services\RoleSkillDerivationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TalentDesignOrchestratorService
{
    protected AiOrchestratorService $ai;
    protected RoleSkillDerivationService $derivation;

    public function __construct(AiOrchestratorService $ai, RoleSkillDerivationService $derivation)
    {
        $this->ai = $ai;
        $this->derivation = $derivation;
    }

    /**
     * FASE 1: Consulta a los agentes.
     * Envía el contexto completo del escenario al agente Diseñador de Roles
     * y retorna las propuestas de roles y competencias sin persistirlas.
     */
    public function orchestrate(int $scenarioId): array
    {
        $scenario = Scenario::with([
            'capabilities.competencies.skills'
        ])->findOrFail($scenarioId);

        $orgId = $scenario->organization_id;

        // 1. Catálogo actual de roles activos
        $currentRoles = Roles::where('organization_id', $orgId)
            ->where('status', 'active')
            ->get(['id', 'name', 'description'])
            ->toArray();

        // 2. Catálogo actual de competencias activas
        $currentCompetencies = Competency::where('organization_id', $orgId)
            ->where('status', 'active')
            ->get(['id', 'name', 'description'])
            ->toArray();

        // 3. Blueprint del Paso 1 (árbol capabilities → competencies → skills)
        $targetBlueprint = $this->formatBlueprint($scenario);

        // 4. Roles del escenario con contexto del Cubo (archetype, fte, human_leverage)
        $scenarioRoles = ScenarioRole::with('role')
            ->where('scenario_id', $scenarioId)
            ->get()
            ->map(fn($r) => [
                'name'           => $r->role->name ?? 'Sin nombre',
                'archetype'      => $r->archetype,
                'fte'            => $r->fte,
                'human_leverage' => $r->human_leverage,
                'role_change'    => $r->role_change,
                'evolution_type' => $r->evolution_type,
            ])
            ->toArray();

        // 5. Mappings ya existentes en la matriz (para no duplicar propuestas)
        $existingMappings = ScenarioRoleCompetency::where('scenario_id', $scenarioId)
            ->with(['role.role', 'competency'])
            ->get()
            ->map(fn($m) => [
                'role_name'       => $m->role->role->name ?? null,
                'competency_name' => $m->competency->name ?? null,
                'competency_id'   => $m->competency_id,
                'change_type'     => $m->change_type,
                'required_level'  => $m->required_level,
                'is_core'         => $m->is_core,
                'source'          => $m->source,
            ])
            ->toArray();

        // 6. Cargar el system prompt del archivo
        $promptPath = resource_path('prompt_instructions/talent_design_orchestration_es.md');
        $systemInstructions = file_exists($promptPath)
            ? file_get_contents($promptPath)
            : 'Actúa como Ingeniero de Talento.';

        // 7. Construir el task prompt enriquecido
        $taskPrompt = $this->buildTaskPrompt($scenario, $targetBlueprint, $currentRoles, $currentCompetencies, $scenarioRoles, $existingMappings);

        try {
            $result = $this->ai->agentThink('Ingeniero de Talento', $taskPrompt, $systemInstructions);

            return [
                'success'   => true,
                'proposals' => $result['response'] ?? $result,
                'metadata'  => [
                    'scenario_id'        => $scenarioId,
                    'agent'              => 'Ingeniero de Talento',
                    'existing_mappings'  => count($existingMappings),
                    'scenario_roles'     => count($scenarioRoles),
                ]
            ];
        } catch (\Exception $e) {
            Log::error("Error en orquestación de talento: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    /**
     * FASE 2: Aplica en batch las propuestas aprobadas por el usuario.
     * Persiste roles, mappings y desencadena la derivación de skills.
     */
    public function applyProposals(int $scenarioId, array $approvedRoleProposals, array $approvedCatalogProposals): array
    {
        $scenario = Scenario::findOrFail($scenarioId);
        $orgId    = $scenario->organization_id;
        $stats    = ['roles_created' => 0, 'roles_evolved' => 0, 'mappings_created' => 0, 'competencies_created' => 0];

        DB::transaction(function () use ($scenarioId, $orgId, $approvedRoleProposals, $approvedCatalogProposals, &$stats) {

            // A. Persistir competencias nuevas del catálogo
            $competencyIdMap = []; // proposed_name → real id (para resolver referencias en roles)
            foreach ($approvedCatalogProposals as $proposal) {
                $type = strtoupper($proposal['type'] ?? 'ADD');

                if ($type === 'ADD') {
                    $comp = Competency::firstOrCreate(
                        ['organization_id' => $orgId, 'name' => $proposal['proposed_name']],
                        [
                            'description' => $proposal['action_rationale'] ?? null,
                            'status'      => 'in_incubation',
                        ]
                    );
                    $competencyIdMap[$proposal['proposed_name']] = $comp->id;
                    $stats['competencies_created']++;

                } elseif ($type === 'MODIFY' && !empty($proposal['competency_id'])) {
                    Competency::where('id', $proposal['competency_id'])->update([
                        'name'        => $proposal['proposed_name'],
                        'description' => $proposal['action_rationale'] ?? null,
                    ]);
                    $competencyIdMap[$proposal['proposed_name']] = $proposal['competency_id'];
                }
            }

            // B. Persistir propuestas de roles
            foreach ($approvedRoleProposals as $proposal) {
                $type = strtoupper($proposal['type'] ?? 'NEW');

                if ($type === 'NEW') {
                    // Crear el rol en el catálogo
                    $role = Roles::firstOrCreate(
                        ['organization_id' => $orgId, 'name' => $proposal['proposed_name']],
                        ['description' => $proposal['proposed_description'] ?? null, 'status' => 'in_incubation']
                    );

                    // Crear entrada en scenario_roles
                    $scenarioRole = \App\Models\ScenarioRole::updateOrCreate(
                        ['scenario_id' => $scenarioId, 'role_id' => $role->id],
                        [
                            'fte'             => $proposal['fte_suggested'] ?? 1.0,
                            'archetype'       => $proposal['archetype'] ?? null,
                            'human_leverage'  => $proposal['talent_composition']['human_percentage'] ?? null,
                            'role_change'     => 'create',
                            'evolution_type'  => 'new_role',
                            'impact_level'    => 'medium',
                            'rationale'       => $proposal['talent_composition']['logic_justification'] ?? null,
                        ]
                    );
                    $stats['roles_created']++;

                } elseif (in_array($type, ['EVOLVE', 'REPLACE']) && !empty($proposal['target_role_id'])) {
                    // Obtener o crear el scenario_role para el rol existente
                    $scenarioRole = \App\Models\ScenarioRole::updateOrCreate(
                        ['scenario_id' => $scenarioId, 'role_id' => $proposal['target_role_id']],
                        [
                            'fte'            => $proposal['fte_suggested'] ?? 1.0,
                            'archetype'      => $proposal['archetype'] ?? null,
                            'human_leverage' => $proposal['talent_composition']['human_percentage'] ?? null,
                            'role_change'    => 'modify',
                            'evolution_type' => $type === 'REPLACE' ? 'transformation' : 'upgrade_skills',
                            'impact_level'   => 'medium',
                        ]
                    );
                    $stats['roles_evolved']++;
                } else {
                    continue; // propuesta inválida, saltar
                }

                // C. Persistir los mappings rol → competencia
                foreach ($proposal['competency_mappings'] ?? [] as $mapping) {
                    // Ensure scenarioRole is set before proceeding
                    if (!$scenarioRole) {
                        Log::warning("ScenarioRole not found for proposal type '{$type}'. Skipping competency mappings.");
                        continue;
                    }

                    $compId = $this->resolveCompetencyId($mapping, $orgId, $competencyIdMap);
                    if (!$compId) {
                        continue;
                    }
                    ScenarioRoleCompetency::updateOrCreate(
                        [
                            'scenario_id'   => $scenarioId,
                            'role_id'       => $scenarioRole->id,
                            'competency_id' => $compId,
                        ],
                        [
                            'change_type'    => $mapping['change_type'] ?? 'enrichment',
                            'required_level' => $mapping['required_level'] ?? 3,
                            'is_core'        => $mapping['is_core'] ?? false,
                            'rationale'      => $mapping['rationale'] ?? null,
                            'source'         => 'agent',
                        ]
                    );
                    $stats['mappings_created']++;

                    try {
                        $this->derivation->deriveSkillsFromCompetencies($scenarioId, $scenarioRole->id);
                    } catch (\Exception $e) {
                        Log::warning("Skill derivation failed for role {$scenarioRole->id}: " . $e->getMessage());
                    }
                }
            }
        });

        return ['success' => true, 'stats' => $stats];
    }

    /**
     * FASE 4: Aprobación final — mueve roles y competencias a incubation.
     */
    public function finalizeStep2(int $scenarioId): array
    {
        // Verificar que el escenario existe (el controller ya validó autorización multi-tenant)
        Scenario::findOrFail($scenarioId);

        // Pre-conditions
        $rolesCount = \App\Models\ScenarioRole::where('scenario_id', $scenarioId)->count();
        if ($rolesCount === 0) {
            return ['success' => false, 'error' => 'El escenario debe tener al menos un rol antes de finalizar el Paso 2.'];
        }

        $rolesWithoutArchetype = \App\Models\ScenarioRole::where('scenario_id', $scenarioId)
            ->whereNull('archetype')
            ->count();
        if ($rolesWithoutArchetype > 0) {
            return ['success' => false, 'error' => "Hay {$rolesWithoutArchetype} roles sin arquetipo definido (E/T/O). Define el arquetipo antes de continuar."];
        }

        DB::transaction(function () use ($scenarioId) {
            // Roles nuevos del escenario → incubation
            Roles::whereIn('id',
                \App\Models\ScenarioRole::where('scenario_id', $scenarioId)
                    ->where('role_change', 'create')
                    ->pluck('role_id')
            )->update(['status' => 'in_incubation']);

            // Competencias nuevas del escenario → incubation
            Competency::whereIn('id',
                ScenarioRoleCompetency::where('scenario_id', $scenarioId)
                    ->where('source', 'agent')
                    ->pluck('competency_id')
            )->where('status', 'in_incubation')->update(['status' => 'in_incubation']);

            // Skills derivadas → incubation
            DB::table('skills')
                ->where('discovered_in_scenario_id', $scenarioId)
                ->update(['status' => 'incubation']);


            // Marcar el escenario como paso 2 completado
            Scenario::where('id', $scenarioId)->update([
                'status' => 'incubating',
            ]);
        });

        return ['success' => true, 'message' => 'Paso 2 finalizado. Roles y competencias en incubación.'];
    }

    /**
     * Genera un Blueprint de Ingeniería (BARS) para una competencia en un rol específico.
     */
    public function generateEngineeringBlueprint(int $scenarioId, string $roleName, string $competencyName, int $level, string $archetype): array
    {
        $scenario = Scenario::findOrFail($scenarioId);
        
        // System Prompt: El Ing. de Talento
        $systemInstructions = "Actúa como Ingeniero de Talento Senior experto en SFIA 8 y metodologías BARS. 
        Tu misión es redactar la ingeniería de detalle (conductas observables) para una competencia específica.
        Debes seguir estrictamente los descriptores de nivel de Stratos:
        1: Ayuda (Básico), 2: Aplica (Intermedio), 3: Habilita (Avanzado), 4: Asegura (Experto), 5: Maestro (Inspira).
        
        CONTEXTO DEL ROL:
        - Arquetipo: {$archetype} (E=Estratégico, T=Táctico, O=Operacional)
        - Nivel SFIA Objetivo: {$level}
        
        REGLAS DE REDACCIÓN:
        - Behaviour: Conductas observables.
        - Attitude: Mindset esperado.
        - Responsibility: Qué resultados garantiza.
        - Skills: Sub-habilidades técnicas necesarias.
        
        Devuelve SOLO un JSON con las claves: behaviour[], attitude[], responsibility[], skills[].";

        $taskPrompt = "Genera el Blueprint de Ingeniería para:
        ROL: {$roleName}
        COMPETENCIA: {$competencyName}
        NIVEL OBJETIVO: {$level}
        CONTEXTO ESCENARIO: {$scenario->description}
        
        Asegúrate de que las conductas reflejen el nivel {$level} de maestría para un arquetipo {$archetype}.";

        try {
            $result = $this->ai->agentThink('Ingeniero de Talento', $taskPrompt, $systemInstructions);
            return $result['response'] ?? $result;
        } catch (\Exception $e) {
            Log::error("Error generando BARS IA: " . $e->getMessage());
            throw $e;
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // Helpers privados
    // ─────────────────────────────────────────────────────────────────

    protected function buildTaskPrompt(
        Scenario $scenario,
        array $targetBlueprint,
        array $currentRoles,
        array $currentCompetencies,
        array $scenarioRoles,
        array $existingMappings
    ): string {
        $prompt  = "### CONTEXTO DEL ESCENARIO\n";
        $prompt .= "Nombre: {$scenario->name}\n";
        $prompt .= "Descripción: {$scenario->description}\n";
        $prompt .= "Horizonte: " . ($scenario->horizon_months ?? 12) . " meses\n\n";

        $prompt .= "### BLUEPRINT ESTRATÉGICO (generado en Paso 1)\n";
        $prompt .= json_encode($targetBlueprint, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

        $prompt .= "### CATÁLOGO ACTUAL DE LA EMPRESA\n";
        $prompt .= "ROLES ACTIVOS: " . json_encode($currentRoles, JSON_UNESCAPED_UNICODE) . "\n";
        $prompt .= "COMPETENCIAS ACTIVAS: " . json_encode($currentCompetencies, JSON_UNESCAPED_UNICODE) . "\n\n";

        if (!empty($scenarioRoles)) {
            $prompt .= "### ROLES YA ASIGNADOS AL ESCENARIO (con arquetipo del Cubo)\n";
            $prompt .= json_encode($scenarioRoles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        }

        if (!empty($existingMappings)) {
            $prompt .= "### MAPPINGS ROL-COMPETENCIA YA DEFINIDOS EN LA MATRIZ\n";
            $prompt .= "IMPORTANTE: NO repitas propuestas para combinaciones ya existentes.\n";
            $prompt .= json_encode($existingMappings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        }

        $prompt .= "### INSTRUCCIÓN\n";
        $prompt .= "Analiza el blueprint estratégico y propón el diseño de roles y competencias. ";
        $prompt .= "Para cada role_proposal incluye 'competency_mappings' con: competency_name, competency_id (si aplica), change_type (maintenance/transformation/enrichment/extinction), required_level (1-5), is_core (true/false), rationale.\n";
        $prompt .= "Para cada rol propuesto incluye también 'archetype' (E=Estratégico, T=Táctico, O=Operacional).\n";
        $prompt .= "Devuelve SOLO el JSON con las claves: role_proposals, catalog_proposals, alignment_score.";

        return $prompt;
    }

    protected function formatBlueprint(Scenario $scenario): array
    {
        return $scenario->capabilities->map(function ($cap) {
            return [
                'capability'   => $cap->name,
                'description'  => $cap->description,
                'competencies' => $cap->competencies->map(function ($comp) {
                    return [
                        'id'     => $comp->id,
                        'name'   => $comp->name,
                        'skills' => $comp->skills->pluck('name')->toArray()
                    ];
                })
            ];
        })->toArray();
    }

    /**
     * Resuelve el ID de una competencia a partir del mapping propuesto por el agente.
     * Primero intenta usar el ID explícito; si no, busca por nombre en el mapa local
     * de competencias recién creadas o en el catálogo activo de la organización.
     */
    protected function resolveCompetencyId(array $mapping, int $orgId, array $competencyIdMap): ?int
    {
        if (!empty($mapping['competency_id'])) {
            return (int) $mapping['competency_id'];
        }

        $name = $mapping['competency_name'] ?? null;
        if (!$name) {
            return null;
        }

        return $competencyIdMap[$name]
            ?? Competency::where('organization_id', $orgId)
                ->where('name', $name)
                ->value('id');
    }
}
