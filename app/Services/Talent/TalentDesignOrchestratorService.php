<?php

namespace App\Services\Talent;

use App\Models\Scenario;
use App\Models\Roles;
use App\Models\Competency;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\Log;

class TalentDesignOrchestratorService
{
    protected AiOrchestratorService $ai;

    public function __construct(AiOrchestratorService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Orquesta el diseño de talento para un escenario específico.
     * Combina el análisis del Agente Planificador con el catálogo actual.
     */
    public function orchestrate(int $scenarioId): array
    {
        $scenario = Scenario::with([
            'capabilities.competencies.skills'
        ])->findOrFail($scenarioId);

        $orgId = $scenario->organization_id;

        // 1. Obtener catálogo actual de roles y competencias
        $currentRoles = Roles::where('organization_id', $orgId)
            ->where('status', 'active')
            ->get(['id', 'name', 'description'])
            ->toArray();

        $currentCompetencies = Competency::where('organization_id', $orgId)
            ->where('status', 'active')
            ->get(['id', 'name', 'description'])
            ->toArray();

        // 2. Preparar el mapa de lo que el "Paso 1" propuso
        $targetBlueprint = $this->formatBlueprint($scenario);

        // 3. Cargar el prompt especializado
        $promptPath = resource_path('prompt_instructions/talent_design_orchestration_es.md');
        $systemInstructions = file_exists($promptPath) ? file_get_contents($promptPath) : 'Actúa como Diseñador de Roles y Curador de Competencias.';

        $taskPrompt = "Contexto del Escenario: {$scenario->name}\n";
        $taskPrompt .= "Descripción: {$scenario->description}\n\n";
        $taskPrompt .= "### BLUEPRINT ESTRATÉGICO (Sugerido en Paso 1):\n";
        $taskPrompt .= json_encode($targetBlueprint, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        $taskPrompt .= "### CATÁLOGO ACTUAL DE LA EMPRESA:\n";
        $taskPrompt .= "ROLES ACTUALES: " . json_encode($currentRoles, JSON_UNESCAPED_UNICODE) . "\n";
        $taskPrompt .= "COMPETENCIAS ACTUALES: " . json_encode($currentCompetencies, JSON_UNESCAPED_UNICODE) . "\n\n";
        $taskPrompt .= "Por favor, generen la propuesta de diseño de talento siguiendo las instrucciones del sistema.";

        try {
            // Usamos al "Diseñador de Roles" como el agente principal,
            // pero el prompt le pide colaborar con el Curador.
            $result = $this->ai->agentThink('Diseñador de Roles', $taskPrompt, $systemInstructions);

            return [
                'success' => true,
                'proposals' => $result['response'] ?? $result,
                'metadata' => [
                    'scenario_id' => $scenarioId,
                    'agent' => 'Role Designer & Competency Curator Collaboration'
                ]
            ];
        } catch (\Exception $e) {
            Log::error("Error en orquestación de talento: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function formatBlueprint(Scenario $scenario): array
    {
        return $scenario->capabilities->map(function ($cap) {
            return [
                'capability' => $cap->name,
                'competencies' => $cap->competencies->map(function ($comp) {
                    return [
                        'name' => $comp->name,
                        'skills' => $comp->skills->pluck('name')->toArray()
                    ];
                })
            ];
        })->toArray();
    }
}
