<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    use \App\Traits\ApiResponses;

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\SupportTicket::with(['reporter', 'assignee']);

        // Si no es admin, solo ve los de su organización
        if (! $user->hasRole('admin')) {
            $query->where('organization_id', $user->organization_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        return $this->success($query->latest()->get(), 'Tickets fetched successfully.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:bug,improvement,code_quality,ux',
            'priority' => 'required|in:low,medium,high,critical',
            'context' => 'nullable|array',
            'file_path' => 'nullable|string',
        ]);

        $user = auth()->user();

        $ticket = \App\Models\SupportTicket::create([
            'organization_id' => $user->organization_id,
            'reporter_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'priority' => $validated['priority'],
            'context' => $validated['context'] ?? [
                'url' => $request->header('Referer'),
                'user_agent' => $request->userAgent(),
            ],
            'file_path' => $validated['file_path'] ?? null,
            'status' => 'open',
        ]);

        return $this->success($ticket, 'Ticket created successfully.', 201);
    }

    public function show($id)
    {
        $ticket = \App\Models\SupportTicket::with(['reporter', 'assignee', 'organization'])->findOrFail($id);

        return $this->success($ticket, 'Ticket details fetched.');
    }

    public function update(Request $request, $id)
    {
        $ticket = \App\Models\SupportTicket::findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|in:open,in_analysis,in_progress,resolved,closed',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'description' => 'sometimes|string',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'resolved' && $ticket->status !== 'resolved') {
            $ticket->resolved_at = now();
        }

        $ticket->update($validated);

        return $this->success($ticket, 'Ticket updated successfully.');
    }

    public function destroy($id)
    {
        $ticket = \App\Models\SupportTicket::findOrFail($id);
        $ticket->delete();

        return $this->success(null, 'Ticket deleted successfully.');
    }

    public function metrics()
    {
        $metrics = [
            'total' => \App\Models\SupportTicket::count(),
            'by_status' => \App\Models\SupportTicket::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status'),
            'by_type' => \App\Models\SupportTicket::selectRaw('type, count(*) as count')->groupBy('type')->pluck('count', 'type'),
            'critical_bugs' => \App\Models\SupportTicket::where('type', 'bug')->where('priority', 'critical')->count(),
        ];

        return $this->success($metrics, 'Ticket metrics fetched.');
    }
}
