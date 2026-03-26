<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TalentSearchService;
use Illuminate\Http\Request;

class TalentSearchController extends Controller
{
    public function __construct(
        private TalentSearchService $searchService,
    ) {}

    /**
     * Global search across multiple fields
     */
    public function search(Request $request)
    {
        $organizationId = auth()->user()?->organization_id;
        $query = $request->query('q', '');

        if (strlen($query) < 2) {
            return response()->json(['error' => 'Query must be at least 2 characters'], 400);
        }

        $results = $this->searchService->globalSearch($query, $organizationId);

        return response()->json([
            'query' => $query,
            'count' => $results->count(),
            'results' => $results->map(function ($tp) {
                return [
                    'id' => $tp->id,
                    'title' => $tp->title,
                    'ulid' => $tp->ulid,
                    'person_name' => $tp->person?->full_name,
                    'status' => $tp->status,
                    'visibility' => $tp->visibility,
                    'views' => $tp->views_count,
                ];
            }),
        ]);
    }

    /**
     * Search by specific skills
     */
    public function searchBySkills(Request $request)
    {
        $organizationId = auth()->user()?->organization_id;
        $skills = $request->query('skills', []);

        if (empty($skills)) {
            return response()->json(['error' => 'Skills parameter required'], 400);
        }

        $results = $this->searchService->searchBySkills((array) $skills, $organizationId);

        return response()->json([
            'skills' => $skills,
            'count' => $results->count(),
            'results' => $results->map(function ($tp) {
                return [
                    'id' => $tp->id,
                    'title' => $tp->title,
                    'person_name' => $tp->person?->full_name,
                    'skills' => $tp->skills->map(fn ($s) => $s->skill_name)->toArray(),
                ];
            }),
        ]);
    }

    /**
     * Get trending skills in organization
     */
    public function getTrending(Request $request)
    {
        $organizationId = auth()->user()?->organization_id;
        $limit = $request->query('limit', 10);

        $trending = $this->searchService->getTrendingSkills($organizationId);

        return response()->json([
            'trending' => array_slice($trending, 0, $limit),
            'total' => count($trending),
        ]);
    }

    /**
     * Analyze skill gaps against target skills
     */
    public function gaps(Request $request)
    {
        $organizationId = auth()->user()?->organization_id;

        $validated = $request->validate([
            'target_skills' => 'required|array|min:1',
            'target_skills.*' => 'string',
        ]);

        $analysis = $this->searchService->getSkillGapAnalysis(
            $validated['target_skills'],
            $organizationId
        );

        return response()->json($analysis);
    }

    /**
     * Find similar talent based on a reference talent pass
     */
    public function similar(Request $request)
    {
        $organizationId = auth()->user()?->organization_id;
        $talentPassId = $request->query('talent_pass_id');

        if (! $talentPassId) {
            return response()->json(['error' => 'talent_pass_id required'], 400);
        }

        $referenceTalentPass = \App\Models\TalentPass::findOrFail($talentPassId);

        if ($referenceTalentPass->organization_id !== $organizationId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $results = $this->searchService->getSimilarTalent($referenceTalentPass, $organizationId);

        return response()->json([
            'reference_talent_pass' => $referenceTalentPass->title,
            'similar_count' => $results->count(),
            'results' => $results->map(function ($tp) {
                return [
                    'id' => $tp->id,
                    'title' => $tp->title,
                    'person_name' => $tp->person?->full_name,
                    'status' => $tp->status,
                    'matches' => 'calculated',
                ];
            }),
        ]);
    }

    /**
     * Find talents by skill level proficiency
     */
    public function findBySkillLevel(Request $request)
    {
        $organizationId = auth()->user()?->organization_id;
        $skill = $request->query('skill');
        $level = $request->query('level', 'intermediate');

        $valid_levels = ['beginner', 'intermediate', 'expert', 'master'];

        if (! in_array($level, $valid_levels)) {
            return response()->json(['error' => 'Invalid level: ' . implode(', ', $valid_levels)], 400);
        }

        $results = $this->searchService->findBySkillLevel($skill, $level, $organizationId);

        return response()->json([
            'skill' => $skill,
            'level' => $level,
            'count' => $results->count(),
            'results' => $results->map(function ($tp) {
                return [
                    'id' => $tp->id,
                    'title' => $tp->title,
                    'person_name' => $tp->person?->full_name,
                ];
            }),
        ]);
    }

    /**
     * Find talents by company/experience
     */
    public function findByExperience(Request $request)
    {
        $organizationId = auth()->user()?->organization_id;
        $company = $request->query('company');
        $minYears = $request->query('min_years', 0);

        $results = $this->searchService->findByExperience($company, (int) $minYears, $organizationId);

        return response()->json([
            'company' => $company,
            'min_years' => $minYears,
            'count' => $results->count(),
            'results' => $results->map(function ($tp) {
                return [
                    'id' => $tp->id,
                    'title' => $tp->title,
                    'person_name' => $tp->person?->full_name,
                    'experiences' => $tp->experiences->count(),
                ];
            }),
        ]);
    }

    /**
     * Find talents by credential/certification
     */
    public function findByCredential(Request $request)
    {
        $organizationId = auth()->user()?->organization_id;
        $credential = $request->query('credential');

        $results = $this->searchService->findByCredential($credential, $organizationId);

        return response()->json([
            'credential' => $credential,
            'count' => $results->count(),
            'results' => $results->map(function ($tp) {
                return [
                    'id' => $tp->id,
                    'title' => $tp->title,
                    'person_name' => $tp->person?->full_name,
                    'credentials' => $tp->credentials->count(),
                ];
            }),
        ]);
    }
}
