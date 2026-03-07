<?php

namespace App\Services;

use App\Models\People;
use App\Models\Scenario;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * StratosGuideService — Cierra Stratos Guide de Fase 5.
 *
 * Asistente contextual in-app que proporciona:
 * - Guía paso a paso para cada módulo
 * - Sugerencias proactivas según el contexto del usuario
 * - Tips de productividad y mejores prácticas
 * - Onboarding inteligente para nuevos usuarios
 */
class StratosGuideService
{
    public function __construct(
        protected AiOrchestratorService $orchestrator,
        protected AuditTrailService $audit
    ) {}

    /**
     * Obtiene sugerencias contextuales basadas en el módulo actual del usuario.
     */
    public function getContextualSuggestions(string $module, int $userId, array $context = []): array
    {
        $suggestions = $this->getModuleSuggestions($module);
        $proactiveTips = $this->getProactiveTips($userId, $module);
        $quickActions = $this->getQuickActions($module, $context);

        return [
            'module' => $module,
            'suggestions' => $suggestions,
            'proactive_tips' => $proactiveTips,
            'quick_actions' => $quickActions,
            'onboarding_step' => $this->getOnboardingStep($userId, $module),
        ];
    }

    /**
     * Genera una respuesta contextual para consultas del usuario.
     */
    public function askGuide(string $question, string $module, int $userId): array
    {
        $context = $this->buildContext($module, $userId);

        $prompt = "Actúa como Stratos Guide, el asistente contextual de la plataforma Stratos.
        
        El usuario está en el módulo: {$module}
        Contexto: {$context}
        
        Pregunta del usuario: {$question}
        
        Responde de forma concisa, práctica y orientada a la acción.
        Si la pregunta es sobre cómo hacer algo, da pasos numerados.
        Si es una duda conceptual, explica brevemente y sugiere la acción siguiente.
        
        Responde en formato JSON con: 'answer' (string), 'next_action' (string), 'related_module' (string o null).";

        try {
            $result = $this->orchestrator->agentThink('Stratos Guide', $prompt);

            return [
                'status' => 'success',
                'answer' => $result['response']['answer'] ?? $result['response'] ?? 'Consulta procesada.',
                'next_action' => $result['response']['next_action'] ?? null,
                'related_module' => $result['response']['related_module'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Stratos Guide error: '.$e->getMessage());

            return [
                'status' => 'fallback',
                'answer' => $this->getFallbackAnswer($question, $module),
                'next_action' => null,
                'related_module' => null,
            ];
        }
    }

    /**
     * Marca un step de onboarding como completado.
     */
    public function completeOnboardingStep(int $userId, string $module, string $step): void
    {
        $key = "onboarding_{$userId}";
        $completed = Cache::get($key, []);
        $completed["{$module}_{$step}"] = now()->toIso8601String();
        Cache::put($key, $completed, now()->addMonths(6));
    }

    // ── Module Suggestions ───────────────────────────────────

    protected function getModuleSuggestions(string $module): array
    {
        $suggestions = [
            'scenario_planning' => [
                ['icon' => 'ph-compass', 'title' => 'Crea tu primer escenario', 'description' => 'Comienza definiendo la identidad y el horizonte estratégico.', 'action' => 'create_scenario'],
                ['icon' => 'ph-graph', 'title' => 'Analiza brechas', 'description' => 'Revisa las skills faltantes en tu escenario activo.', 'action' => 'view_gaps'],
                ['icon' => 'ph-strategy', 'title' => 'Define estrategias 4B', 'description' => 'Asigna Build, Buy, Borrow o Bot a cada brecha.', 'action' => 'assign_strategies'],
            ],
            'talento_360' => [
                ['icon' => 'ph-users-four', 'title' => 'Configura un ciclo 360', 'description' => 'Lanza evaluaciones con retroalimentación multidireccional.', 'action' => 'new_cycle'],
                ['icon' => 'ph-chart-bar', 'title' => 'Revisa resultados', 'description' => 'Consulta los resultados de las evaluaciones completadas.', 'action' => 'view_results'],
            ],
            'mi_stratos' => [
                ['icon' => 'ph-user-circle', 'title' => 'Completa tu perfil', 'description' => 'Asegúrate de tener tu ADN y skills actualizados.', 'action' => 'edit_profile'],
                ['icon' => 'ph-path', 'title' => 'Revisa tu ruta', 'description' => 'Consulta tu Learning Path y marca avances.', 'action' => 'view_path'],
            ],
            'people' => [
                ['icon' => 'ph-user-plus', 'title' => 'Agrega colaboradores', 'description' => 'Registra nuevas personas en el sistema.', 'action' => 'add_person'],
                ['icon' => 'ph-identification-card', 'title' => 'Asigna roles', 'description' => 'Vincula personas con sus roles para análisis de brechas.', 'action' => 'assign_role'],
            ],
            'gap_analysis' => [
                ['icon' => 'ph-magnifying-glass', 'title' => 'Ejecuta análisis', 'description' => 'Identifica brechas de skills a nivel individual o departamental.', 'action' => 'run_analysis'],
            ],
            'learning_paths' => [
                ['icon' => 'ph-graduation-cap', 'title' => 'Genera blueprints', 'description' => 'Crea rutas de aprendizaje automáticas con IA.', 'action' => 'generate_blueprint'],
            ],
            'marketplace' => [
                ['icon' => 'ph-storefront', 'title' => 'Explora oportunidades', 'description' => 'Revisa vacantes y proyectos alineados a tu perfil.', 'action' => 'browse_opportunities'],
            ],
            'dashboard' => [
                ['icon' => 'ph-chart-line-up', 'title' => 'KPIs ejecutivos', 'description' => 'Revisa las métricas clave de la organización.', 'action' => 'view_kpis'],
            ],
        ];

        return $suggestions[$module] ?? [
            ['icon' => 'ph-info', 'title' => 'Bienvenido', 'description' => 'Explora las funcionalidades de este módulo.', 'action' => 'explore'],
        ];
    }

    protected function getProactiveTips(int $userId, string $module): array
    {
        $tips = [];

        // Tips basados en actividad del usuario
        $personPeople = People::where('user_id', $userId)->first();

        if ($personPeople) {
            $hasGaps = DB::table('people_role_skills')
                ->where('people_id', $personPeople->id)
                ->whereColumn('current_level', '<', 'required_level')
                ->exists();

            if ($hasGaps && $module === 'mi_stratos') {
                $tips[] = [
                    'type' => 'nudge',
                    'message' => '¡Tienes brechas de skills por cerrar! Revisa tu ruta de desarrollo.',
                    'action' => 'view_gaps',
                ];
            }

            $hasActivePath = DB::table('development_paths')
                ->where('people_id', $personPeople->id)
                ->where('status', 'active')
                ->exists();

            if (! $hasActivePath) {
                $tips[] = [
                    'type' => 'suggestion',
                    'message' => 'Aún no tienes un plan de desarrollo activo. ¿Quieres que generemos uno?',
                    'action' => 'generate_path',
                ];
            }
        }

        return $tips;
    }

    protected function getQuickActions(string $module, array $context): array
    {
        $actions = [
            'scenario_planning' => [
                ['label' => 'Nuevo Escenario', 'route' => '/scenario-planning?action=create', 'icon' => 'ph-plus-circle'],
                ['label' => 'Ver Overview', 'route' => '/scenario-planning?view=overview', 'icon' => 'ph-chart-pie'],
            ],
            'talento_360' => [
                ['label' => 'Nuevo Ciclo', 'route' => '/talento-360/comando?action=new', 'icon' => 'ph-plus-circle'],
            ],
            'people' => [
                ['label' => 'Agregar Persona', 'route' => '/people?action=create', 'icon' => 'ph-user-plus'],
            ],
        ];

        return $actions[$module] ?? [];
    }

    protected function getOnboardingStep(int $userId, string $module): ?array
    {
        $completed = Cache::get("onboarding_{$userId}", []);

        $steps = [
            'scenario_planning' => [
                ['step' => 'create_first', 'title' => 'Crea tu primer escenario', 'order' => 1],
                ['step' => 'add_roles', 'title' => 'Agrega roles al escenario', 'order' => 2],
                ['step' => 'run_analysis', 'title' => 'Ejecuta el análisis de brechas', 'order' => 3],
                ['step' => 'assign_strategies', 'title' => 'Define estrategias de cierre', 'order' => 4],
            ],
            'talento_360' => [
                ['step' => 'configure_cycle', 'title' => 'Configura un ciclo de evaluación', 'order' => 1],
                ['step' => 'launch_cycle', 'title' => 'Lanza el ciclo oficialmente', 'order' => 2],
                ['step' => 'review_results', 'title' => 'Revisa los resultados  360°', 'order' => 3],
            ],
        ];

        $moduleSteps = $steps[$module] ?? [];

        foreach ($moduleSteps as $step) {
            $key = "{$module}_{$step['step']}";
            if (! isset($completed[$key])) {
                return $step;
            }
        }

        return null; // Todo completado
    }

    protected function buildContext(string $module, int $userId): string
    {
        $context = "Módulo: {$module}. ";
        $context .= 'Escenarios activos: '.Scenario::whereIn('status', ['active', 'published'])->count().'. ';
        $context .= 'Total personas: '.People::count().'. ';

        return $context;
    }

    protected function getFallbackAnswer(string $question, string $module): string
    {
        $fallbacks = [
            'scenario_planning' => 'Para trabajar con escenarios, comienza creando uno nuevo desde el botón "+ Nuevo Escenario". Luego agrega roles, analiza brechas y define estrategias de cierre.',
            'talento_360' => 'El módulo 360 te permite crear ciclos de evaluación. Ve a Comando 360 y presiona "Nuevo Ciclo" para empezar.',
            'mi_stratos' => 'Tu portal personal muestra tu rol, brechas, ruta de desarrollo y logros. Revisa cada sección para estar al día.',
        ];

        return $fallbacks[$module]
            ?? 'Puedo ayudarte a navegar la plataforma. Por favor, sé más específico en tu consulta o navega al módulo relevante.';
    }
}
