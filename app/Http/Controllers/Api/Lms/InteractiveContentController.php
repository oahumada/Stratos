<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\InteractiveContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InteractiveContentController extends Controller
{
    public function __construct(
        protected InteractiveContentService $service,
    ) {}

    public function index(Request $request, int $lesson): JsonResponse
    {
        $contents = $this->service->getForLesson($lesson);

        return response()->json(['data' => $contents]);
    }

    public function store(Request $request, int $lesson): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $request->validate([
            'widget_type' => 'required|string|in:accordion,tabs,timeline,hotspot,drag_drop,fill_blanks',
            'config' => 'required|array',
            'title' => 'required|string|max:255',
        ]);

        $content = $this->service->create(
            $lesson,
            $orgId,
            $request->input('widget_type'),
            $request->input('config'),
            $request->input('title'),
        );

        return response()->json(['data' => $content], 201);
    }

    public function update(Request $request, int $interactiveContent): JsonResponse
    {
        $request->validate([
            'config' => 'required|array',
            'title' => 'required|string|max:255',
        ]);

        $content = $this->service->update(
            $interactiveContent,
            $request->input('config'),
            $request->input('title'),
        );

        return response()->json(['data' => $content]);
    }

    public function destroy(Request $request, int $interactiveContent): JsonResponse
    {
        $content = \App\Models\LmsInteractiveContent::findOrFail($interactiveContent);
        $content->delete();

        return response()->json(['success' => true]);
    }

    public function widgetTypes(): JsonResponse
    {
        return response()->json(['data' => $this->service->getAvailableWidgetTypes()]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
