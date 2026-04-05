<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\StoreCommunityRequest;
use App\Http\Requests\Lms\UpdateCommunityRequest;
use App\Models\LmsCommunity;
use App\Services\Lms\CommunityHealthService;
use App\Services\Lms\CommunityProgressionService;
use App\Services\Lms\CommunityService;
use App\Services\Lms\CommunityFormationService;
use App\Services\Lms\MentorMatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function __construct(
        private readonly CommunityService $communityService,
        private readonly CommunityHealthService $healthService,
        private readonly CommunityProgressionService $progressionService,
        private readonly MentorMatchingService $mentorMatchingService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $filters = $request->only(['type', 'status', 'search']);

        $communities = $this->communityService->list($orgId, $filters, (int) $request->input('per_page', 15));

        return response()->json($communities);
    }

    public function store(StoreCommunityRequest $request): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $data = $request->validated();
        $data['facilitator_id'] = $request->user()->id;

        $community = $this->communityService->create($orgId, $data);

        return response()->json(['success' => true, 'community' => $community], 201);
    }

    public function show(Request $request, LmsCommunity $community): JsonResponse
    {
        $this->authorizeOrganization($request, $community);

        $community->loadCount('members');
        $community->load(['facilitator', 'course']);

        $health = $this->healthService->assessHealth($community);

        return response()->json([
            'community' => $community,
            'health' => $health,
        ]);
    }

    public function update(UpdateCommunityRequest $request, LmsCommunity $community): JsonResponse
    {
        $this->authorizeOrganization($request, $community);

        $community = $this->communityService->update($community, $request->validated());

        return response()->json(['success' => true, 'community' => $community]);
    }

    public function destroy(Request $request, LmsCommunity $community): JsonResponse
    {
        $this->authorizeOrganization($request, $community);

        $this->communityService->archive($community);

        return response()->json(['success' => true, 'message' => 'Community archived.']);
    }

    public function members(Request $request, LmsCommunity $community): JsonResponse
    {
        $this->authorizeOrganization($request, $community);

        $filters = $request->only(['role', 'active_only']);

        $members = $this->communityService->getMembers($community, $filters, (int) $request->input('per_page', 15));

        return response()->json($members);
    }

    public function join(Request $request, LmsCommunity $community): JsonResponse
    {
        $this->authorizeOrganization($request, $community);

        $member = $this->communityService->join($community, $request->user());

        return response()->json(['success' => true, 'member' => $member], 201);
    }

    public function leave(Request $request, LmsCommunity $community): JsonResponse
    {
        $this->authorizeOrganization($request, $community);

        $this->communityService->leave($community, $request->user());

        return response()->json(['success' => true, 'message' => 'Left community.']);
    }

    public function activities(Request $request, LmsCommunity $community): JsonResponse
    {
        $this->authorizeOrganization($request, $community);

        $activities = $community->activities()
            ->with('user')
            ->latest()
            ->paginate((int) $request->input('per_page', 20));

        return response()->json($activities);
    }

    public function health(Request $request, LmsCommunity $community): JsonResponse
    {
        $this->authorizeOrganization($request, $community);

        $health = $this->healthService->recalculateAndSave($community);

        return response()->json($health);
    }

    public function progression(Request $request, LmsCommunity $community): JsonResponse
    {
        $this->authorizeOrganization($request, $community);

        $member = $community->members()
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $member) {
            return response()->json(['message' => 'Not a member of this community.'], 404);
        }

        $this->communityService->updateMemberStats($member);
        $progression = $this->progressionService->evaluateProgression($member);

        return response()->json($progression);
    }

    public function mentors(Request $request, LmsCommunity $community): JsonResponse
    {
        $this->authorizeOrganization($request, $community);

        $result = $this->mentorMatchingService->findMentors($community, $request->user());

        return response()->json($result);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }

    private function authorizeOrganization(Request $request, LmsCommunity $community): void
    {
        $orgId = $this->resolveOrganizationId($request);

        if ((int) $community->organization_id !== $orgId) {
            abort(403, 'Community does not belong to your organization.');
        }
    }

    public function knowledgeBase(Request $request, LmsCommunity $community): JsonResponse
    {
        $this->authorizeOrganization($request, $community);

        $articles = $community->knowledgeArticles()
            ->with('author:id,name')
            ->when($request->category, fn ($q, $cat) => $q->where('category', $cat))
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        return response()->json($articles);
    }

    public function storeKnowledge(Request $request, LmsCommunity $community): JsonResponse
    {
        $this->authorizeOrganization($request, $community);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|in:best_practice,tutorial,faq,resource,wiki',
            'tags' => 'nullable|array',
        ]);

        $article = $community->knowledgeArticles()->create([
            'author_id' => $request->user()->id,
            ...$validated,
        ]);

        $this->communityService->recordActivity($community, $request->user(), 'knowledge_share', [
            'title' => $article->title,
            'knowledge_id' => $article->id,
        ]);

        return response()->json($article, 201);
    }

    public function suggestFromGaps(Request $request): JsonResponse
    {
        $request->validate([
            'gaps' => 'required|array',
            'gaps.*.skill_name' => 'required|string',
            'gaps.*.gap_size' => 'required|numeric',
            'gaps.*.gap_type' => 'nullable|string|in:proficiency,headcount',
            'gaps.*.affected_count' => 'required|integer',
            'threshold' => 'nullable|numeric|min:0.5',
            'min_affected' => 'nullable|integer|min:1',
        ]);

        $orgId = $this->resolveOrganizationId($request);
        $service = app(CommunityFormationService::class);
        $suggestions = $service->analyzeAndSuggest(
            $orgId,
            $request->gaps,
            $request->float('threshold', 2.0),
            $request->integer('min_affected', 3),
        );

        return response()->json(['suggestions' => $suggestions]);
    }
}
