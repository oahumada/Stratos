<?php

namespace App\Services;

use App\Models\Departments;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Skill;
use Illuminate\Support\Collection;

/**
 * SkillIntelligenceService — Fase C1
 *
 * Provides org-wide skill gap intelligence:
 *  - Department heatmap (avg gap per skill × dept)
 *  - Top gaps across org (skills most lacking)
 *  - Upskilling recommendations (skill + target people)
 *  - Coverage trend (% of people meeting required level per skill)
 */
class SkillIntelligenceService
{
    /**
     * Returns a heatmap matrix: departments × skills → avg_gap score.
     *
     * @return array{departments: string[], skills: string[], matrix: array<string, array<string, float>>, meta: array}
     */
    public function departmentHeatmap(int $organizationId, ?string $category = null): array
    {
        // Load all active people with their role-skills, scoped to org
        $query = PeopleRoleSkills::where('is_active', true)
            ->whereHas('person', fn ($q) => $q->where('organization_id', $organizationId))
            ->with([
                'person.department',
                'skill',
            ]);

        if ($category) {
            $query->whereHas('skill', fn ($q) => $q->where('category', $category));
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            return ['departments' => [], 'skills' => [], 'matrix' => [], 'meta' => ['total_records' => 0]];
        }

        // Build gap matrix: dept_name → skill_name → [gaps]
        $matrix = [];
        foreach ($records as $record) {
            $dept = $record->person?->department?->name ?? 'Sin Departamento';
            $skillName = $record->skill?->name ?? 'Desconocido';
            $gap = max(0, ($record->required_level ?? 0) - ($record->current_level ?? 0));

            $matrix[$dept][$skillName][] = $gap;
        }

        // Compute averages
        $heatmap = [];
        $allDepts = [];
        $allSkills = [];

        foreach ($matrix as $dept => $skills) {
            $allDepts[] = $dept;
            foreach ($skills as $skill => $gaps) {
                $allSkills[] = $skill;
                $heatmap[$dept][$skill] = round(array_sum($gaps) / count($gaps), 2);
            }
        }

        $allDepts = array_values(array_unique($allDepts));
        $allSkills = array_values(array_unique($allSkills));
        sort($allDepts);
        sort($allSkills);

        return [
            'departments' => $allDepts,
            'skills' => $allSkills,
            'matrix' => $heatmap,
            'meta' => [
                'total_records' => $records->count(),
                'category_filter' => $category,
            ],
        ];
    }

    /**
     * Returns top N skills with largest aggregate gaps across the org.
     *
     * @return array{top_gaps: array<int, array{skill_id: int, skill_name: string, category: string, avg_gap: float, affected_people: int, critical_count: int}>}
     */
    public function topGaps(int $organizationId, int $limit = 10): array
    {
        $records = PeopleRoleSkills::where('is_active', true)
            ->whereHas('person', fn ($q) => $q->where('organization_id', $organizationId))
            ->with('skill')
            ->get();

        $bySkill = [];
        foreach ($records as $record) {
            if (! $record->skill) {
                continue;
            }
            $gap = max(0, ($record->required_level ?? 0) - ($record->current_level ?? 0));
            $sid = $record->skill_id;

            if (! isset($bySkill[$sid])) {
                $bySkill[$sid] = [
                    'skill_id' => $sid,
                    'skill_name' => $record->skill->name,
                    'category' => $record->skill->category ?? 'general',
                    'domain_tag' => $record->skill->domain_tag,
                    'is_critical' => (bool) $record->skill->is_critical,
                    'gaps' => [],
                    'affected_people' => 0,
                    'critical_count' => 0,
                ];
            }

            $bySkill[$sid]['gaps'][] = $gap;
            if ($gap > 0) {
                $bySkill[$sid]['affected_people']++;
                if ($gap >= 2) {
                    $bySkill[$sid]['critical_count']++;
                }
            }
        }

        // Compute avg and filter to skills with actual gaps
        $result = [];
        foreach ($bySkill as $entry) {
            $avgGap = count($entry['gaps']) > 0
                ? round(array_sum($entry['gaps']) / count($entry['gaps']), 2)
                : 0.0;

            if ($avgGap > 0) {
                $result[] = [
                    'skill_id' => $entry['skill_id'],
                    'skill_name' => $entry['skill_name'],
                    'category' => $entry['category'],
                    'domain_tag' => $entry['domain_tag'],
                    'is_critical' => $entry['is_critical'],
                    'avg_gap' => $avgGap,
                    'affected_people' => $entry['affected_people'],
                    'critical_count' => $entry['critical_count'],
                ];
            }
        }

        // Sort by critical_count DESC then avg_gap DESC
        usort($result, fn ($a, $b) =>
            $b['critical_count'] <=> $a['critical_count'] ?: $b['avg_gap'] <=> $a['avg_gap']
        );

        return ['top_gaps' => array_slice($result, 0, $limit)];
    }

    /**
     * Returns upskilling recommendations: for each top-gap skill, lists
     * people who need it most and suggested development actions.
     *
     * @return array{recommendations: array}
     */
    public function upskillingRecommendations(int $organizationId, int $limit = 8): array
    {
        $topGaps = $this->topGaps($organizationId, $limit)['top_gaps'];

        $recommendations = [];
        foreach ($topGaps as $gap) {
            $skillId = $gap['skill_id'];

            // People with largest gap for this skill
            $urgent = PeopleRoleSkills::where('skill_id', $skillId)
                ->where('is_active', true)
                ->whereHas('person', fn ($q) => $q->where('organization_id', $organizationId))
                ->whereRaw('GREATEST(0, required_level - current_level) >= 2')
                ->with('person:id,first_name,last_name,department_id')
                ->limit(5)
                ->get()
                ->map(fn ($r) => [
                    'people_id' => $r->people_id,
                    'name' => trim(($r->person?->first_name ?? '') . ' ' . ($r->person?->last_name ?? '')),
                    'current_level' => $r->current_level ?? 0,
                    'required_level' => $r->required_level ?? 0,
                    'gap' => max(0, ($r->required_level ?? 0) - ($r->current_level ?? 0)),
                ]);

            $action = $this->suggestAction($gap);

            $recommendations[] = [
                'skill_id' => $skillId,
                'skill_name' => $gap['skill_name'],
                'category' => $gap['category'],
                'avg_gap' => $gap['avg_gap'],
                'affected_people' => $gap['affected_people'],
                'priority' => $gap['critical_count'] >= 3 ? 'alta' : ($gap['avg_gap'] >= 1.5 ? 'media' : 'baja'),
                'suggested_action' => $action,
                'urgent_people' => $urgent,
            ];
        }

        return ['recommendations' => $recommendations];
    }

    /**
     * Returns skill coverage summary: % of people meeting required level per skill.
     */
    public function coverageSummary(int $organizationId): array
    {
        $records = PeopleRoleSkills::where('is_active', true)
            ->whereHas('person', fn ($q) => $q->where('organization_id', $organizationId))
            ->with('skill:id,name,category,is_critical')
            ->get();

        $bySkill = [];
        foreach ($records as $record) {
            if (! $record->skill) {
                continue;
            }
            $sid = $record->skill_id;
            if (! isset($bySkill[$sid])) {
                $bySkill[$sid] = ['skill' => $record->skill, 'met' => 0, 'total' => 0];
            }
            $bySkill[$sid]['total']++;
            if (($record->current_level ?? 0) >= ($record->required_level ?? 0)) {
                $bySkill[$sid]['met']++;
            }
        }

        $coverage = [];
        foreach ($bySkill as $entry) {
            $pct = $entry['total'] > 0 ? round($entry['met'] / $entry['total'] * 100, 1) : 0.0;
            $coverage[] = [
                'skill_id' => $entry['skill']->id,
                'skill_name' => $entry['skill']->name,
                'category' => $entry['skill']->category ?? 'general',
                'is_critical' => (bool) $entry['skill']->is_critical,
                'coverage_pct' => $pct,
                'people_meeting' => $entry['met'],
                'total_people' => $entry['total'],
            ];
        }

        usort($coverage, fn ($a, $b) => $a['coverage_pct'] <=> $b['coverage_pct']);

        $orgAvg = count($coverage) > 0
            ? round(array_sum(array_column($coverage, 'coverage_pct')) / count($coverage), 1)
            : 0.0;

        return [
            'org_avg_coverage_pct' => $orgAvg,
            'skills' => $coverage,
            'meta' => ['total_skills' => count($coverage)],
        ];
    }

    private function suggestAction(array $gap): string
    {
        $name = strtolower($gap['skill_name']);
        $avg = $gap['avg_gap'];

        if (str_contains($name, 'python') || str_contains($name, 'sql') || str_contains($name, 'data')) {
            return 'Curso técnico + práctica en proyectos internos';
        }
        if (str_contains($name, 'liderazgo') || str_contains($name, 'leadership') || str_contains($name, 'gestión')) {
            return 'Programa de mentoring + asignación a proyectos estratégicos';
        }
        if ($avg >= 2) {
            return 'Plan de desarrollo individual (PDI) acelerado de 3 meses';
        }
        if ($avg >= 1) {
            return 'Workshop interno + job rotation de 4-6 semanas';
        }

        return 'Microlearning + refuerzo en evaluaciones trimestrales';
    }
}
