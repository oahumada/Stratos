<?php

namespace App\Jobs;

use App\Models\ScenarioRoleCompetency;
use App\Services\Intelligence\StratosIntelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Analyzes a PROFICIENCY GAP (Talent Planning domain) for a specific role-competency linkage.
 *
 * This job operates in the Talent Intelligence domain, not Workforce Planning.
 * It analyzes the difference between a competency's required_level and current_level
 * for a specific role, then generates closure strategy recommendations via IA.
 *
 * For headcount/dotacional gaps, see WorkforceDemandLine and ScenarioSkillDemand.
 * For the domain distinction, see: docs/STRATOS_DOMINIOS_WFP_VS_TALENT.md
 */
class AnalyzeTalentGap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $scenarioRoleCompetencyId;

    /**
     * Create a new job instance.
     *
     * @param  int  $scenarioRoleCompetencyId  The ID of the specific gap (Role <-> Competency linkage)
     */
    public function __construct(int $scenarioRoleCompetencyId)
    {
        $this->scenarioRoleCompetencyId = $scenarioRoleCompetencyId;
    }

    /**
     * Execute the job.
     */
    public function handle(StratosIntelService $intelService, \App\Services\Intelligence\MarketIntelligenceService $marketService, \App\Services\ScenarioAnalyticsService $scenarioAnalytics): void
    {
        Log::info("Starting AnalyzeTalentGap job for ID: {$this->scenarioRoleCompetencyId}");

        // 1. Fetch the data
        $gapRecord = ScenarioRoleCompetency::with(['role', 'competency'])->find($this->scenarioRoleCompetencyId);

        if (! $gapRecord) {
            Log::warning("Gap record not found: {$this->scenarioRoleCompetencyId}");

            return;
        }

        // Fetch real current level from employee alignment
        $currentLevel = $this->determineCurrentLevel($gapRecord);
        $gapSize = max(0, $gapRecord->required_level - $currentLevel);

        if ($gapSize === 0) {
            Log::info("No gap to analyze for ID: {$this->scenarioRoleCompetencyId}");

            return;
        }

        // 2. Format payload for Python Service
        $payload = [
            'role_context' => [
                'role_id' => $gapRecord->role_id,
                'role_name' => $gapRecord->role->role->name ?? 'Unknown Role',
                'design_purpose' => $gapRecord->role->role->description ?? 'No description provided',
            ],
            'competency_context' => [
                'competency_name' => $gapRecord->competency->name ?? 'Unknown Competency',
                'required_level' => $gapRecord->required_level,
                'current_level' => $currentLevel,
                'gap_size' => $gapSize,
            ],
            'talent_context' => [
                'current_headcount' => 1,
                'talent_status' => 'Unknown',
            ],
            'market_context' => $marketService->getRoleMarketContext($gapRecord->role_id),
        ];

        // 3. Call the Intelligence Service
        $recommendation = $intelService->analyzeGap($payload);

        if ($recommendation) {
            // 4. Save the strategy
            Log::info("Saving IA strategy for record: {$this->scenarioRoleCompetencyId}");

            $gapRecord->update([
                'suggested_strategy' => $recommendation['strategy'] ?? 'Unknown',
                'strategy_rationale' => $recommendation['reasoning_summary'] ?? '',
                'ia_confidence_score' => $recommendation['confidence_score'] ?? 0,
                'ia_action_plan' => $recommendation['action_plan'] ?? [],
            ]);

            // 5. Update Official Strategies (ScenarioClosureStrategy)
            $this->syncToOfficialStrategies($gapRecord, $recommendation);
        }
    }

    /**
     * Syncs the AI recommendation to the official closure strategies table.
     */
    private function syncToOfficialStrategies(ScenarioRoleCompetency $gapRecord, array $recommendation): void
    {
        // Find associated skills for this competency mapping
        $skills = \App\Models\ScenarioRoleSkill::where('scenario_id', $gapRecord->scenario_id)
            ->where('role_id', $gapRecord->role_id)
            ->where('competency_id', $gapRecord->competency_id)
            ->get();

        foreach ($skills as $skill) {
            $baseRole = \App\Models\ScenarioRole::find($gapRecord->role_id);
            $roleId = $baseRole ? $baseRole->role_id : null;

            \App\Models\ScenarioClosureStrategy::updateOrCreate(
                [
                    'scenario_id' => $gapRecord->scenario_id,
                    'skill_id' => $skill->skill_id,
                    'role_id' => $roleId,
                ],
                [
                    'strategy' => strtolower($recommendation['strategy'] ?? 'build'),
                    'strategy_name' => $recommendation['strategy'] ?? 'AI Recommendation',
                    'description' => $recommendation['reasoning_summary'] ?? '',
                    'ia_confidence_score' => $recommendation['confidence_score'] ?? 0,
                    'ia_strategy_rationale' => $recommendation['reasoning_summary'] ?? '',
                    'is_ia_generated' => true,
                    'action_items' => $recommendation['action_plan'] ?? [],
                    'status' => 'proposed',
                ]
            );
        }
    }

    /**
     * Determine the current average level of the competency for the role's incumbents.
     */
    private function determineCurrentLevel(ScenarioRoleCompetency $gapRecord): float
    {
        $scenarioRole = \App\Models\ScenarioRole::find($gapRecord->role_id);
        if (! $scenarioRole || ! $scenarioRole->role_id) {
            return 0;
        }

        // Get all skills under this competency
        $skillIds = \DB::table('competency_skills')
            ->where('competency_id', $gapRecord->competency_id)
            ->pluck('skill_id');

        if ($skillIds->isEmpty()) {
            return 0;
        }

        // 1. Try to get average proficiency of High Potential incumbents
        // Try to use scenario cache aggregates to avoid DB hits
        // Use the ScenarioAnalyticsService from the container to read precalculated aggregates
        $scenarioAnalytics = app(\App\Services\ScenarioAnalyticsService::class);
        try {
            $scenarioAnalytics->ensureScenarioCache($gapRecord->scenario_id);
            $cache = $scenarioAnalytics->getScenarioCache($gapRecord->scenario_id);

            // High potential averages
            $hipoValues = [];
            foreach ($skillIds as $sid) {
                $key = "{$scenarioRole->role_id}:{$sid}";
                if (!empty($cache['people_role_skills_hipo_avg'][$key])) {
                    $hipoValues[] = (float) $cache['people_role_skills_hipo_avg'][$key];
                }
            }

            if (!empty($hipoValues)) {
                $hipoAvg = array_sum($hipoValues) / count($hipoValues);
                Log::info('Using High Potential talent average (cache) for gap analysis', ['role_id' => $scenarioRole->role_id, 'avg' => $hipoAvg]);
                return round((float) $hipoAvg, 1);
            }

            // Fallback to overall averages from cache
            $vals = [];
            foreach ($skillIds as $sid) {
                $key = "{$scenarioRole->role_id}:{$sid}";
                if (!empty($cache['people_role_skills_avg'][$key])) {
                    $vals[] = (float) $cache['people_role_skills_avg'][$key];
                }
            }

            if (!empty($vals)) {
                $avg = array_sum($vals) / count($vals);
                return round((float) $avg, 1);
            }
        } catch (\Throwable $e) {
            // proceed to DB fallback
            $cache = null;
        }

        // 1. Try DB high-potential average
        $hipoAvg = \DB::table('people_role_skills')
            ->join('people', 'people_role_skills.people_id', '=', 'people.id')
            ->where('people_role_skills.role_id', $scenarioRole->role_id)
            ->whereIn('people_role_skills.skill_id', $skillIds)
            ->where('people_role_skills.is_active', true)
            ->where('people.is_high_potential', true)
            ->avg('people_role_skills.current_level');

        if ($hipoAvg !== null) {
            Log::info('Using High Potential talent average for gap analysis', ['role_id' => $scenarioRole->role_id, 'avg' => $hipoAvg]);

            return round((float) $hipoAvg, 1);
        }

        // 2. Fallback: Average proficiency of all incumbents in those skills
        $avg = \DB::table('people_role_skills')
            ->where('role_id', $scenarioRole->role_id)
            ->whereIn('skill_id', $skillIds)
            ->where('is_active', true)
            ->avg('current_level');

        return round((float) $avg, 1);
    }
}
