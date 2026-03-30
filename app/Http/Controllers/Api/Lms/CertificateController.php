<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsCertificate;
use App\Services\Talent\Lms\CertificateService;
use Illuminate\Http\JsonResponse;

class CertificateController extends Controller
{
    protected CertificateService $service;

    public function __construct(CertificateService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $list = LmsCertificate::latest()->limit(50)->get();
        return response()->json(['data' => $list]);
    }

    public function show($id): JsonResponse
    {
        $cert = LmsCertificate::findOrFail($id);
        return response()->json(['data' => $cert]);
    }

    public function download($id): JsonResponse
    {
        $cert = LmsCertificate::findOrFail($id);
        return response()->json(['url' => $cert->certificate_url]);
    }

    public function verify($id): JsonResponse
    {
        $cert = LmsCertificate::findOrFail($id);
        $ok = $this->service->verify($cert);
        return response()->json(['verified' => $ok]);
    }

    public function revoke($id): JsonResponse
    {
        $cert = LmsCertificate::findOrFail($id);
        $this->service->revoke($cert, 'revoked by admin');
        return response()->json(['success' => true]);
    }
}
