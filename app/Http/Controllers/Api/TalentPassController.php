<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TalentPass;
use App\Services\TalentPassService;
use App\Services\CVExportService;
use Illuminate\Http\Request;

class TalentPassController extends Controller
{
    public function __construct(
        private TalentPassService $talentPassService,
        private CVExportService $exportService,
    ) {}

    /**
     * List all talent passes for organization
     */
    public function index(Request $request)
    {
        $organizationId = auth()->user()?->organization_id;

        $talentPasses = TalentPass::byOrganization($organizationId)
            ->with(['person', 'skills', 'credentials', 'experiences'])
            ->paginate(15);

        return response()->json($talentPasses);
    }

    /**
     * Get single talent pass public view (by ULID)
     */
    public function showPublic(string $publicId)
    {
        $talentPass = TalentPass::where('ulid', $publicId)
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->with(['person', 'skills', 'credentials', 'experiences'])
            ->firstOrFail();

        // Record view
        $this->talentPassService->recordView($talentPass);

        return response()->json([
            'data' => $talentPass,
            'completeness' => $this->exportService->getCompletenessScore($talentPass),
        ]);
    }

    /**
     * Get single talent pass (authenticated)
     */
    public function show($people_id)
    {
        $talentPass = TalentPass::where('people_id', $people_id)
            ->with(['person', 'skills', 'credentials', 'experiences'])
            ->firstOrFail();

        $this->authorize('view', $talentPass);

        return response()->json($talentPass);
    }

    /**
     * Create new talent pass
     */
    public function store(Request $request)
    {
        $organizationId = auth()->user()?->organization_id;
        $peopleId = auth()->user()?->people_id;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:2000',
            'visibility' => 'required|in:private,public',
        ]);

        $talentPass = $this->talentPassService->create(
            $organizationId,
            $peopleId,
            $validated
        );

        return response()->json($talentPass, 201);
    }

    /**
     * Update talent pass
     */
    public function update($id, Request $request)
    {
        $talentPass = TalentPass::findOrFail($id);
        $this->authorize('update', $talentPass);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'summary' => 'nullable|string|max:2000',
            'visibility' => 'sometimes|in:private,public',
        ]);

        $updated = $this->talentPassService->update($id, $validated);

        return response()->json($updated);
    }

    /**
     * Delete talent pass (soft delete)
     */
    public function destroy($id)
    {
        $talentPass = TalentPass::findOrFail($id);
        $this->authorize('delete', $talentPass);

        $talentPass->delete();

        return response()->json(null, 204);
    }

    /**
     * Publish talent pass
     */
    public function publish($id)
    {
        $talentPass = TalentPass::findOrFail($id);
        $this->authorize('update', $talentPass);

        $this->talentPassService->publish($id);

        return response()->json(['message' => 'Talent pass published', 'status' => 'published']);
    }

    /**
     * Archive talent pass
     */
    public function archive($id)
    {
        $talentPass = TalentPass::findOrFail($id);
        $this->authorize('update', $talentPass);

        $this->talentPassService->archive($id);

        return response()->json(['message' => 'Talent pass archived', 'status' => 'archived']);
    }

    /**
     * Clone talent pass with relationships
     */
    public function clone($id)
    {
        $talentPass = TalentPass::findOrFail($id);
        $this->authorize('view', $talentPass);

        $cloned = $this->talentPassService->clone($id);

        return response()->json($cloned->load(['person', 'skills', 'credentials', 'experiences']), 201);
    }

    /**
     * Export talent pass (PDF/JSON/LinkedIn)
     */
    public function export($id, Request $request)
    {
        $talentPass = TalentPass::findOrFail($id);
        $this->authorize('view', $talentPass);

        $format = $request->query('format', 'pdf');

        return match ($format) {
            'json' => response()->json(json_decode($this->exportService->exportJson($talentPass), true)),
            'linkedin' => response()->json(json_decode($this->exportService->exportLinkedIn($talentPass), true)),
            'pdf' => response($this->exportService->exportPdf($talentPass), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $talentPass->title . '.pdf"',
            ]),
            default => response()->json(['error' => 'Invalid format'], 400),
        };
    }

    /**
     * Generate shareable link
     */
    public function share($id)
    {
        $talentPass = TalentPass::findOrFail($id);
        $this->authorize('update', $talentPass);

        $shareableLink = $this->exportService->generateShareableLink($talentPass);

        return response()->json(['link' => $shareableLink, 'publicId' => $talentPass->ulid]);
    }
}
