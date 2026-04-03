<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsCertificate;
use App\Services\Talent\Lms\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id para certificados LMS.';

    protected CertificateService $service;

    public function __construct(CertificateService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $organizationId = (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);

        if ($organizationId <= 0) {
            return response()->json([
                'message' => self::ORG_RESOLUTION_ERROR,
            ], 422);
        }

        $list = LmsCertificate::query()
            ->where('organization_id', $organizationId)
            ->latest()
            ->limit(50)
            ->get();

        return response()->json(['data' => $list]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $cert = $this->findCertificateForCurrentOrganization($request, $id);

        if ($cert instanceof JsonResponse) {
            return $cert;
        }

        return response()->json(['data' => $cert]);
    }

    public function download(Request $request, $id): JsonResponse
    {
        $cert = $this->findCertificateForCurrentOrganization($request, $id);

        if ($cert instanceof JsonResponse) {
            return $cert;
        }

        return response()->json(['url' => $cert->certificate_url]);
    }

    public function verify($id): JsonResponse
    {
        $cert = LmsCertificate::findOrFail($id);
        $ok = $this->service->verify($cert);

        return response()->json(['verified' => $ok]);
    }

    public function revoke(Request $request, $id): JsonResponse
    {
        $cert = $this->findCertificateForCurrentOrganization($request, $id);

        if ($cert instanceof JsonResponse) {
            return $cert;
        }

        $this->service->revoke($cert, 'revoked by admin');

        return response()->json(['success' => true]);
    }

    private function findCertificateForCurrentOrganization(Request $request, int|string $id): LmsCertificate|JsonResponse
    {
        $organizationId = (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);

        if ($organizationId <= 0) {
            return response()->json([
                'message' => self::ORG_RESOLUTION_ERROR,
            ], 422);
        }

        return LmsCertificate::query()
            ->where('organization_id', $organizationId)
            ->findOrFail($id);
    }
}
