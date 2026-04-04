<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departments;
use App\Models\People;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * OrgPeopleChartController — C3 Org Charting
 *
 * Builds hierarchical org chart data from real People (supervised_by)
 * and Departments (parent_id) — for the interactive frontend org chart.
 */
class OrgPeopleChartController extends Controller
{
    /**
     * GET /api/org-chart/people
     * Full people tree rooted at top-level employees (supervised_by = null or out-of-org).
     * ?view=departments → returns dept tree instead
     */
    public function tree(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;

        if ($request->query('view') === 'departments') {
            return response()->json($this->departmentTree($orgId));
        }

        return response()->json($this->peopleTree($orgId));
    }

    /**
     * GET /api/org-chart/people/{id}/subtree
     * Subtree rooted at a specific person.
     */
    public function subtree(int $id): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $person = People::where('organization_id', $orgId)->findOrFail($id);
        $index = $this->buildPeopleIndex($orgId);

        return response()->json([
            'root' => $this->buildPersonNode($person, $index),
        ]);
    }

    /**
     * GET /api/org-chart/people/stats
     * Quick stats: total employees, avg span of control, max depth, top depts.
     */
    public function stats(): JsonResponse
    {
        $orgId = auth()->user()->organization_id;

        $people = People::where('organization_id', $orgId)
            ->select('id', 'supervised_by', 'department_id')
            ->get();

        $total = $people->count();
        $supervised = $people->whereNotNull('supervised_by')->groupBy('supervised_by');
        $avgSpan = $supervised->count() > 0
            ? round($supervised->map->count()->avg(), 2)
            : 0.0;

        return response()->json([
            'total_employees' => $total,
            'total_managers' => $supervised->count(),
            'avg_span_of_control' => $avgSpan,
            'max_depth' => $this->computeMaxDepth($people),
            'top_departments' => $people->groupBy('department_id')->map->count()->sortDesc()->take(5),
        ]);
    }

    // ─── Private helpers ────────────────────────────────────────────────────────

    private function peopleTree(int $orgId): array
    {
        $index = $this->buildPeopleIndex($orgId);

        $roots = collect($index)->filter(fn ($p) =>
            ! $p->supervised_by || ! isset($index[$p->supervised_by])
        );

        return [
            'view' => 'people',
            'nodes' => $roots->values()->map(fn ($p) => $this->buildPersonNode($p, $index))->all(),
            'meta' => ['total' => count($index)],
        ];
    }

    private function buildPeopleIndex(int $orgId): array
    {
        return People::where('organization_id', $orgId)
            ->select('id', 'first_name', 'last_name', 'supervised_by', 'department_id', 'photo_url', 'is_high_potential')
            ->get()
            ->keyBy('id')
            ->all();
    }

    private function buildPersonNode(People $person, array $index, int $depth = 0): array
    {
        $reports = collect($index)->filter(fn ($p) => $p->supervised_by === $person->id);

        return [
            'id' => $person->id,
            'name' => trim(($person->first_name ?? '') . ' ' . ($person->last_name ?? '')),
            'department_id' => $person->department_id,
            'is_high_potential' => (bool) $person->is_high_potential,
            'photo_url' => $person->photo_url,
            'depth' => $depth,
            'direct_reports_count' => $reports->count(),
            'children' => $reports->values()
                ->map(fn ($r) => $this->buildPersonNode($r, $index, $depth + 1))
                ->all(),
        ];
    }

    private function departmentTree(int $orgId): array
    {
        $depts = Departments::where('organization_id', $orgId)
            ->select('id', 'name', 'parent_id', 'manager_id')
            ->get()
            ->keyBy('id');

        $roots = $depts->filter(fn ($d) => ! $d->parent_id || ! $depts->has($d->parent_id));

        return [
            'view' => 'departments',
            'nodes' => $roots->values()->map(fn ($d) => $this->buildDeptNode($d, $depts))->all(),
            'meta' => ['total' => $depts->count()],
        ];
    }

    private function buildDeptNode(Departments $dept, $index): array
    {
        $children = $index->filter(fn ($d) => $d->parent_id === $dept->id);

        return [
            'id' => $dept->id,
            'name' => $dept->name,
            'manager_id' => $dept->manager_id,
            'children' => $children->values()->map(fn ($d) => $this->buildDeptNode($d, $index))->all(),
        ];
    }

    private function computeMaxDepth($people): int
    {
        $byParent = $people->groupBy('supervised_by');
        $maxDepth = 0;

        $traverse = function (int $parentId, int $depth) use (&$traverse, $byParent, &$maxDepth) {
            $maxDepth = max($maxDepth, $depth);
            foreach ($byParent->get($parentId, collect()) as $child) {
                $traverse($child->id, $depth + 1);
            }
        };

        foreach ($people->whereNull('supervised_by') as $root) {
            $traverse($root->id, 0);
        }

        return $maxDepth;
    }
}
