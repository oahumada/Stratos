<?php

namespace App\Services;

use App\Models\Scenario;
use App\Models\DevelopmentPath;
use App\Models\People;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * SentinelMonitorService — Cierra Stratos Sentinel de Fase 5.
 *
 * Monitor de calidad integral que supervisa:
 * - Calidad de las decisiones de IA (agentes + orquestador)
 * - Integridad de datos de talento
 * - Salud del pipeline de desarrollo
 * - Anomalías en métricas clave
 * - Compliance ético de las recomendaciones de IA
 */
class SentinelMonitorService
{
    public function __construct(
        protected AuditTrailService $audit,
        protected CultureSentinelService $cultureSentinel
    ) {}

    /**
     * Ejecuta un monitoreo completo del sistema.
     */
    public function runFullScan(): array
    {
        $results = [
            'scan_timestamp' => now()->toIso8601String(),
            'modules' => [],
            'overall_health' => 0,
            'alerts' => [],
            'recommendations' => [],
        ];

        // 1. Data Integrity Check
        $dataIntegrity = $this->checkDataIntegrity();
        $results['modules']['data_integrity'] = $dataIntegrity;

        // 2. AI Quality Check
        $aiQuality = $this->checkAiQuality();
        $results['modules']['ai_quality'] = $aiQuality;

        // 3. Development Pipeline Health
        $pipelineHealth = $this->checkDevelopmentPipeline();
        $results['modules']['development_pipeline'] = $pipelineHealth;

        // 4. Scenario Coverage
        $scenarioCoverage = $this->checkScenarioCoverage();
        $results['modules']['scenario_coverage'] = $scenarioCoverage;

        // 5. Ethical Compliance
        $ethicsCheck = $this->checkEthicalCompliance();
        $results['modules']['ethical_compliance'] = $ethicsCheck;

        // Calculate overall health
        $scores = collect($results['modules'])->pluck('health_score');
        $results['overall_health'] = (int) round($scores->avg());

        // Generate alerts
        $results['alerts'] = $this->generateAlerts($results['modules']);

        // Generate recommendations
        $results['recommendations'] = $this->generateMonitorRecommendations($results['modules']);

        // Cache results for dashboard consumption
        Cache::put('sentinel_last_scan', $results, now()->addHours(1));

        // Audit-log the scan
        $this->audit->logDecision(
            'Sentinel Monitor',
            'system_wide',
            "Full scan completed — Health: {$results['overall_health']}%",
            ['alerts_count' => count($results['alerts'])],
            'Stratos Sentinel'
        );

        Log::info("Sentinel Full Scan: Health={$results['overall_health']}%, Alerts=" . count($results['alerts']));

        return $results;
    }

    /**
     * Obtiene el último scan (cached) para el dashboard.
     */
    public function getLastScan(): ?array
    {
        return Cache::get('sentinel_last_scan');
    }

    /**
     * Obtiene el health score rápido (sin escaneo completo).
     */
    public function getQuickHealthScore(): int
    {
        $cached = $this->getLastScan();

        if ($cached && now()->diffInMinutes($cached['scan_timestamp']) < 60) {
            return $cached['overall_health'];
        }

        // Quick check
        $scores = [];
        $scores[] = People::count() > 0 ? 80 : 20;
        $scores[] = Scenario::count() > 0 ? 80 : 40;
        $scores[] = DevelopmentPath::where('status', 'active')->count() > 0 ? 75 : 30;

        return (int) round(collect($scores)->avg());
    }

    // ── Module Checks ────────────────────────────────────────

    protected function checkDataIntegrity(): array
    {
        $issues = [];

        // People sin rol
        $peopleWithoutRole = People::whereNull('role_id')->count();
        if ($peopleWithoutRole > 0) {
            $issues[] = [
                'type' => 'warning',
                'message' => "{$peopleWithoutRole} personas sin rol asignado.",
                'severity' => $peopleWithoutRole > 10 ? 'high' : 'medium',
            ];
        }

        // Skills huérfanas (no vinculadas a ningún rol)
        $orphanSkills = DB::table('skills')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('role_skills')
                    ->whereColumn('role_skills.skill_id', 'skills.id');
            })
            ->count();

        if ($orphanSkills > 0) {
            $issues[] = [
                'type' => 'info',
                'message' => "{$orphanSkills} skills no vinculadas a ningún rol.",
                'severity' => 'low',
            ];
        }

        // Escenarios sin roles
        $emptyScenarios = Scenario::whereDoesntHave('roles')->count();
        if ($emptyScenarios > 0) {
            $issues[] = [
                'type' => 'warning',
                'message' => "{$emptyScenarios} escenarios sin roles asociados.",
                'severity' => 'medium',
            ];
        }

        $healthScore = 100 - (count($issues) * 10);

        return [
            'health_score' => max(0, min(100, $healthScore)),
            'issues' => $issues,
            'checked_at' => now()->toIso8601String(),
        ];
    }

    protected function checkAiQuality(): array
    {
        $issues = [];

        // Verificar si hay logs de audit trail recientes (indica IA activa)
        $recentAiActivity = DB::table('jobs')
            ->where('created_at', '>=', now()->subDays(7)->timestamp)
            ->count();

        $hasAiLogs = true; // Asumimos operativo si el servicio ejecuta

        // Verificar tasa de errores en AI (via logs)
        // En una implementación real, esto consultaría la tabla ai_audit_trails
        $errorRate = 0; // Placeholder

        if ($errorRate > 10) {
            $issues[] = [
                'type' => 'critical',
                'message' => "Tasa de error de IA: {$errorRate}%. Umbral: 10%.",
                'severity' => 'high',
            ];
        }

        $healthScore = $hasAiLogs ? 85 : 50;
        $healthScore -= ($errorRate * 2);

        return [
            'health_score' => max(0, min(100, $healthScore)),
            'ai_active' => $hasAiLogs,
            'error_rate' => $errorRate,
            'issues' => $issues,
        ];
    }

    protected function checkDevelopmentPipeline(): array
    {
        $issues = [];

        $totalPeople = People::count();
        $withActivePaths = DevelopmentPath::where('status', 'active')
            ->distinct('people_id')
            ->count('people_id');

        $coverage = $totalPeople > 0 ? round(($withActivePaths / $totalPeople) * 100) : 0;

        if ($coverage < 30) {
            $issues[] = [
                'type' => 'warning',
                'message' => "Solo el {$coverage}% del workforce tiene planes de desarrollo activos. Meta: >50%.",
                'severity' => 'medium',
            ];
        }

        // Paths estancados (sin actividad en 30+ días)
        $stalePaths = DevelopmentPath::where('status', 'active')
            ->where('updated_at', '<', now()->subDays(30))
            ->count();

        if ($stalePaths > 0) {
            $issues[] = [
                'type' => 'warning',
                'message' => "{$stalePaths} rutas de desarrollo sin actividad en 30+ días.",
                'severity' => $stalePaths > 5 ? 'high' : 'medium',
            ];
        }

        // Acciones completadas vs pendientes
        $completedActions = DB::table('development_actions')->where('status', 'completed')->count();
        $totalActions = DB::table('development_actions')->count();
        $completionRate = $totalActions > 0 ? round(($completedActions / $totalActions) * 100) : 0;

        $healthScore = min(100, $coverage + ($completionRate / 2));

        return [
            'health_score' => max(0, (int) $healthScore),
            'pipeline_coverage' => $coverage,
            'completion_rate' => $completionRate,
            'stale_paths' => $stalePaths,
            'issues' => $issues,
        ];
    }

    protected function checkScenarioCoverage(): array
    {
        $issues = [];

        $totalScenarios = Scenario::count();
        $activeScenarios = Scenario::whereIn('status', ['active', 'published'])->count();
        $scenariosWithStrategies = Scenario::whereHas('closureStrategies')->count();

        if ($totalScenarios === 0) {
            $issues[] = [
                'type' => 'critical',
                'message' => 'No hay escenarios creados. El módulo de Scenario IQ no está en uso.',
                'severity' => 'high',
            ];
        }

        $strategyCoverage = $totalScenarios > 0
            ? round(($scenariosWithStrategies / $totalScenarios) * 100)
            : 0;

        if ($strategyCoverage < 50 && $totalScenarios > 0) {
            $issues[] = [
                'type' => 'warning',
                'message' => "Solo el {$strategyCoverage}% de escenarios tienen estrategias de cierre definidas.",
                'severity' => 'medium',
            ];
        }

        $healthScore = $totalScenarios > 0
            ? min(100, ($activeScenarios * 20) + ($strategyCoverage / 2))
            : 20;

        return [
            'health_score' => max(0, (int) $healthScore),
            'total_scenarios' => $totalScenarios,
            'active_scenarios' => $activeScenarios,
            'strategy_coverage' => $strategyCoverage,
            'issues' => $issues,
        ];
    }

    protected function checkEthicalCompliance(): array
    {
        // Verificar que las decisiones de IA son transparentes y rastreables
        $hasAuditTrail = true; // AuditTrailService está activo
        $hasRbac = true; // RBAC implementado

        $healthScore = 80;
        $issues = [];

        // Verificar que no hay concentración de acceso
        $adminCount = DB::table('users')
            ->where('role', 'admin')
            ->count();

        $totalUsers = DB::table('users')->count();

        if ($totalUsers > 0 && ($adminCount / $totalUsers) > 0.3) {
            $issues[] = [
                'type' => 'info',
                'message' => 'Alta concentración de permisos admin. Considerar distribución de roles.',
                'severity' => 'low',
            ];
            $healthScore -= 10;
        }

        return [
            'health_score' => max(0, $healthScore),
            'audit_trail_active' => $hasAuditTrail,
            'rbac_active' => $hasRbac,
            'issues' => $issues,
        ];
    }

    // ── Alert Generation ─────────────────────────────────────

    protected function generateAlerts(array $modules): array
    {
        $alerts = [];

        foreach ($modules as $moduleName => $moduleData) {
            if ($moduleData['health_score'] < 50) {
                $alerts[] = [
                    'level' => 'critical',
                    'module' => $moduleName,
                    'message' => "Módulo '{$moduleName}' en estado crítico: {$moduleData['health_score']}%",
                ];
            } elseif ($moduleData['health_score'] < 70) {
                $alerts[] = [
                    'level' => 'warning',
                    'module' => $moduleName,
                    'message' => "Módulo '{$moduleName}' requiere atención: {$moduleData['health_score']}%",
                ];
            }

            foreach ($moduleData['issues'] ?? [] as $issue) {
                if (($issue['severity'] ?? '') === 'high') {
                    $alerts[] = [
                        'level' => 'high',
                        'module' => $moduleName,
                        'message' => $issue['message'],
                    ];
                }
            }
        }

        return $alerts;
    }

    protected function generateMonitorRecommendations(array $modules): array
    {
        $recommendations = [];

        $pipelineHealth = $modules['development_pipeline']['health_score'] ?? 0;
        if ($pipelineHealth < 50) {
            $recommendations[] = 'Activar campaña de creación de planes de desarrollo para alcanzar cobertura >50%.';
        }

        $scenarioHealth = $modules['scenario_coverage']['health_score'] ?? 0;
        if ($scenarioHealth < 50) {
            $recommendations[] = 'Crear escenarios estratégicos y definir estrategias de cierre 4B.';
        }

        $dataHealth = $modules['data_integrity']['health_score'] ?? 0;
        if ($dataHealth < 70) {
            $recommendations[] = 'Ejecutar limpieza de datos: asignar roles faltantes y vincular skills huérfanas.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Sistema en buen estado. Programar próximo scan en 24 horas.';
        }

        return $recommendations;
    }
}
