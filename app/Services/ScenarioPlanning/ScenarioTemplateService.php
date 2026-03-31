<?php

namespace App\Services\ScenarioPlanning;

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\ScenarioTemplate;
use App\Services\WorkforcePlanningService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * ScenarioTemplateService — Manage scenario templates for reusable planning
 *
 * Responsibilities:
 * - CRUD operations on templates (create, read, update, delete)
 * - Save-as-template (convert existing scenario to reusable template)
 * - Template instantiation (create scenario from template with customizations)
 * - Template versioning and inheritance
 * - Template recommendations based on organization context
 */
class ScenarioTemplateService
{
    public function __construct(
        private WorkforcePlanningService $workforcePlanning,
    ) {}

    /**
     * Get all templates with optional filtering
     *
     * @param  array<string, mixed>  $filters  Filters: type, industry, is_active, search
     * @param  int  $perPage  Results per page
    * @return LengthAwarePaginator
     */
    public function getTemplates(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ScenarioTemplate::query();

        // Filter by scenario type
        if (isset($filters['scenario_type']) && $filters['scenario_type']) {
            $query->where('scenario_type', $filters['scenario_type']);
        }

        // Filter by industry (include 'general' templates)
        if (isset($filters['industry']) && $filters['industry']) {
            $query->where(function ($q) use ($filters) {
                $q->where('industry', $filters['industry'])
                    ->orWhere('industry', 'general');
            });
        }

        // Filter by active status
        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        // Search by name or description
        if (isset($filters['search']) && $filters['search']) {
            $search = "%{$filters['search']}%";
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhere('slug', 'like', $search);
            });
        }

        return $query->orderBy('usage_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get a single template by ID with usage stats
     */
    public function getTemplate(int $templateId): ?ScenarioTemplate
    {
        $template = ScenarioTemplate::findOrFail($templateId);
        $template->incrementUsage();

        return $template;
    }

    /**
     * Create a new template from scratch
     *
     * @param  array<string, mixed>  $data  Template data
     * @return ScenarioTemplate
     */
    public function createTemplate(array $data): ScenarioTemplate
    {
        // Generate slug from name if not provided
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        // Ensure config is array
        $data['config'] = $data['config'] ?? [];

        // Set defaults
        $data['is_active'] = $data['is_active'] ?? true;
        $data['usage_count'] = 0;

        return ScenarioTemplate::create($data);
    }

    /**
     * Update an existing template
     *
     * @param  int  $templateId
     * @param  array<string, mixed>  $data
     * @return ScenarioTemplate
     */
    public function updateTemplate(int $templateId, array $data): ScenarioTemplate
    {
        $template = ScenarioTemplate::findOrFail($templateId);

        // Prevent slug changes after creation (preserve URLs)
        unset($data['slug']);

        // Merge config instead of replacing
        if (isset($data['config'])) {
            $data['config'] = array_merge($template->config ?? [], $data['config']);
        }

        $template->update($data);

        return $template;
    }

    /**
     * Delete a template (soft delete)
     *
     * @param  int  $templateId
     * @return bool
     */
    public function deleteTemplate(int $templateId): bool
    {
        $template = ScenarioTemplate::findOrFail($templateId);

        return (bool) $template->delete();
    }

    /**
     * Save an existing scenario as a reusable template
     *
     * Converts scenario configuration to template format
     *
     * @param  int  $scenarioId
     * @param  array<string, mixed>  $templateData  Additional template metadata
     * @return ScenarioTemplate
     */
    public function saveScenarioAsTemplate(int $scenarioId, array $templateData): ScenarioTemplate
    {
        // Use withoutGlobalScopes to avoid tenant/global-scope hiding the scenario
        // during tests or administrative operations where we resolve by ID.
        $scenario = Scenario::withoutGlobalScopes()->findOrFail($scenarioId);

        // Extract scenario configuration for template
        $config = [
            'predefined_skills' => $this->extractSkillsFromScenario($scenario),
            'suggested_strategies' => $scenario->suggested_strategies ?? [],
            'kpis' => $scenario->kpis ?? [],
            'assumptions' => [
                'Budget available' => $scenario->budget ?? 'TBD',
                'Timeline' => $scenario->timeline_weeks ? "{$scenario->timeline_weeks} weeks" : 'TBD',
                'Expected retention' => $scenario->expected_retention ?? 'TBD',
            ],
            'source_scenario_id' => $scenario->id,
            'source_scenario_version' => $scenario->version_number ?? 1,
        ];

        // Merge with provided template data
        $templateConfig = array_merge($config, $templateData['config'] ?? []);

        return $this->createTemplate([
            'name' => $templateData['name'],
            'description' => $templateData['description'] ?? $scenario->description,
            'scenario_type' => $templateData['scenario_type'] ?? $scenario->scenario_type ?? 'general',
            'industry' => $templateData['industry'] ?? 'general',
            'icon' => $templateData['icon'] ?? 'mdi-file-document',
            'config' => $templateConfig,
            'is_active' => $templateData['is_active'] ?? true,
        ]);
    }

    /**
     * Create a scenario from a template with customizations
     *
     * @param  int  $templateId
     * @param  array<string, mixed>  $customizations  Override specific template settings
     * @param  string  $organizationId  Organization context
     * @return Scenario
     */
    public function instantiateScenarioFromTemplate(
        int $templateId,
        array $customizations = [],
        string $organizationId = null,
    ): Scenario
    {
        $template = ScenarioTemplate::findOrFail($templateId);

        // Merge template config with customizations
        $config = array_merge($template->config ?? [], $customizations['config'] ?? []);

        // Build scenario data from template
        $scenarioData = [
            'name' => $customizations['name'] ?? "From {$template->name}",
            'description' => $customizations['description'] ?? $template->description,
            'scenario_type' => $customizations['scenario_type'] ?? $template->scenario_type,
            'status' => 'draft',
            'version_number' => 1,
            'version_group_id' => \Illuminate\Support\Str::uuid(),
            'is_current_version' => true,
            'budget' => $customizations['budget'] ?? $config['assumptions']['Budget available'] ?? null,
            'timeline_weeks' => $customizations['timeline_weeks'] ?? $this->extractTimelineWeeks($config),
            'expected_retention' => $customizations['expected_retention'] ?? $config['assumptions']['Expected retention'] ?? null,
            'suggested_strategies' => $config['suggested_strategies'] ?? [],
            'kpis' => $config['kpis'] ?? [],
            'template_id' => $templateId,
        ];

        // Add organization context if provided
        if ($organizationId) {
            $scenarioData['organization_id'] = $organizationId;
        }

        // Ensure required temporal fields and horizon are set to avoid DB NOT NULL errors
        $timelineWeeks = $scenarioData['timeline_weeks'] ?? null;
        if ($timelineWeeks) {
            $scenarioData['horizon_months'] = (int) ceil($timelineWeeks / 4);
            $scenarioData['start_date'] = now()->toDateString();
            $scenarioData['end_date'] = now()->addWeeks((int) $timelineWeeks)->toDateString();
        } else {
            // sensible defaults when timeline not provided
            $scenarioData['horizon_months'] = $scenarioData['horizon_months'] ?? 6;
            $scenarioData['start_date'] = $scenarioData['start_date'] ?? now()->toDateString();
            $scenarioData['end_date'] = $scenarioData['end_date'] ?? now()->addMonths($scenarioData['horizon_months'])->toDateString();
        }

        // Fiscal year (required by schema)
        $scenarioData['fiscal_year'] = $scenarioData['fiscal_year'] ?? now()->year;

        // Set audit/ownership fields from current user when available
        $currentUserId = auth()->id() ?? null;
        if (Schema::hasColumn('scenarios', 'created_by')) {
            $scenarioData['created_by'] = $currentUserId;
        }
        if (Schema::hasColumn('scenarios', 'owner_user_id')) {
            $scenarioData['owner_user_id'] = $currentUserId;
        }

        // Only include optional columns if they exist in the schema
        if (! Schema::hasColumn('scenarios', 'budget')) {
            unset($scenarioData['budget']);
        }
        if (! Schema::hasColumn('scenarios', 'timeline_weeks')) {
            unset($scenarioData['timeline_weeks']);
        }

        // Create scenario
        $scenario = Scenario::create($scenarioData);

        // Increment template usage
        $template->incrementUsage();

        return $scenario->load(['roles', 'skills']);
    }

    /**
     * Clone a template with optional name change
     *
     * @param  int  $templateId
     * @param  string  $newName
     * @return ScenarioTemplate
     */
    public function cloneTemplate(int $templateId, string $newName = null): ScenarioTemplate
    {
        $original = ScenarioTemplate::findOrFail($templateId);

        $cloneData = $original->toArray();

        // Remove auto-generated/identity fields
        unset($cloneData['id'], $cloneData['created_at'], $cloneData['updated_at'], $cloneData['deleted_at']);

        // Update name and slug
        $cloneData['name'] = $newName ?? "{$original->name} (Copy)";
        $cloneData['slug'] = Str::slug($cloneData['name']);

        // Reset usage count
        $cloneData['usage_count'] = 0;

        return ScenarioTemplate::create($cloneData);
    }

    /**
     * Get recommended templates for organization
     *
     * Recommends templates based on organization size, industry, and use cases
     *
     * @param  string  $organizationId
     * @param  int  $limit
     * @return Collection
     */
    public function getRecommendedTemplates(string $organizationId, int $limit = 5): Collection
    {
        // Get organization metrics
        $totalHeadcount = \App\Models\People::where('organization_id', $organizationId)->count();

        // Determine organization size and industry
        $organisation = \App\Models\Organization::find($organizationId);
        $industry = $organisation?->industry ?? 'general';

        // Build recommendation query
        $query = ScenarioTemplate::where('is_active', true)
            ->where(function ($q) use ($industry) {
                $q->where('industry', $industry)
                    ->orWhere('industry', 'general');
            });

        // Prioritize by relevance (most used first, then by creation date)
        $recommendations = $query->orderBy('usage_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $recommendations;
    }

    /**
     * Get template statistics
     *
     * @return array<string, mixed>
     */
    public function getTemplateStatistics(): array
    {
        return [
            'total_templates' => ScenarioTemplate::count(),
            'active_templates' => ScenarioTemplate::where('is_active', true)->count(),
            'total_instantiations' => ScenarioTemplate::sum('usage_count'),
            'templates_by_type' => ScenarioTemplate::selectRaw('scenario_type, COUNT(*) as count')
                ->groupBy('scenario_type')
                ->get()
                ->pluck('count', 'scenario_type')
                ->toArray(),
            'templates_by_industry' => ScenarioTemplate::selectRaw('industry, COUNT(*) as count')
                ->groupBy('industry')
                ->get()
                ->pluck('count', 'industry')
                ->toArray(),
            'most_used_templates' => ScenarioTemplate::orderBy('usage_count', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'usage_count']),
        ];
    }

    /**
     * Extract skills configuration from scenario
     *
     * @param  Scenario  $scenario
     * @return array
     */
    private function extractSkillsFromScenario(Scenario $scenario): array
    {
        return $scenario->skills()
            ->get()
            ->map(function ($skill) {
                return [
                    'skill_id' => $skill->id,
                    'skill_name' => $skill->name,
                    'required_headcount' => $skill->pivot?->required_headcount ?? 1,
                    'required_level' => $skill->pivot?->required_level ?? 2,
                    'priority' => $skill->pivot?->priority ?? 'medium',
                    'rationale' => $skill->pivot?->description ?? '',
                ];
            })
            ->toArray();
    }

    /**
     * Extract timeline weeks from config
     *
     * @param  array  $config
     * @return int|null
     */
    private function extractTimelineWeeks(array $config): ?int
    {
        $timeline = $config['assumptions']['Timeline'] ?? null;

        if (! $timeline) {
            return null;
        }

        // Parse "X weeks" or "X-Y weeks" format
        if (preg_match('/(\d+)/', $timeline, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
