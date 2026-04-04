<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsLesson;
use App\Services\Lms\MicrolearningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MicrolearningController extends Controller
{
    public function __construct(
        protected MicrolearningService $service,
    ) {}

    public function show(Request $request, int $lesson): JsonResponse
    {
        $micro = $this->service->getForLesson($lesson);

        if (! $micro) {
            return response()->json(['data' => null]);
        }

        return response()->json([
            'data' => [
                'micro_content' => $micro,
                'card_count' => $micro->card_count,
            ],
        ]);
    }

    public function store(Request $request, int $lesson): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $request->validate([
            'cards' => 'required|array|min:1',
            'cards.*.type' => 'required|string|in:text,image,quiz,flashcard,tip',
            'cards.*.title' => 'required|string|max:255',
            'cards.*.content' => 'nullable|string',
            'cards.*.media_url' => 'nullable|string',
            'cards.*.quiz_data' => 'nullable|array',
            'estimated_minutes' => 'required|integer|min:1|max:5',
        ]);

        $existing = $this->service->getForLesson($lesson);

        if ($existing) {
            $this->service->updateCards($existing->id, $request->input('cards'));
            $existing->update(['estimated_minutes' => min($request->input('estimated_minutes'), 5)]);
            $existing->refresh();

            return response()->json(['data' => $existing]);
        }

        $micro = $this->service->createMicroContent(
            $lesson,
            $orgId,
            $request->input('cards'),
            $request->input('estimated_minutes'),
        );

        return response()->json(['data' => $micro], 201);
    }

    public function generate(Request $request, int $lesson): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $lessonModel = LmsLesson::findOrFail($lesson);

        $content = $lessonModel->content_body ?? $lessonModel->title;
        $cards = $this->service->generateFromContent($content, $orgId);

        return response()->json(['data' => ['cards' => $cards]]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
