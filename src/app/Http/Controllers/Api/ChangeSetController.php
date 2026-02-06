<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ChangeSetService;
use App\Models\ChangeSet;

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
        $payload['status'] = $payload['status'] ?? 'draft';

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
