<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Lms\StoreCmsArticleRequest;
use App\Jobs\GenerateLmsArticle;
use Illuminate\Http\JsonResponse;

class CmsArticleController extends Controller
{
    public function store(StoreCmsArticleRequest $request): JsonResponse
    {
        $data = $request->validated();

        $organizationId = (int) $data['organization_id'];
        $topic = (string) $data['topic'];
        $options = $data['options'] ?? [];
        $options['auto_publish'] = $data['auto_publish'] ?? false;
        $options['author_id'] = $data['author_id'] ?? null;

        $job = new GenerateLmsArticle($organizationId, $topic, $options);
        dispatch($job);

        return response()->json(['success' => true, 'message' => 'Article generation queued'], 202);
    }
}
