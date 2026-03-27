<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scenario;
use App\Models\ApprovalRequest;
use App\Services\ScenarioPlanning\ScenarioWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * ScenarioApprovalController
 *
 * Handles scenario workflow and approval endpoints
 * - Submit scenarios for approval
 * - Approve/reject approval requests
 * - Activate approved scenarios
 * - Retrieve approval matrix
 * - Access execution plans
 */
class ScenarioApprovalController extends Controller
{
    public function __construct(private ScenarioWorkflowService $workflowService)
    {
    }

    /**
     * Submit scenario for approval
     *
     * POST /api/scenarios/{id}/submit-approval
     * Transitions: draft → pending_approval
     * Creates approval requests for all required approvers
     */
    public function submitForApproval(Request $request, int $id): JsonResponse
    {
        $scenario = Scenario::findOrFail($id);

        // Authorization
        $this->authorize('submit', $scenario);

        // Validate request
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        // Process submission
        $result = $this->workflowService->submitForApproval(
            $id,
            auth()->id(),
            $validated['notes'] ?? null
        );

        if ($result['status'] !== 'success') {
            return response()->json($result, 422);
        }

        return response()->json($result, 201);
    }

    /**
     * Approve approval request
     *
     * POST /api/approval-requests/{id}/approve
     * Marks approval as approved
     * Transitions scenario if all approvals complete
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $approvalRequest = ApprovalRequest::findOrFail($id);

        // Authorization - only assigned approver can approve
        if ($approvalRequest->approver_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        // Validate
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        // Process approval
        $result = $this->workflowService->approve(
            $id,
            auth()->id(),
            $validated['notes'] ?? null
        );

        if ($result['status'] !== 'success') {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    /**
     * Reject approval request
     *
     * POST /api/approval-requests/{id}/reject
     * Marks approval as rejected
     * Transitions scenario back to draft
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $approvalRequest = ApprovalRequest::findOrFail($id);

        // Authorization - only assigned approver can reject
        if ($approvalRequest->approver_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        // Validate
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Process rejection
        $result = $this->workflowService->reject(
            $id,
            auth()->id(),
            $validated['reason']
        );

        if ($result['status'] !== 'success') {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    /**
     * Get approval matrix for scenario
     *
     * GET /api/scenarios/{id}/approval-matrix
     * Returns list of required approvers and their current status
     */
    public function getApprovalMatrix(int $id): JsonResponse
    {
        $scenario = Scenario::findOrFail($id);

        // Authorization
        $this->authorize('view', $scenario);

        // Get approval requests for this scenario
        $approvalRequests = ApprovalRequest::where('approvable_id', $scenario->id)
            ->where('approvable_type', Scenario::class)
            ->get();

        // Build approver status list
        $approvers = $approvalRequests->map(function ($ar) {
            return [
                'id' => $ar->id,
                'approver_id' => $ar->approver_id,
                'approver_name' => $ar->approver?->name ?? 'Unknown',
                'status' => $ar->status,
                'approved_at' => $ar->signed_at,
                'token' => $ar->token,
            ];
        });

        return response()->json([
            'status' => 'success',
            'scenario_id' => $scenario->id,
            'decision_status' => $scenario->decision_status,
            'approvers' => $approvers,
            'required_approvals' => count($approvers),
            'approvals_complete' => $approvers->filter(fn($a) => $a['status'] === 'approved')->count(),
            'approvals_pending' => $approvers->filter(fn($a) => $a['status'] === 'pending')->count(),
            'approvals_rejected' => $approvers->filter(fn($a) => $a['status'] === 'rejected')->count(),
        ]);
    }

    /**
     * Activate approved scenario
     *
     * POST /api/scenarios/{id}/activate
     * Transitions: approved → active
     * Generates execution plan
     */
    public function activate(Request $request, int $id): JsonResponse
    {
        $scenario = Scenario::findOrFail($id);

        // Authorization
        $this->authorize('activate', $scenario);

        // Process activation
        $result = $this->workflowService->activate($id, auth()->id());

        if ($result['status'] !== 'success') {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    /**
     * Get execution plan
     *
     * GET /api/scenarios/{id}/execution-plan
     * Returns phases, milestones, and tasks for active scenario
     */
    public function getExecutionPlan(int $id): JsonResponse
    {
        $scenario = Scenario::findOrFail($id);

        // Authorization
        $this->authorize('view', $scenario);

        // Only active scenarios have execution plans
        if ($scenario->decision_status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Execution plan only available for active scenarios',
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'scenario_id' => $scenario->id,
            'execution_plan' => [
                'timeline_weeks' => $scenario->time_horizon_weeks ?? 26,
                'phases' => [
                    [
                        'phase' => 1,
                        'name' => 'Assessment & Planning',
                        'start_week' => 1,
                        'end_week' => 4,
                        'status' => 'planned',
                    ],
                    [
                        'phase' => 2,
                        'name' => 'Capability Building',
                        'start_week' => 5,
                        'end_week' => 12,
                        'status' => 'planned',
                    ],
                    [
                        'phase' => 3,
                        'name' => 'Execution & Transition',
                        'start_week' => 13,
                        'end_week' => 20,
                        'status' => 'planned',
                    ],
                    [
                        'phase' => 4,
                        'name' => 'Stabilization & Review',
                        'start_week' => 21,
                        'end_week' => $scenario->time_horizon_weeks ?? 26,
                        'status' => 'planned',
                    ],
                ],
                'milestones' => [
                    ['milestone' => 'Kickoff', 'week' => 1, 'status' => 'planned'],
                    ['milestone' => 'Assessment Complete', 'week' => 4, 'status' => 'planned'],
                    ['milestone' => 'Pilot Launch', 'week' => 8, 'status' => 'planned'],
                    ['milestone' => 'Full Rollout', 'week' => 14, 'status' => 'planned'],
                    ['milestone' => 'Go-Live', 'week' => $scenario->time_horizon_weeks ?? 26, 'status' => 'planned'],
                ],
                'tasks' => [
                    ['task_id' => 1, 'name' => 'Stakeholder Alignment', 'phase' => 1, 'status' => 'pending'],
                    ['task_id' => 2, 'name' => 'Resource Planning', 'phase' => 1, 'status' => 'pending'],
                    ['task_id' => 3, 'name' => 'Training Preparation', 'phase' => 2, 'status' => 'pending'],
                    ['task_id' => 4, 'name' => 'Capability Delivery', 'phase' => 2, 'status' => 'pending'],
                    ['task_id' => 5, 'name' => 'Execution Kickoff', 'phase' => 3, 'status' => 'pending'],
                    ['task_id' => 6, 'name' => 'Transition Management', 'phase' => 3, 'status' => 'pending'],
                    ['task_id' => 7, 'name' => 'Lessons Learned', 'phase' => 4, 'status' => 'pending'],
                ],
            ],
        ]);
    }
}
