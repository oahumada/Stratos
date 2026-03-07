<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicJobController extends Controller
{
    /**
     * List all public job openings for a specific organization.
     */
    public function index(string $tenantSlug): JsonResponse
    {
        $organization = Organization::where('subdomain', $tenantSlug)->firstOrFail();

        $openings = JobOpening::withoutGlobalScope('organization')
            ->where('organization_id', $organization->id)
            ->where('is_external', true)
            ->where('status', 'open')
            ->with(['role.skills'])
            ->get();

        return response()->json([
            'organization' => [
                'name' => $organization->name,
                'logo' => $organization->logo_url, // Assuming it has one
            ],
            'openings' => $openings,
        ]);
    }

    /**
     * Show a specific public job opening details.
     */
    public function show(string $tenantSlug, string $jobSlug): JsonResponse
    {
        $organization = Organization::where('subdomain', $tenantSlug)->firstOrFail();

        $opening = JobOpening::withoutGlobalScope('organization')
            ->where('organization_id', $organization->id)
            ->where('is_external', true)
            ->where('slug', $jobSlug)
            ->with(['role.skills'])
            ->firstOrFail();

        return response()->json([
            'opening' => $opening,
        ]);
    }

    /**
     * Handle unauthenticated application (Simplified for demo).
     */
    public function apply(Request $request, string $tenantSlug, string $jobSlug): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'resume_url' => 'nullable|url',
        ]);

        // Logic to store external candidate and link to application...
        // For Phase 3, we just return success acknowledging the receipt.

        return response()->json([
            'status' => 'success',
            'message' => 'Tu postulación ha sido recibida por el motor de Stratos Magnet. Analizaremos tu perfil con nuestra IA y te contactaremos.',
        ]);
    }
}
