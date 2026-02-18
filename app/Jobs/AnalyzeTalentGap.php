<?php

namespace App\Jobs;

use App\Services\Intelligence\GapAnalysisService;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeTalentGap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $scenarioRoleCompetencyId;

    /**
     * Create a new job instance.
     *
     * @param int $scenarioRoleCompetencyId The ID of the specific gap (Role <-> Competency linkage)
     */
    public function __construct(int $scenarioRoleCompetencyId)
    {
        $this->scenarioRoleCompetencyId = $scenarioRoleCompetencyId;
    }

    /**
     * Execute the job.
     */
    public function handle(GapAnalysisService $intelService): void
    {
        Log::info("Starting AnalyzeTalentGap job for ID: {$this->scenarioRoleCompetencyId}");

        // 1. Fetch the data
        $gapRecord = ScenarioRoleCompetency::with(['role', 'competency'])->find($this->scenarioRoleCompetencyId);
        
        if (!$gapRecord) {
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
                'role_name' => $gapRecord->role->name ?? 'Unknown Role',
                'design_purpose' => $gapRecord->role->description ?? 'No description provided'
            ],
            'competency_context' => [
                'competency_name' => $gapRecord->competency->name ?? 'Unknown Competency',
                'required_level' => $gapRecord->required_level,
                'current_level' => $currentLevel,
                'gap_size' => $gapSize
            ],
            'talent_context' => [
                'current_headcount' => 1,
                'talent_status' => 'Unknown'
            ],
            'market_context' => null
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
                'ia_action_plan' => $recommendation['action_plan'] ?? []
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
        if (!$scenarioRole || !$scenarioRole->role_id) {
            return 0;
        }

        // Get all skills under this competency
        $skillIds = \DB::table('competency_skills')
            ->where('competency_id', $gapRecord->competency_id)
            ->pluck('skill_id');

        if ($skillIds->isEmpty()) {
            return 0;
        }

        // Average proficiency of incumbents in those skills
        $avg = \DB::table('people_role_skills')
            ->where('role_id', $scenarioRole->role_id)
            ->whereIn('skill_id', $skillIds)
            ->where('is_active', true)
            ->avg('current_level');

        return round((float) $avg, 1);
    }
}
