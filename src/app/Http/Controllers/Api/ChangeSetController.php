<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChangeSet;
use App\Models\Scenario;
use App\Services\ChangeSetService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChangeSetController extends Controller
{
    protected ChangeSetService $service;

    public function __construct(ChangeSetService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum');
    }

    public function store(Request $request, $scenarioId)
    {
        $user = $request->user();
        $payload = $request->only(['title', 'description', 'diff', 'metadata']);
        $payload['scenario_id'] = $scenarioId;
        $payload['organization_id'] = $user->organization_id ?? null;
        $payload['created_by'] = $user->id ?? null;
        // Provide sensible defaults when client invokes store with empty payload
        $payload['title'] = $payload['title'] ?? 'ChangeSet';
        $payload['diff'] = $payload['diff'] ?? ['ops' => []];
        $payload['status'] = $payload['status'] ?? 'draft';

        // If a draft ChangeSet already exists for this scenario+org, return it instead of creating a new empty one
        $existing = ChangeSet::where('scenario_id', $scenarioId)
            ->where('organization_id', $payload['organization_id'])
            ->where('status', 'draft')
            ->latest()
            ->first();
        if ($existing) {
            return response()->json(['success' => true, 'data' => $existing], 200);
        }

        $cs = $this->service->build($payload);

        return response()->json(['success' => true, 'data' => $cs], 201);
    }

    public function preview($id)
    {
        $cs = ChangeSet::findOrFail($id);
        $user = auth()->user();
        if ($cs->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json(['success' => true, 'preview' => $this->service->preview($cs)]);
    }

    public function apply(Request $request, $id)
    {
        $cs = ChangeSet::findOrFail($id);
        $user = $request->user();
        if ($cs->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        $this->authorize('apply', $cs);
        $ignored = $request->input('ignored_indexes', []);
        $applied = $this->service->apply($cs, $user, ['ignored_indexes' => $ignored]);

        return response()->json(['success' => true, 'data' => $applied]);
    }

    public function addOp(Request $request, $id)
    {
        $cs = ChangeSet::findOrFail($id);
        $user = $request->user();
        if ($cs->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $op = $request->only(['type', 'payload']);
        if (empty($op['type'])) {
            return response()->json(['success' => false, 'message' => 'Missing op type'], 422);
        }

        $diff = $cs->diff ?? ['ops' => []];
        $diff['ops'][] = $op;
        $cs->diff = $diff;
        $cs->save();

        return response()->json(['success' => true, 'data' => $cs]);
    }

    public function canApply($id)
    {
        $cs = ChangeSet::findOrFail($id);
        $user = auth()->user();
        if ($cs->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'can_apply' => false], 403);
        }
        $can = auth()->user() ? auth()->user()->can('apply', $cs) : false;

        return response()->json(['success' => true, 'can_apply' => (bool) $can]);
    }

    public function approve(Request $request, $id)
    {
        $cs = ChangeSet::findOrFail($id);
        $user = $request->user();
        if ($cs->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        $this->authorize('apply', $cs);
        $cs->status = 'approved';
        $cs->approved_by = $user->id ?? null;
        $cs->save();

        // Ensure the related Scenario receives versioning metadata when a ChangeSet is approved
        try {
            if (! empty($cs->scenario_id)) {
                $scenario = Scenario::find($cs->scenario_id);
                if ($scenario) {
                    $changed = false;
                    if (empty($scenario->version_group_id)) {
                        $scenario->version_group_id = (string) Str::uuid();
                        $changed = true;
                    }
                    if (empty($scenario->version_number) || $scenario->version_number < 1) {
                        $scenario->version_number = 1;
                        $changed = true;
                    }
                    if (empty($scenario->is_current_version)) {
                        $scenario->is_current_version = true;
                        $changed = true;
                    }

                    if ($changed) {
                        if (! empty($scenario->version_group_id)) {
                            Scenario::where('version_group_id', $scenario->version_group_id)
                                ->where('id', '!=', $scenario->id)
                                ->where('is_current_version', true)
                                ->update(['is_current_version' => false]);
                        }
                        $scenario->approved_by = $user->id ?? $scenario->approved_by ?? null;
                        $scenario->approved_at = $scenario->approved_at ?? now();
                        $scenario->status = $scenario->status === 'approved' ? $scenario->status : 'approved';
                        $scenario->save();
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('ChangeSet approval: failed to assign scenario version metadata: '.$e->getMessage());
        }

        return response()->json(['success' => true, 'data' => $cs]);
    }

    public function reject(Request $request, $id)
    {
        $cs = ChangeSet::findOrFail($id);
        $user = $request->user();
        if ($cs->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        $this->authorize('apply', $cs);
        $cs->status = 'rejected';
        $cs->approved_by = $user->id ?? null;
        $meta = $cs->metadata ?? [];
        $meta['rejected_at'] = now()->toDateTimeString();
        $cs->metadata = $meta;
        $cs->save();

        return response()->json(['success' => true, 'data' => $cs]);
    }
}
