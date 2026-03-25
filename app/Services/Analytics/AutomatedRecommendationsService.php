<?php

namespace App\Services\Analytics;

use App\Models\LLMEvaluation;
use App\Services\LLMClient;
use App\Services\RedactionService;
use Illuminate\Support\Facades\Log;

/**
 * AutomatedRecommendationsService
 *
 * Generates intelligent recommendations for:
 * - System optimization
 * - Talent planning
 * - Risk mitigation
 * - Deployment timing
 * - Resource allocation
 *
 * Uses anomaly detection, predictive insights, and LLM reasoning
 * to generate actionable, context-aware recommendations.
 */
class AutomatedRecommendationsService
{
    public function __construct(
        private AnomalyDetectionService $anomalyService,
        private PredictiveInsightsService $predictiveService,
        private MetricsAggregationService $metricsService,
        private LLMClient $llmClient,
        private RedactionService $redactionService
    ) {}

    /**
     * Generate comprehensive AI recommendations for organization
     */
    public function generateComprehensiveRecommendations(string $organizationId): array
    {
        $anomalies = $this->anomalyService->analyzeVerificationMetrics($organizationId);
        $predictions = $this->predictiveService->predictResourceNeeds($organizationId);
        $currentMetrics = $this->metricsService->getCurrentMetrics($organizationId);

        $recommendations = [];

        // System Performance Recommendations
        if (! empty($anomalies)) {
            $recommendations = array_merge(
                $recommendations,
                $this->generatePerformanceRecommendations($anomalies, $currentMetrics)
            );
        }

        // Capacity Planning Recommendations
        if (isset($predictions['status']) && $predictions['status'] !== 'insufficient_data') {
            $recommendations = array_merge(
                $recommendations,
                $this->generateCapacityRecommendations($predictions)
            );
        }

        // Risk Mitigation Recommendations
        $talentAnomalies = $this->anomalyService->analyzeTalentAnomalies($organizationId);
        if (! empty($talentAnomalies)) {
            $recommendations = array_merge(
                $recommendations,
                $this->generateRiskRecommendations($talentAnomalies)
            );
        }

        // Sort by priority
        usort($recommendations, fn ($a, $b) => $this->priorityScore($b) <=> $this->priorityScore($a));

        // Store in database
        $this->storeRecommendations($organizationId, $recommendations);

        return [
            'organization_id' => $organizationId,
            'generated_at' => now()->toIso8601String(),
            'total_recommendations' => count($recommendations),
            'by_priority' => [
                'critical' => count(array_filter($recommendations, fn ($r) => $r['priority'] === 'CRITICAL')),
                'high' => count(array_filter($recommendations, fn ($r) => $r['priority'] === 'HIGH')),
                'medium' => count(array_filter($recommendations, fn ($r) => $r['priority'] === 'MEDIUM')),
            ],
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Generate performance optimization recommendations
     */
    private function generatePerformanceRecommendations(array $anomalies, array $metrics): array
    {
        $recommendations = [];

        // Latency spike
        if (isset($anomalies['avg_latency'])) {
            $recommendations[] = [
                'category' => 'PERFORMANCE',
                'priority' => 'HIGH',
                'title' => 'Latency Spike Detected',
                'description' => 'Current latency exceeds historical average by '.
                    $anomalies['avg_latency'][0]['z_score'].'σ',
                'impact' => 'Slower verification transitions',
                'suggested_actions' => [
                    'Check database query performance',
                    'Monitor CPU and memory usage',
                    'Review slow logs for bottlenecks',
                    'Consider query optimization',
                ],
                'estimated_severity' => 'HIGH',
                'confidence_score' => 0.95,
            ];
        }

        // Compliance drift
        if (isset($anomalies['compliance_score'])) {
            $deviation = $anomalies['compliance_score'][0];
            $recommendations[] = [
                'category' => 'COMPLIANCE',
                'priority' => $deviation['severity'],
                'title' => 'Compliance Score Trending '.$deviation['direction'],
                'description' => 'Compliance score changed from '.
                    round($deviation['old_average'], 2).' to '.
                    round($deviation['new_average'], 2),
                'impact' => 'Audit readiness risk',
                'suggested_actions' => [
                    'Review recent policy changes',
                    'Audit verification logs',
                    'Check for environmental changes',
                    'Consider remediation plan',
                ],
                'estimated_severity' => $deviation['severity'],
                'confidence_score' => 0.92,
            ];
        }

        // System health degradation
        if (isset($anomalies['system_health'])) {
            $health = $anomalies['system_health'][0];
            $recommendations[] = [
                'category' => 'SYSTEM',
                'priority' => $health['severity'],
                'title' => 'System Health Degradation',
                'description' => 'Verification failure rate: '.$health['failure_rate'].'%',
                'impact' => 'Production reliability at risk',
                'suggested_actions' => [
                    $health['recommendation'],
                    'Escalate to infrastructure team',
                    'Review system logs for errors',
                    'Consider emergency patching',
                ],
                'estimated_severity' => $health['severity'],
                'confidence_score' => 0.88,
            ];
        }

        return $recommendations;
    }

    /**
     * Generate capacity planning recommendations
     */
    private function generateCapacityRecommendations(array $predictions): array
    {
        $recommendations = [];

        if (isset($predictions['throughput_trend'])) {
            $trend = $predictions['throughput_trend'];
            if ($trend['utilization_at_capacity']) {
                $recommendations[] = [
                    'category' => 'CAPACITY',
                    'priority' => 'CRITICAL',
                    'title' => 'System at Capacity',
                    'description' => 'Throughput utilization exceeds 85%',
                    'impact' => 'Risk of service degradation',
                    'suggested_actions' => [
                        'Scale infrastructure immediately',
                        'Implement load balancing',
                        'Optimize query performance',
                        'Consider rate limiting',
                    ],
                    'estimated_severity' => 'CRITICAL',
                    'confidence_score' => 0.99,
                ];
            }
        }

        if (isset($predictions['capacity_saturation_date'])) {
            $saturationDate = $predictions['capacity_saturation_date'];
            if ($saturationDate) {
                $daysUntilFull = now()->diffInDays(new \Carbon\Carbon($saturationDate));
                $priority = $daysUntilFull < 7 ? 'CRITICAL' : ($daysUntilFull < 14 ? 'HIGH' : 'MEDIUM');

                $recommendations[] = [
                    'category' => 'CAPACITY',
                    'priority' => $priority,
                    'title' => 'Projected Capacity Saturation',
                    'description' => 'System will reach capacity in approximately '.$daysUntilFull.' days',
                    'impact' => 'Service disruption risk',
                    'suggested_actions' => [
                        'Plan infrastructure scaling',
                        'Begin vendor RFP if external resources needed',
                        'Communicate timeline to stakeholders',
                        'Prepare contingency plan',
                    ],
                    'estimated_severity' => $priority,
                    'confidence_score' => 0.85,
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Generate risk mitigation recommendations
     */
    private function generateRiskRecommendations(array $anomalies): array
    {
        $recommendations = [];

        // Skill gaps
        if (isset($anomalies['skill_gaps'])) {
            $recommendations[] = [
                'category' => 'TALENT',
                'priority' => 'HIGH',
                'title' => 'Critical Skill Gaps Detected',
                'description' => 'Identified '.count($anomalies['skill_gaps']).' critical skill deficiencies',
                'impact' => 'Project delivery risk',
                'suggested_actions' => [
                    'Launch targeted recruitment',
                    'Accelerate training programs',
                    'Consider skill exchanges',
                ],
                'estimated_severity' => 'HIGH',
                'confidence_score' => 0.90,
            ];
        }

        // Role vacancies
        if (isset($anomalies['role_vacancies'])) {
            $recommendations[] = [
                'category' => 'TALENT',
                'priority' => 'HIGH',
                'title' => 'Role Vacancy Risk',
                'description' => 'Potential vacancies in '.count($anomalies['role_vacancies']).' critical roles',
                'impact' => 'Organizational continuity risk',
                'suggested_actions' => [
                    'Develop succession plans',
                    'Cross-train team members',
                    'Begin recruitment early',
                ],
                'estimated_severity' => 'HIGH',
                'confidence_score' => 0.87,
            ];
        }

        return $recommendations;
    }

    /**
     * Calculate priority score for sorting
     */
    private function priorityScore(array $recommendation): int
    {
        return match ($recommendation['priority']) {
            'CRITICAL' => 100,
            'HIGH' => 75,
            'MEDIUM' => 50,
            'LOW' => 25,
            default => 0,
        };
    }

    /**
     * Store recommendations for future reference
     */
    private function storeRecommendations(string $organizationId, array $recommendations): void
    {
        $content = json_encode($recommendations, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $redactedContent = $this->redactionService->redactText($content);

        try {
            LLMEvaluation::create([
                'organization_id' => $organizationId,
                'input_content' => 'Automated Recommendations Generation',
                'output_content' => $redactedContent,
                'context_content' => 'System-generated recommendations',
                'model_name' => 'anomaly_detection_v1',
                'evaluation_score' => null,
                'tags' => ['recommendations', 'ai_generated', 'system_insights'],
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to store recommendations: '.$e->getMessage());
        }
    }

    /**
     * Get AI-enhanced explanations for recommendations (optional LLM call)
     */
    public function getDetailedExplanation(
        string $organizationId,
        array $recommendation,
        bool $useLLM = false
    ): array {
        if (! $useLLM) {
            return $recommendation;
        }

        $prompt = $this->buildExplanationPrompt($recommendation);

        try {
            $response = $this->llmClient->generate(
                prompt: $prompt,
                options: [
                    'system_prompt' => 'You are a system analyst providing detailed explanations of technical recommendations.',
                    'max_tokens' => 500,
                    'temperature' => 0.3,
                ]
            );

            $recommendation['llm_explanation'] = $response['response']['raw_text'] ?? null;
        } catch (\Exception $e) {
            Log::error('LLM explanation generation failed: '.$e->getMessage());
        }

        return $recommendation;
    }

    /**
     * Build prompt for LLM explanation
     */
    private function buildExplanationPrompt(array $recommendation): string
    {
        return sprintf(
            'Provide a detailed technical explanation for this recommendation:\n\n'.
            'Category: %s\n'.
            'Title: %s\n'.
            'Description: %s\n'.
            'Impact: %s\n\n'.
            'Include: root cause analysis, implementation steps, and success metrics.',
            $recommendation['category'],
            $recommendation['title'],
            $recommendation['description'],
            $recommendation['impact']
        );
    }
}
