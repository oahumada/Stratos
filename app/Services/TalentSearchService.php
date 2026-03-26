<?php

namespace App\Services;

use App\Models\TalentPass;
use App\Models\TalentPassSkill;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;

class TalentSearchService
{
    /**
     * Search talent passes by skills
     */
    public function searchBySkills(array $skillNames, int $organizationId, int $limit = 20): Collection
    {
        return TalentPass::byOrganization($organizationId)
            ->published()
            ->whereHas('skills', function ($query) use ($skillNames) {
                $query->whereIn('skill_name', $skillNames);
            })
            ->with(['person', 'skills', 'credentials', 'experiences'])
            ->limit($limit)
            ->get();
    }

    /**
     * Search globally by keyword (name, skill, company, etc)
     */
    public function globalSearch(string $query, int $organizationId, int $limit = 20): Collection
    {
        $query = '%' . $query . '%';

        return TalentPass::byOrganization($organizationId)
            ->published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', $query)
                  ->orWhere('summary', 'like', $query)
                  ->orWhereHas('person', fn($pq) => $pq->where('first_name', 'like', $query)
                      ->orWhere('last_name', 'like', $query))
                  ->orWhereHas('skills', fn($sq) => $sq->where('skill_name', 'like', $query))
                  ->orWhereHas('credentials', fn($cq) => $cq->where('credential_name', 'like', $query))
                  ->orWhereHas('experiences', fn($eq) => $eq->where('company', 'like', $query)
                      ->orWhere('job_title', 'like', $query));
            })
            ->with(['person', 'skills', 'credentials', 'experiences'])
            ->limit($limit)
            ->get();
    }

    /**
     * Find talent passes by skill level
     */
    public function findBySkillLevel(string $skillName, string $level, int $organizationId, int $limit = 20): Collection
    {
        return TalentPass::byOrganization($organizationId)
            ->published()
            ->whereHas('skills', function ($query) use ($skillName, $level) {
                $query->where('skill_name', $skillName)
                      ->where('proficiency_level', $level);
            })
            ->with(['person', 'skills', 'credentials', 'experiences'])
            ->limit($limit)
            ->get();
    }

    /**
     * Find talent passes by experience (years and company)
     */
    public function findByExperience(string $company, int $minYears = 0, int $organizationId = null, int $limit = 20): Collection
    {
        return TalentPass::when($organizationId, fn($q) => $q->byOrganization($organizationId))
            ->published()
            ->whereHas('experiences', function ($query) use ($company) {
                $query->where('company', 'like', '%' . $company . '%');
            })
            ->with(['person', 'skills', 'credentials', 'experiences'])
            ->get()
            ->filter(function ($tp) use ($minYears, $company) {
                $experiences = $tp->experiences->where('company', 'like', '%' . $company . '%');
                $totalYears = $experiences->sum(fn($exp) => $exp->getDurationInMonths()) / 12;
                return $totalYears >= $minYears;
            })
            ->take($limit)
            ->values();
    }

    /**
     * Get trending skills
     */
    public function getTrendingSkills(int $organizationId, int $limit = 10): array
    {
        return TalentPassSkill::whereHas('talentPass', function ($query) use ($organizationId) {
            $query->byOrganization($organizationId)->published();
        })
            ->selectRaw('skill_name, COUNT(*) as count, AVG(CAST(endorsement_count AS DECIMAL)) as avg_endorsements')
            ->groupBy('skill_name')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->map(fn($skill) => [
                'skill_name' => $skill->skill_name,
                'count' => $skill->count,
                'avg_endorsements' => $skill->avg_endorsements,
            ])
            ->toArray();
    }

    /**
     * Get skill gap analysis (what skills are needed vs what we have)
     */
    public function getSkillGapAnalysis(array $targetSkills, int $organizationId, int $limit = 20): array
    {
        $talents = $this->searchBySkills($targetSkills, $organizationId, $limit);

        $skillCounts = [];
        foreach ($targetSkills as $skill) {
            $skillCounts[$skill] = $talents->filter(function ($tp) use ($skill) {
                return $tp->skills->pluck('skill_name')->contains($skill);
            })->count();
        }

        // Calculate overall coverage percentage
        $totalPossible = count($targetSkills) * max(1, $talents->count());
        $totalCovered = array_sum($skillCounts);

        return [
            'target_skills' => $targetSkills,
            'total_talents' => $talents->count(),
            'skills_coverage' => $skillCounts,
            'coverage' => $totalPossible > 0 ? ($totalCovered / $totalPossible) * 100 : 0,
            'gap_analysis' => collect($skillCounts)->map(fn($count, $skill) => [
                'skill' => $skill,
                'coverage' => $talents->count() > 0 ? ($count / $talents->count()) * 100 : 0,
                'gap' => max(0, $talents->count() - $count),
            ])->toArray(),
        ];
    }

    /**
     * Find talent by credentials
     */
    public function findByCredential(string $credentialName, int $organizationId, int $limit = 20): Collection
    {
        return TalentPass::byOrganization($organizationId)
            ->published()
            ->whereHas('credentials', function ($query) use ($credentialName) {
                $query->where('credential_name', 'like', '%' . $credentialName . '%');
            })
            ->with(['person', 'skills', 'credentials', 'experiences'])
            ->limit($limit)
            ->get();
    }

    /**
     * Recommend similar talent passes
     */
    public function getSimilarTalent(TalentPass $talentPass, int $limit = 5): Collection
    {
        $skillNames = $talentPass->skills->pluck('skill_name')->toArray();

        if (empty($skillNames)) {
            return collect();
        }

        return TalentPass::where('id', '!=', $talentPass->id)
            ->byOrganization($talentPass->organization_id)
            ->published()
            ->whereHas('skills', function ($query) use ($skillNames) {
                $query->whereIn('skill_name', $skillNames);
            })
            ->with(['person', 'skills'])
            ->limit($limit)
            ->get();
    }
}
