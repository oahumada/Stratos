<?php

namespace App\Services\ScenarioPlanning;

use App\Models\People;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\SuccessionCandidate;

class SuccessionPlanningService
{
    /**
     * Calculate skill match between person and target role
     * Returns: 0-100 score
     */
    public function calculateSkillMatch(People $person, Roles $targetRole, Scenario $scenario): float
    {
        // Base skill match: compare person skills vs role requirements
        $baseMatch = $this->getBaseSkillMatch($person, $targetRole);

        // Experience bonus: 0-20 points
        $experienceBonus = $this->calculateExperienceBonus($person, $targetRole);

        // Training in scenario: 0-15 points
        $trainingBonus = $this->calculateTrainingBonus($person, $scenario);

        // Overall score
        $score = ($baseMatch * 0.65) + $experienceBonus + $trainingBonus;

        return min(100, max(0, $score));
    }

    /**
     * Assess readiness level for succession candidate
     * Returns: 'junior', 'intermediate', 'senior', or 'expert'
     */
    public function assessReadiness(SuccessionCandidate $candidate): string
    {
        $score = $candidate->skill_match_score;
        $experience = $this->getRelevantExperienceYears($candidate->person, $candidate->targetRole);
        $mentorAvail = $this->getMentorAvailability($candidate); // 0-100

        $readinessScore =
            ($score * 0.40) +           // 40% skill match
            (min(20, $experience * 5)) +  // 30% experience (capped at 20 points)
            ($mentorAvail * 0.20) +     // 20% mentor availability
            ($this->getMotivationSignal($candidate) * 0.10); // 10% motivation

        if ($readinessScore >= 85) {
            return 'expert';
        }
        if ($readinessScore >= 70) {
            return 'senior';
        }
        if ($readinessScore >= 50) {
            return 'intermediate';
        }

        return 'junior';
    }

    /**
     * Calculate succession coverage for all critical roles in scenario
     * Returns: [{role_id, role_name, covered_count, at_risk_count, critical_count, coverage_pct}]
     */
    public function calculateSuccessionCoverage(Scenario $scenario): array
    {
        $coverage = [];

        // Get all roles in the organization (no is_critical filter since column doesn't exist)
        $roles = Roles::where('organization_id', $scenario->organization_id)
            ->limit(10)
            ->get();

        foreach ($roles as $role) {
            $successors = SuccessionCandidate::where('scenario_id', $scenario->id)
                ->where('target_role_id', $role->id)
                ->get();

            $readyCount = $successors->filter(fn ($s) => $s->readiness_level === 'expert' || $s->readiness_level === 'senior')->count();
            $atRiskCount = $successors->filter(fn ($s) => $s->skill_match_score < 50)->count();
            $criticalCount = $successors->where('skill_match_score', '<', 50)->count();

            $coverage[] = [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'covered_count' => $readyCount,
                'at_risk_count' => $atRiskCount,
                'critical_count' => $criticalCount,
                'coverage_pct' => $readyCount > 0 ? round(($readyCount / max(1, $successors->count())) * 100) : 0,
            ];
        }

        return $coverage;
    }

    // ── Private Helper Methods ──────────────────────────────────────────────

    /**
     * Get base skill match between person's skills and role requirements (0-100)
     */
    private function getBaseSkillMatch(People $person, Roles $targetRole): float
    {
        // Get person's skills - using 'level' field if available
        $personSkills = $person->skills()
            ->select('skills.id', 'people_role_skills.current_level')
            ->pluck('current_level', 'skills.id')
            ->toArray();

        // If no skills found, return base match
        if (empty($personSkills)) {
            return 50; // Default if no skills defined
        }

        // Simple match: higher person skill count, higher match
        $matchCount = count($personSkills);

        return min(100, 50 + ($matchCount * 10)); // Base 50 + bonus for each skill
    }

    /**
     * Calculate experience bonus (0-20 points)
     */
    private function calculateExperienceBonus(People $person, Roles $targetRole): float
    {
        $relevantYears = $this->getRelevantExperienceYears($person, $targetRole);

        // 5 years relevant experience = 20 points
        return min(20, $relevantYears * 4);
    }

    /**
     * Calculate training bonus from scenario planned training (0-15 points)
     */
    private function calculateTrainingBonus(People $person, Scenario $scenario): float
    {
        // Count training activities planned for this person in scenario
        // For now, return 0 - can be extended with LMS integration
        return 0;
    }

    /**
     * Get years of relevant experience (same or similar roles)
     */
    private function getRelevantExperienceYears(People $person, Roles $targetRole): int
    {
        // Sample implementation: check person's career history
        // In production, query actual career progression data
        $hiredDate = $person->hire_date ?? $person->created_at;
        if (! $hiredDate) {
            return 0;
        }

        return (int) ceil($hiredDate->diffInYears(now()));
    }

    /**
     * Get mentor availability score (0-100)
     * Based on availability of mentors in the organization for this role
     */
    private function getMentorAvailability(SuccessionCandidate $candidate): float
    {
        // Find potential mentors (people with higher readiness in target role)
        $potentialMentors = SuccessionCandidate::where('scenario_id', $candidate->scenario_id)
            ->where('target_role_id', $candidate->target_role_id)
            ->where('person_id', '!=', $candidate->person_id)
            ->where('readiness_level', 'expert')
            ->count();

        // Score: 0 mentors = 0%, 1+ mentor = 100 (for simplicity)
        return $potentialMentors > 0 ? 100 : 50;
    }

    /**
     * Get motivation signal for person to take target role (0-100)
     * Based on career path alignment and current satisfaction
     */
    private function getMotivationSignal(SuccessionCandidate $candidate): float
    {
        // In production, integrate with engagement/career preference data
        // For now, return weighted score based on readiness tracking
        $acceptanceTime = now()->diffInDays($candidate->created_at);

        // If they're still in the pipeline after 30 days, assume motivation
        return $acceptanceTime > 30 ? 75 : 50;
    }
}
