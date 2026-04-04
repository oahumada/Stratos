<?php

namespace App\Services;

use App\Models\Competency;
use App\Models\Scenario;
use App\Models\ScenarioClosureStrategy;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Motor de recomendaciones de palancas para cierre de brechas de talento.
 *
 * Implementa el framework AIHR de palancas (HIRE/RESKILL/ROTATE/TRANSFER/
 * CONTINGENT/AUTOMATE/HYBRID_TALENT) mapeadas al esquema 6Bs del DB
 * (buy/build/bind/bridge/borrow/bot).
 *
 * Cada recomendación incluye un racional explicable con la justificación
 * basada en las métricas de brecha, criticidad y contexto organizacional.
 */
class ClosureStrategyMotor
{
    /**
     * Estrategias disponibles con su código DB (6Bs) y etiqueta AIHR.
     *
     * @var array<string, array{db_code: string, label: string, base_cost: int, base_weeks: int, base_probability: float}>
     */
    private const LEVERS = [
        'HIRE'          => ['db_code' => 'buy',    'label' => 'Contratación externa (HIRE)',     'base_cost' => 50000, 'base_weeks' => 12, 'base_probability' => 0.70],
        'RESKILL'       => ['db_code' => 'build',  'label' => 'Capacitación interna (RESKILL)',  'base_cost' => 8000,  'base_weeks' => 16, 'base_probability' => 0.75],
        'ROTATE'        => ['db_code' => 'bind',   'label' => 'Rotación interna (ROTATE)',       'base_cost' => 2000,  'base_weeks' => 4,  'base_probability' => 0.65],
        'TRANSFER'      => ['db_code' => 'bridge', 'label' => 'Transferencia de área (TRANSFER)', 'base_cost' => 3000, 'base_weeks' => 6,  'base_probability' => 0.68],
        'CONTINGENT'    => ['db_code' => 'borrow', 'label' => 'Talento contingente (CONTINGENT)', 'base_cost' => 20000, 'base_weeks' => 8, 'base_probability' => 0.80],
        'AUTOMATE'      => ['db_code' => 'bot',    'label' => 'Automatización / IA (AUTOMATE)',  'base_cost' => 15000, 'base_weeks' => 10, 'base_probability' => 0.72],
        'HYBRID_TALENT' => ['db_code' => 'borrow', 'label' => 'Talento híbrido (HYBRID_TALENT)', 'base_cost' => 12000, 'base_weeks' => 6,  'base_probability' => 0.78],
    ];

    /**
     * Genera recomendaciones de palancas para todas las brechas de un escenario
     * y las persiste en `scenario_closure_strategies`.
     *
     * @return array{generated: int, gaps_analyzed: int, recommendations: array<int, array<string, mixed>>}
     */
    public function generateForScenario(Scenario $scenario): array
    {
        $gaps = ScenarioRoleCompetency::with(['role', 'competency'])
            ->where('scenario_id', $scenario->id)
            ->get();

        $allRecommendations = [];
        $generated = 0;

        DB::transaction(function () use ($scenario, $gaps, &$allRecommendations, &$generated) {
            foreach ($gaps as $gap) {
                $recs = $this->generateForGap($gap);

                foreach ($recs as $rec) {
                    ScenarioClosureStrategy::updateOrCreate(
                        [
                            'scenario_id' => $scenario->id,
                            'role_id'     => $gap->role->role_id ?? null,
                            'strategy'    => $rec['db_code'],
                        ],
                        [
                            'strategy_name'       => $rec['label'],
                            'description'         => $rec['rationale'],
                            'estimated_cost'      => $rec['estimated_cost'],
                            'estimated_time_weeks' => $rec['estimated_weeks'],
                            'success_probability' => $rec['success_probability'],
                            'risk_level'          => $rec['risk_level'],
                            'action_items'        => $rec['action_items'],
                            'ia_confidence_score' => $rec['confidence_score'],
                            'ia_strategy_rationale' => $rec['rationale'],
                            'is_ia_generated'     => false,
                            'status'              => 'proposed',
                        ]
                    );
                    $generated++;
                }

                $allRecommendations[] = [
                    'gap_id'          => $gap->id,
                    'competency'      => $gap->competency?->name ?? 'N/A',
                    'required_level'  => $gap->required_level,
                    'recommendations' => $recs,
                ];
            }
        });

        return [
            'generated'        => $generated,
            'gaps_analyzed'    => $gaps->count(),
            'recommendations'  => $allRecommendations,
        ];
    }

    /**
     * Genera recomendaciones de palancas para una brecha específica.
     * Retorna un array ordenado por prioridad (mayor primero).
     *
     * @return array<int, array<string, mixed>>
     */
    public function generateForGap(ScenarioRoleCompetency $gap): array
    {
        $currentLevel  = $this->estimateCurrentLevel($gap);
        $gapSize       = max(0, $gap->required_level - $currentLevel);
        $isCore        = (bool) $gap->is_core;
        $isReferent    = (bool) $gap->is_referent;
        $competencyName = $gap->competency?->name ?? 'competencia';

        if ($gapSize === 0) {
            return [];
        }

        $candidates = [];

        // — HIRE: brecha grande (≥3) o competencia core crítica
        if ($gapSize >= 3 || ($gapSize >= 2 && $isCore)) {
            $candidates[] = $this->buildRecommendation(
                'HIRE',
                $gapSize,
                $isCore,
                $competencyName,
                "La brecha de {$gapSize} niveles en «{$competencyName}» supera la capacidad de desarrollo interno. " .
                "Se recomienda contratación externa de un perfil con nivel {$gap->required_level} o superior. " .
                ($isCore ? 'Es competencia CORE del rol, por lo que el impacto en operaciones es alto.' : '')
            );
        }

        // — RESKILL: brecha traiable (1-2 niveles)
        if ($gapSize <= 2 && $gapSize >= 1) {
            $candidates[] = $this->buildRecommendation(
                'RESKILL',
                $gapSize,
                $isCore,
                $competencyName,
                "Una brecha de {$gapSize} nivel(es) en «{$competencyName}» es alcanzable con un plan de capacitación interno. " .
                "Se recomienda programa de upskilling de " . ($gapSize === 1 ? '8-12' : '12-20') . " semanas con mentoring. " .
                ($isCore ? 'Priorizar como competencia CORE del rol.' : 'Puede combinarse con rotación de proyectos.')
            );
        }

        // — ROTATE: brecha pequeña (≤1) y no es core
        if ($gapSize <= 1 && !$isCore) {
            $candidates[] = $this->buildRecommendation(
                'ROTATE',
                $gapSize,
                $isCore,
                $competencyName,
                "Brecha mínima de {$gapSize} nivel en «{$competencyName}». " .
                "Se recomienda rotación interna de un talento con perfil similar. " .
                'Bajo costo y riesgo mínimo. Ideal como primer paso antes de reclutar externamente.'
            );
        }

        // — TRANSFER: brecha moderada, no referente
        if ($gapSize <= 2 && !$isReferent) {
            $candidates[] = $this->buildRecommendation(
                'TRANSFER',
                $gapSize,
                $isCore,
                $competencyName,
                "Existe oportunidad de movilidad interna para cubrir la brecha en «{$competencyName}». " .
                'Se recomienda identificar talento en otras áreas con competencias transferibles. ' .
                'Beneficia la retención y reduce costo versus contratación externa.'
            );
        }

        // — CONTINGENT: cubre mientras se desarrolla la solución permanente
        if (!$isCore || $gapSize <= 2) {
            $candidates[] = $this->buildRecommendation(
                'CONTINGENT',
                $gapSize,
                $isCore,
                $competencyName,
                "Para cubrir la brecha en «{$competencyName}» de forma inmediata, " .
                'se recomienda talento contingente (freelance / consultor / staff augmentation). ' .
                'Permite iniciar operaciones mientras se ejecuta la solución permanente (HIRE o RESKILL).'
            );
        }

        // — AUTOMATE: si la competencia es técnica/repetitiva
        if ($this->isAutomatable($competencyName)) {
            $candidates[] = $this->buildRecommendation(
                'AUTOMATE',
                $gapSize,
                $isCore,
                $competencyName,
                "La competencia «{$competencyName}» tiene características automatizables. " .
                'Se recomienda evaluar herramientas de IA o RPA para cubrir parcialmente la brecha. ' .
                'Reduce la dependencia de talento escaso y acelera la ejecución operativa.'
            );
        }

        // — HYBRID_TALENT: brecha de 1 nivel, no referente — augmentar con IA
        if ($gapSize === 1 && !$isReferent) {
            $candidates[] = $this->buildRecommendation(
                'HYBRID_TALENT',
                $gapSize,
                $isCore,
                $competencyName,
                "La brecha de 1 nivel en «{$competencyName}» puede cerrarse con apalancamiento híbrido humano+IA. " .
                'Un colaborador con nivel ' . ($gap->required_level - 1) . ' asistido por herramientas de IA alcanza el nivel requerido. ' .
                'Estrategia de menor fricción y mayor velocidad de adopción.'
            );
        }

        // Ordenar por priority_score descendente
        usort($candidates, fn ($a, $b) => $b['priority_score'] <=> $a['priority_score']);

        // Retornar máximo 3 recomendaciones por gap
        return array_slice($candidates, 0, 3);
    }

    /**
     * Construye el objeto de recomendación con métricas y rationale.
     *
     * @return array<string, mixed>
     */
    private function buildRecommendation(
        string $leverKey,
        int $gapSize,
        bool $isCore,
        string $competencyName,
        string $rationale
    ): array {
        $lever = self::LEVERS[$leverKey];

        // Ajustar estimaciones según el tamaño de la brecha
        $costMultiplier     = 1 + ($gapSize - 1) * 0.3;
        $weeksMultiplier    = 1 + ($gapSize - 1) * 0.2;
        $estimatedCost      = (int) ($lever['base_cost'] * $costMultiplier);
        $estimatedWeeks     = (int) ($lever['base_weeks'] * $weeksMultiplier);
        $successProbability = min(0.95, $lever['base_probability'] - ($gapSize - 1) * 0.05);

        // Risk level según tamaño de brecha y criticidad
        $riskLevel = match (true) {
            $gapSize >= 3 && $isCore  => 'high',
            $gapSize >= 2 || $isCore  => 'medium',
            default                   => 'low',
        };

        // Priority score: ponderación impacto + costo-tiempo + probabilidad de éxito
        $impactScore     = ($isCore ? 40 : 20) * min(1.0, $gapSize / 3);
        $costTimeScore   = 30 * (1 - ($estimatedCost / 100000)) * (1 - ($estimatedWeeks / 52));
        $probabilityScore = 30 * $successProbability;
        $priorityScore   = (int) round($impactScore + $costTimeScore + $probabilityScore);

        return [
            'lever'               => $leverKey,
            'db_code'             => $lever['db_code'],
            'label'               => $lever['label'],
            'rationale'           => $rationale,
            'estimated_cost'      => $estimatedCost,
            'estimated_weeks'     => $estimatedWeeks,
            'success_probability' => round($successProbability, 2),
            'risk_level'          => $riskLevel,
            'priority_score'      => $priorityScore,
            'confidence_score'    => round($successProbability * 0.9, 2),
            'action_items'        => $this->getActionItems($leverKey, $competencyName, $gapSize),
        ];
    }

    /**
     * Estima el nivel actual promedio de la competencia en el rol.
     */
    private function estimateCurrentLevel(ScenarioRoleCompetency $gap): float
    {
        if (! $gap->role) {
            return 0;
        }

        $skillIds = DB::table('competency_skills')
            ->where('competency_id', $gap->competency_id)
            ->pluck('skill_id');

        if ($skillIds->isEmpty()) {
            return 0;
        }

        $avg = DB::table('people_role_skills')
            ->where('role_id', $gap->role->role_id)
            ->whereIn('skill_id', $skillIds)
            ->where('is_active', true)
            ->avg('current_level');

        return round((float) ($avg ?? 0), 1);
    }

    /**
     * Determina si una competencia es candidata a automatización parcial.
     */
    private function isAutomatable(string $competencyName): bool
    {
        $automatable = [
            'análisis de datos', 'data analysis', 'reporting', 'reportes',
            'procesamiento', 'processing', 'automatización', 'testing',
            'qa', 'monitoreo', 'monitoring', 'reconciliación', 'reconciliation',
            'documentación', 'documentation', 'clasificación', 'classification',
        ];

        $name = strtolower($competencyName);

        foreach ($automatable as $keyword) {
            if (str_contains($name, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna los pasos de acción para cada palanca.
     *
     * @return array<int, string>
     */
    private function getActionItems(string $leverKey, string $competencyName, int $gapSize): array
    {
        return match ($leverKey) {
            'HIRE' => [
                "Definir perfil de búsqueda con nivel {$gapSize}+ en {$competencyName}",
                'Publicar vacante en canales especializados (LinkedIn, Bumeran, referidos)',
                'Evaluar candidatos con assessment técnico validado por el equipo',
                'Incorporar con plan de onboarding de 30/60/90 días',
            ],
            'RESKILL' => [
                "Identificar colaboradores con potencial de desarrollo en {$competencyName}",
                'Diseñar ruta de aprendizaje con LMS (cursos + práctica supervisada)',
                'Asignar mentor interno o coach externo especializado',
                'Evaluar progreso cada 4 semanas con assessment de nivel',
            ],
            'ROTATE' => [
                "Mapear colaboradores internos con competencia en {$competencyName}",
                'Coordinar con líderes de área la disponibilidad de rotación',
                'Definir periodo de rotación y objetivos de transferencia de conocimiento',
                'Formalizar movilidad con acuerdo de desempeño temporal',
            ],
            'TRANSFER' => [
                "Realizar inventario de talento con perfil en {$competencyName}",
                'Evaluar fit cultural y motivación del candidato interno',
                'Gestionar transferencia con RRHH (cambio de posición, compensación)',
                'Definir plan de adaptación al nuevo equipo/área',
            ],
            'CONTINGENT' => [
                "Publicar brief de proyecto para perfil con nivel {$gapSize}+ en {$competencyName}",
                'Evaluar 3 opciones: freelance, consultora, staff augmentation',
                'Contratar con alcance y entregables definidos (SoW)',
                'Gestionar transferencia de conocimiento al cierre del contrato',
            ],
            'AUTOMATE' => [
                "Mapear tareas repetitivas en {$competencyName} candidatas a automatizar",
                'Evaluar herramientas de IA/RPA disponibles en el stack actual',
                'Implementar piloto de automatización con métricas de adopción',
                'Capacitar equipo en supervisión y mejora del proceso automatizado',
            ],
            'HYBRID_TALENT' => [
                "Identificar colaborador con nivel " . ($gapSize) . " en {$competencyName} para aumentar con IA",
                'Seleccionar herramientas de asistencia IA para el flujo de trabajo',
                'Diseñar protocolo de trabajo humano+IA con métricas de calidad',
                'Revisar desempeño a 30 días y ajustar nivel de asistencia IA',
            ],
            default => [],
        };
    }
}
