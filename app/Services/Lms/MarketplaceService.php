<?php

namespace App\Services\Lms;

use App\Models\LmsCourse;
use App\Models\LmsMarketplaceListing;
use App\Models\LmsMarketplacePurchase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MarketplaceService
{
    public function createListing(int $orgId, int $courseId, array $data): LmsMarketplaceListing
    {
        return LmsMarketplaceListing::create([
            'organization_id' => $orgId,
            'course_id' => $courseId,
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'] ?? null,
            'currency' => $data['currency'] ?? 'USD',
            'listing_type' => $data['listing_type'] ?? 'free',
            'category' => $data['category'] ?? null,
            'tags' => $data['tags'] ?? null,
        ]);
    }

    public function publishListing(int $listingId): LmsMarketplaceListing
    {
        $listing = LmsMarketplaceListing::findOrFail($listingId);
        $listing->update(['is_published' => true]);

        return $listing;
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $query = LmsMarketplaceListing::published();

        if (! empty($filters['title'])) {
            $query->where('title', 'like', "%{$filters['title']}%");
        }
        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (! empty($filters['listing_type'])) {
            $query->where('listing_type', $filters['listing_type']);
        }
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        $sortField = $filters['sort'] ?? 'created_at';
        $sortDir = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDir);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function purchase(int $listingId, int $buyerOrgId, int $buyerUserId): LmsMarketplacePurchase
    {
        return DB::transaction(function () use ($listingId, $buyerOrgId, $buyerUserId) {
            $listing = LmsMarketplaceListing::findOrFail($listingId);

            $purchase = LmsMarketplacePurchase::create([
                'listing_id' => $listingId,
                'buyer_organization_id' => $buyerOrgId,
                'purchased_by' => $buyerUserId,
                'price_paid' => $listing->price ?? 0,
                'status' => 'completed',
            ]);

            $listing->increment('downloads_count');

            // Clone the course to buyer's organization
            $sourceCourse = $listing->course;
            if ($sourceCourse) {
                LmsCourse::create([
                    'organization_id' => $buyerOrgId,
                    'title' => $sourceCourse->title,
                    'description' => $sourceCourse->description,
                    'category' => $sourceCourse->category ?? null,
                    'is_active' => false,
                ]);
            }

            return $purchase;
        });
    }

    public function getSellerListings(int $orgId)
    {
        return LmsMarketplaceListing::forOrganization($orgId)->with('course')->latest()->get();
    }

    public function getPurchases(int $orgId)
    {
        return LmsMarketplacePurchase::where('buyer_organization_id', $orgId)
            ->with('listing')
            ->latest()
            ->get();
    }
}
