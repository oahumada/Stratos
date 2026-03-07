<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    use \App\Traits\ApiResponses;

    /**
     * Display a listing of support tickets with multi-tenancy logic.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $query = SupportTicket::with(['reporter', 'assignee']);

        // Multi-tenancy: restrict to organization unless admin
        if (! $user->hasRole('admin')) {
            $query->where('organization_id', $user->organization_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->query('type'));
        }

        return $this->success($query->latest()->get(), 'Tickets fetched successfully.');
    }

    /**
     * Store a newly created support ticket.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:bug,improvement,code_quality,ux',
            'priority' => 'required|in:low,medium,high,critical',
            'context' => 'nullable|array',
            'file_path' => 'nullable|string',
        ]);

        /** @var User $user */
        $user = auth()->user();

        $ticket = SupportTicket::create([
            'organization_id' => $user->organization_id,
            'reporter_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'priority' => $validated['priority'],
            'context' => $validated['context'] ?? [
                'url' => (string) $request->header('Referer'),
                'user_agent' => (string) $request->userAgent(),
                'screen_size' => 'unknown',
            ],
            'file_path' => $validated['file_path'] ?? null,
            'status' => 'open',
        ]);

        return $this->success($ticket, 'Ticket created successfully.', 201);
    }

    /**
     * Display the specified support ticket.
     */
    public function show(SupportTicket $supportTicket): JsonResponse
    {
        $supportTicket->load(['reporter', 'assignee', 'organization']);

        return $this->success($supportTicket, 'Ticket details fetched.');
    }

    /**
     * Update the specified support ticket.
     */
    public function update(Request $request, SupportTicket $supportTicket): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:open,in_analysis,in_progress,resolved,closed',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'description' => 'sometimes|string',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'resolved' && $supportTicket->status !== 'resolved') {
            $supportTicket->resolved_at = \Illuminate\Support\Carbon::now();
        }

        $supportTicket->update($validated);

        return $this->success($supportTicket, 'Ticket updated successfully.');
    }

    /**
     * Remove the specified support ticket.
     */
    public function destroy(SupportTicket $supportTicket): JsonResponse
    {
        $supportTicket->delete();

        return $this->success(null, 'Ticket deleted successfully.');
    }

    /**
     * Get aggregate metrics for the quality hub.
     */
    public function metrics(): JsonResponse
    {
        $metrics = [
            'total' => SupportTicket::count(),
            'by_status' => SupportTicket::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status'),
            'by_type' => SupportTicket::selectRaw('type, count(*) as count')->groupBy('type')->pluck('count', 'type'),
            'critical_bugs' => SupportTicket::where('type', 'bug')->where('priority', 'critical')->count(),
        ];

        return $this->success($metrics, 'Ticket metrics fetched.');
    }
}
