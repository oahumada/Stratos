<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected WebhookService $webhookService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $webhooks = $this->webhookService->getForOrganization($orgId);

        return response()->json($webhooks);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url|max:500',
            'secret' => 'required|string|min:8|max:255',
            'events' => 'required|array|min:1',
            'events.*' => 'string|in:enrollment.completed,course.completed,quiz.submitted,compliance.overdue,certificate.issued',
        ]);

        $orgId = $this->resolveOrganizationId($request);
        $webhook = $this->webhookService->register($orgId, $request->url, $request->secret, $request->events);

        return response()->json($webhook, 201);
    }

    public function update(Request $request, int $webhook): JsonResponse
    {
        $request->validate([
            'url' => 'nullable|url|max:500',
            'secret' => 'nullable|string|min:8|max:255',
            'events' => 'nullable|array|min:1',
            'events.*' => 'string|in:enrollment.completed,course.completed,quiz.submitted,compliance.overdue,certificate.issued',
            'is_active' => 'nullable|boolean',
        ]);

        $orgId = $this->resolveOrganizationId($request);
        $model = \App\Models\LmsWebhook::where('id', $webhook)
            ->where('organization_id', $orgId)
            ->firstOrFail();

        $model->update($request->only(['url', 'secret', 'events', 'is_active']));

        return response()->json($model->fresh());
    }

    public function destroy(Request $request, int $webhook): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $model = \App\Models\LmsWebhook::where('id', $webhook)
            ->where('organization_id', $orgId)
            ->firstOrFail();

        $model->delete();

        return response()->json(['message' => 'Webhook deleted']);
    }

    public function test(Request $request, int $webhook): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $model = \App\Models\LmsWebhook::where('id', $webhook)
            ->where('organization_id', $orgId)
            ->firstOrFail();

        $log = $this->webhookService->testWebhook($model->id);

        return response()->json($log);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
