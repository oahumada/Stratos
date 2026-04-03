<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Lms\StoreCmsArticleRequest;
use App\Jobs\GenerateLmsArticle;
use Illuminate\Http\JsonResponse;

class CmsArticleController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id para CMS LMS.';

    public function store(StoreCmsArticleRequest $request): JsonResponse
    {
        $data = $request->validated();

        $organizationId = (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);

        if ($organizationId <= 0) {
            return response()->json([
                'message' => self::ORG_RESOLUTION_ERROR,
            ], 422);
        }

        $topic = (string) $data['topic'];
        $options = $data['options'] ?? [];
        $options['auto_publish'] = $data['auto_publish'] ?? false;
        $options['author_id'] = $data['author_id'] ?? null;

        $job = new GenerateLmsArticle($organizationId, $topic, $options);
        dispatch($job);

        return response()->json(['success' => true, 'message' => 'Article generation queued'], 202);
    }
}
