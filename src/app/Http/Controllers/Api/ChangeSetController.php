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

    public function apply($id)
    {
        $cs = ChangeSet::findOrFail($id);
        $user = auth()->user();
        if ($cs->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        $this->authorize('apply', $cs);
        $applied = $this->service->apply($cs, $user);
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
}
