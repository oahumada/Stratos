<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\MarketplaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function __construct(
        protected MarketplaceService $marketplaceService,
    ) {}

    public function browse(Request $request): JsonResponse
    {
        $filters = $request->only(['title', 'category', 'listing_type', 'min_price', 'max_price', 'sort', 'direction', 'per_page']);
        $listings = $this->marketplaceService->search($filters);

        return response()->json($listings);
    }

    public function myListings(Request $request): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $listings = $this->marketplaceService->getSellerListings($orgId);

        return response()->json($listings);
    }

    public function createListing(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|integer|exists:lms_courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'listing_type' => 'nullable|string|in:free,paid,subscription',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
        ]);

        $orgId = $this->resolveOrganizationId($request);
        $listing = $this->marketplaceService->createListing($orgId, $request->course_id, $request->all());

        return response()->json($listing, 201);
    }

    public function publish(Request $request, int $listing): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $model = \App\Models\LmsMarketplaceListing::where('id', $listing)
            ->where('organization_id', $orgId)
            ->firstOrFail();

        $published = $this->marketplaceService->publishListing($model->id);

        return response()->json($published);
    }

    public function purchase(Request $request, int $listing): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $purchase = $this->marketplaceService->purchase($listing, $orgId, $request->user()->id);

        return response()->json($purchase, 201);
    }

    public function purchases(Request $request): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $purchases = $this->marketplaceService->getPurchases($orgId);

        return response()->json($purchases);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
