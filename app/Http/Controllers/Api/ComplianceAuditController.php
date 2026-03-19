<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventStore;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Services\Security\StratosSignatureService;
use App\Services\TalentRoiService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComplianceAuditController extends Controller
{
    use ApiResponses;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $perPage = (int) $request->integer('per_page', 25);
        $perPage = max(1, min($perPage, 100));

        $query = EventStore::query()
            ->where('organization_id', $user->organization_id)
            ->when($request->filled('event_name'), function ($builder) use ($request) {
                $builder->where('event_name', 'like', '%'.$request->string('event_name')->toString().'%');
            })
            ->when($request->filled('aggregate_type'), function ($builder) use ($request) {
                $builder->where('aggregate_type', $request->string('aggregate_type')->toString());
            })
            ->when($request->filled('from'), function ($builder) use ($request) {
                $builder->where('occurred_at', '>=', Carbon::parse($request->string('from')->toString())->startOfDay());
            })
            ->when($request->filled('to'), function ($builder) use ($request) {
                $builder->where('occurred_at', '<=', Carbon::parse($request->string('to')->toString())->endOfDay());
            })
            ->orderByDesc('occurred_at');

        return $this->success($query->paginate($perPage), 'Audit events fetched successfully.');
    }

    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $baseQuery = EventStore::query()->where('organization_id', $user->organization_id);

        $metrics = [
            'total_events' => (clone $baseQuery)->count(),
            'events_last_24h' => (clone $baseQuery)->where('occurred_at', '>=', now()->subDay())->count(),
            'unique_event_names' => (clone $baseQuery)->distinct('event_name')->count('event_name'),
            'unique_aggregates' => (clone $baseQuery)->select(['aggregate_type', 'aggregate_id'])->get()->unique(function ($event) {
                return $event->aggregate_type.'#'.$event->aggregate_id;
            })->count(),
            'top_event_names' => (clone $baseQuery)
                ->selectRaw('event_name, COUNT(*) as total')
                ->groupBy('event_name')
                ->orderByDesc('total')
                ->limit(10)
                ->pluck('total', 'event_name'),
        ];

        return $this->success($metrics, 'Audit summary fetched successfully.');
    }

    public function iso30414Summary(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) $user->organization_id;

        $roleArchitecture = DB::table('role_skills')
            ->selectRaw('role_id, COUNT(*) as required_skills_count, SUM(CASE WHEN is_critical THEN 1 ELSE 0 END) as critical_skills_count, AVG(required_level) as avg_required_level')
            ->groupBy('role_id');

        $peopleForReplacement = DB::table('people as p')
            ->leftJoin('roles as r', 'r.id', '=', 'p.role_id')
            ->leftJoinSub($roleArchitecture, 'ra', function ($join) {
                $join->on('ra.role_id', '=', 'p.role_id');
            })
            ->where('p.organization_id', $organizationId)
            ->whereNull('p.deleted_at')
            ->selectRaw('p.id as people_id, p.salary as people_salary, r.id as role_id, r.name as role_name, r.base_salary as role_base_salary, COALESCE(ra.required_skills_count, 0) as required_skills_count, COALESCE(ra.critical_skills_count, 0) as critical_skills_count, COALESCE(ra.avg_required_level, 3) as avg_required_level')
            ->get();

        $replacementDetails = $peopleForReplacement->map(function ($item) {
            $baseSalary = (float) ($item->role_base_salary ?? $item->people_salary ?? TalentRoiService::AVG_ANNUAL_SALARY);
            $avgRequiredLevel = (float) $item->avg_required_level;
            $criticalSkills = (int) $item->critical_skills_count;
            $requiredSkills = (int) $item->required_skills_count;

            $complexityFactor = 1
                + max(0, $avgRequiredLevel - 1) * 0.10
                + ($criticalSkills * 0.03)
                + min(0.20, $requiredSkills * 0.01);

            $estimatedReplacementCost = round($baseSalary * TalentRoiService::AVG_REPLACEMENT_COST_MULTIPLIER * $complexityFactor, 2);

            return [
                'people_id' => (int) $item->people_id,
                'role_id' => $item->role_id ? (int) $item->role_id : null,
                'role_name' => $item->role_name,
                'base_salary' => $baseSalary,
                'required_skills_count' => $requiredSkills,
                'critical_skills_count' => $criticalSkills,
                'avg_required_level' => round($avgRequiredLevel, 2),
                'complexity_factor' => round($complexityFactor, 3),
                'estimated_replacement_cost' => $estimatedReplacementCost,
            ];
        });

        $replacementCostSummary = [
            'total_headcount' => $replacementDetails->count(),
            'total_estimated_replacement_cost' => round($replacementDetails->sum('estimated_replacement_cost'), 2),
            'average_estimated_replacement_cost' => round((float) $replacementDetails->avg('estimated_replacement_cost'), 2),
            'highest_risk_roles' => $replacementDetails
                ->groupBy('role_name')
                ->map(function ($rows, $roleName) {
                    return [
                        'role_name' => $roleName,
                        'people_count' => $rows->count(),
                        'avg_replacement_cost' => round((float) $rows->avg('estimated_replacement_cost'), 2),
                    ];
                })
                ->sortByDesc('avg_replacement_cost')
                ->values()
                ->take(5),
        ];

        $departmentMaturity = DB::table('departments as d')
            ->leftJoin('people as p', function ($join) {
                $join->on('p.department_id', '=', 'd.id')
                    ->whereNull('p.deleted_at');
            })
            ->leftJoin('people_role_skills as prs', function ($join) {
                $join->on('prs.people_id', '=', 'p.id')
                    ->where('prs.is_active', '=', true);
            })
            ->where('d.organization_id', $organizationId)
            ->groupBy('d.id', 'd.name')
            ->selectRaw('d.id, d.name, COUNT(DISTINCT p.id) as headcount, ROUND(AVG(CASE WHEN prs.required_level > 0 THEN CASE WHEN (prs.current_level * 1.0 / prs.required_level) > 1 THEN 1 ELSE (prs.current_level * 1.0 / prs.required_level) END ELSE NULL END), 4) as readiness_ratio, ROUND(AVG(prs.current_level), 2) as avg_current_level, ROUND(AVG(prs.required_level), 2) as avg_required_level, SUM(CASE WHEN prs.current_level < prs.required_level THEN 1 ELSE 0 END) as gap_records')
            ->orderBy('d.name')
            ->get();

        $transversalCapabilityGaps = DB::table('people_role_skills as prs')
            ->join('people as p', function ($join) {
                $join->on('p.id', '=', 'prs.people_id')
                    ->whereNull('p.deleted_at');
            })
            ->join('skills as s', 's.id', '=', 'prs.skill_id')
            ->where('p.organization_id', $organizationId)
            ->where('prs.is_active', true)
            ->where('s.scope_type', 'transversal')
            ->groupBy('s.id', 's.name', 's.domain_tag')
            ->selectRaw('s.id as skill_id, s.name as skill_name, s.domain_tag, COUNT(DISTINCT p.id) as assessed_people, COUNT(DISTINCT CASE WHEN prs.current_level < prs.required_level THEN p.id ELSE NULL END) as people_with_gap, ROUND(AVG(CASE WHEN prs.current_level < prs.required_level THEN (prs.required_level - prs.current_level) ELSE 0 END), 2) as avg_gap_level')
            ->orderByDesc('people_with_gap')
            ->limit(10)
            ->get();

        $report = [
            'replacement_cost' => $replacementCostSummary,
            'talent_maturity_by_department' => $departmentMaturity,
            'transversal_capability_gaps' => $transversalCapabilityGaps,
        ];

        return $this->success($report, 'ISO 30414 metrics fetched successfully.');
    }

    public function recordAiConsent(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $validated = $request->validate([
            'accepted' => ['required', 'boolean'],
            'policy_version' => ['nullable', 'string', 'max:100'],
            'person_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $organizationId = (int) $user->organization_id;

        $person = null;
        if (! empty($validated['person_id'])) {
            $person = People::query()
                ->where('organization_id', $organizationId)
                ->find($validated['person_id']);

            if (! $person) {
                return $this->notFoundResponse('Person not found in organization.');
            }
        } elseif ($user->people) {
            $person = People::query()
                ->where('organization_id', $organizationId)
                ->find($user->people->id);
        }

        $eventName = $validated['accepted']
            ? 'consent.ai_processing.accepted'
            : 'consent.ai_processing.revoked';

        $payload = [
            'accepted' => (bool) $validated['accepted'],
            'policy_version' => $validated['policy_version'] ?? 'v1',
            'consent_scope' => 'ai_processing',
            'person_id' => $person?->id,
            'notes' => $validated['notes'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ];

        $event = EventStore::create([
            'id' => (string) Str::uuid(),
            'event_name' => $eventName,
            'aggregate_type' => People::class,
            'aggregate_id' => (string) ($person?->id ?? $user->id),
            'organization_id' => $organizationId,
            'actor_id' => $user->id,
            'payload' => $payload,
            'occurred_at' => now()->toIso8601String(),
        ]);

        return $this->success([
            'event_id' => $event->id,
            'event_name' => $event->event_name,
            'person_id' => $person?->id,
        ], 'AI consent recorded successfully.', 201);
    }

    public function executeGdprPurge(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $validated = $request->validate([
            'person_id' => ['required', 'integer'],
            'confirm' => ['sometimes', 'boolean'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $organizationId = (int) $user->organization_id;

        $person = People::query()
            ->where('organization_id', $organizationId)
            ->find($validated['person_id']);

        if (! $person) {
            return $this->notFoundResponse('Person not found in organization.');
        }

        $impact = [
            'people_role_skills_records' => PeopleRoleSkills::query()->where('people_id', $person->id)->count(),
            'will_soft_delete_person' => true,
            'will_anonymize_core_fields' => [
                'first_name',
                'last_name',
                'email',
                'external_id',
                'photo_url',
                'hire_date',
                'termination_date',
            ],
        ];

        if (! ($validated['confirm'] ?? false)) {
            return $this->success([
                'status' => 'dry_run',
                'person_id' => $person->id,
                'impact' => $impact,
            ], 'GDPR purge dry-run generated. Set confirm=true to execute.');
        }

        DB::transaction(function () use ($person, $organizationId, $user, $validated) {
            PeopleRoleSkills::query()
                ->where('people_id', $person->id)
                ->update([
                    'notes' => null,
                    'evidence_source' => 'gdpr_purged',
                    'updated_at' => now(),
                ]);

            $person->forceFill([
                'first_name' => 'ANONYMIZED',
                'last_name' => 'USER',
                'email' => 'deleted+'.$person->id.'@anonymized.local',
                'external_id' => null,
                'photo_url' => null,
                'hire_date' => null,
                'termination_date' => now()->toDateString(),
                'status' => 'anonymized',
                'user_id' => null,
                'role_id' => null,
                'department_id' => null,
            ])->save();

            $person->delete();

            EventStore::create([
                'id' => (string) Str::uuid(),
                'event_name' => 'gdpr.purge.executed',
                'aggregate_type' => People::class,
                'aggregate_id' => (string) $person->id,
                'organization_id' => $organizationId,
                'actor_id' => $user->id,
                'payload' => [
                    'reason' => $validated['reason'] ?? null,
                    'mode' => 'anonymize_and_soft_delete',
                    'executed_at' => now()->toIso8601String(),
                ],
                'occurred_at' => now()->toIso8601String(),
            ]);
        });

        return $this->success([
            'status' => 'executed',
            'person_id' => $person->id,
            'impact' => $impact,
        ], 'GDPR purge executed successfully.');
    }

    public function exportRoleCredential(Request $request, int $roleId): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $role = Roles::query()
            ->where('organization_id', (int) $user->organization_id)
            ->with('department:id,name')
            ->find($roleId);

        if (! $role) {
            return $this->notFoundResponse('Role not found in organization.');
        }

        $credential = $this->buildRoleCredential($role);

        return $this->success($credential, 'Role verifiable credential generated successfully.');
    }

    public function verifyRoleCredential(Request $request, int $roleId): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $role = Roles::query()
            ->where('organization_id', (int) $user->organization_id)
            ->find($roleId);

        if (! $role) {
            return $this->notFoundResponse('Role not found in organization.');
        }

        $validated = $request->validate([
            'credential' => ['nullable', 'array'],
        ]);

        $credential = $validated['credential'] ?? $this->buildRoleCredential($role);

        $verification = $this->verifyCredentialPayload($role, $credential);

        return $this->success($verification, 'Credential verification executed successfully.');
    }

    public function verifyRoleCredentialPublic(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'credential' => ['required', 'array'],
        ]);

        $credential = $validated['credential'];
        $credentialRoleId = (int) data_get($credential, 'credentialSubject.role.id', 0);
        $credentialOrganizationId = (int) data_get($credential, 'credentialSubject.organization_id', 0);

        if ($credentialRoleId <= 0 || $credentialOrganizationId <= 0) {
            return $this->success([
                'is_valid' => false,
                'role_exists' => false,
                'checks' => [
                    'proof_matches_role_signature' => false,
                    'issuer_matches_expected' => false,
                    'credential_subject_role_matches' => false,
                    'credential_subject_organization_matches' => false,
                ],
            ], 'Public credential verification executed successfully.');
        }

        $role = Roles::query()
            ->where('organization_id', $credentialOrganizationId)
            ->find($credentialRoleId);

        if (! $role) {
            return $this->success([
                'is_valid' => false,
                'role_exists' => false,
                'checks' => [
                    'proof_matches_role_signature' => false,
                    'issuer_matches_expected' => false,
                    'credential_subject_role_matches' => false,
                    'credential_subject_organization_matches' => false,
                ],
            ], 'Public credential verification executed successfully.');
        }

        $verification = $this->verifyCredentialPayload($role, $credential);
        $verification['role_exists'] = true;

        return $this->success($verification, 'Public credential verification executed successfully.');
    }

    public function didDocument(): JsonResponse
    {
        $issuerDid = $this->resolveIssuerDid();
        $verificationMethodFragment = (string) config('stratos.compliance.verification_method_fragment', 'stratos-digital-seal');
        $verificationMethodId = $issuerDid.'#'.$verificationMethodFragment;
        $serviceEndpoint = rtrim((string) config('app.url'), '/').'/api/compliance/public/credentials/verify';
        $metadataEndpoint = rtrim((string) config('app.url'), '/').'/api/compliance/public/verifier-metadata';

        $document = [
            '@context' => ['https://www.w3.org/ns/did/v1'],
            'id' => $issuerDid,
            'verificationMethod' => [
                [
                    'id' => $verificationMethodId,
                    'type' => 'StratosDigitalSealMethod2026',
                    'controller' => $issuerDid,
                ],
            ],
            'assertionMethod' => [$verificationMethodId],
            'service' => [
                [
                    'id' => $issuerDid.'#credential-verification',
                    'type' => 'CredentialVerificationService',
                    'serviceEndpoint' => $serviceEndpoint,
                ],
                [
                    'id' => $issuerDid.'#verifier-metadata',
                    'type' => 'VerifierMetadataService',
                    'serviceEndpoint' => $metadataEndpoint,
                ],
            ],
        ];

        return response()->json($document, 200, ['Content-Type' => 'application/did+json']);
    }

    public function verifierMetadata(): JsonResponse
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $issuerDid = $this->resolveIssuerDid();

        return $this->success([
            'version' => (string) config('stratos.compliance.verifier_version', '2026.03'),
            'policy_version' => (string) config('stratos.compliance.policy_version', 'v1'),
            'issuer_did' => $issuerDid,
            'verification' => [
                'public_verify_endpoint' => $baseUrl.'/api/compliance/public/credentials/verify',
                'requires_authentication' => false,
                'proof_type' => 'StratosDigitalSealProof',
                'verification_method_type' => 'StratosDigitalSealMethod2026',
                'algorithm' => 'HMAC-SHA256',
            ],
            'supported_credentials' => [
                'RoleComplianceCredential',
            ],
            'checks' => [
                'proof_matches_role_signature',
                'issuer_matches_expected',
                'credential_subject_role_matches',
                'credential_subject_organization_matches',
            ],
            'did_document' => $baseUrl.'/.well-known/did.json',
        ], 'Verifier metadata fetched successfully.');
    }

    private function verifyCredentialPayload(Roles $role, array $credential): array
    {
        $proofJws = data_get($credential, 'proof.jws');
        $credentialIssuer = data_get($credential, 'issuer.id');
        $credentialRoleId = (int) data_get($credential, 'credentialSubject.role.id', 0);
        $credentialOrganizationId = (int) data_get($credential, 'credentialSubject.organization_id', 0);

        $modelSignatureValid = app(StratosSignatureService::class)->verify($role);
        $proofMatchesRoleSignature =
            ! empty($proofJws) &&
            ! empty($role->digital_signature) &&
            hash_equals((string) $role->digital_signature, (string) $proofJws);

        $expectedIssuerDid = $this->resolveIssuerDid();
        $issuerMatches =
            is_string($credentialIssuer) &&
            hash_equals($expectedIssuerDid, $credentialIssuer);

        $roleIdMatches = $credentialRoleId === (int) $role->id;
        $organizationIdMatches = $credentialOrganizationId === (int) $role->organization_id;

        $verification = [
            'is_valid' => $proofMatchesRoleSignature && $issuerMatches && $roleIdMatches && $organizationIdMatches,
            'checks' => [
                'model_signature_valid' => $modelSignatureValid,
                'proof_matches_role_signature' => $proofMatchesRoleSignature,
                'issuer_matches_expected' => $issuerMatches,
                'credential_subject_role_matches' => $roleIdMatches,
                'credential_subject_organization_matches' => $organizationIdMatches,
            ],
            'expected' => [
                'issuer_did' => $expectedIssuerDid,
                'role_id' => (int) $role->id,
                'organization_id' => (int) $role->organization_id,
            ],
        ];

        return $verification;
    }

    private function buildRoleCredential(Roles $role): array
    {
        $issuedAt = now()->toIso8601String();
        $issuerDid = $this->resolveIssuerDid();
        $verificationMethodFragment = (string) config('stratos.compliance.verification_method_fragment', 'stratos-digital-seal');

        $credential = [
            '@context' => [
                'https://www.w3.org/ns/credentials/v2',
                'https://www.w3.org/ns/credentials/examples/v2',
            ],
            'id' => 'urn:uuid:'.Str::uuid(),
            'type' => ['VerifiableCredential', 'RoleComplianceCredential'],
            'issuer' => [
                'id' => $issuerDid,
                'name' => (string) config('app.name', 'Stratos'),
            ],
            'issuanceDate' => $issuedAt,
            'validFrom' => $issuedAt,
            'credentialSubject' => [
                'id' => 'urn:stratos:role:'.$role->id,
                'organization_id' => (int) $role->organization_id,
                'role' => [
                    'id' => (int) $role->id,
                    'name' => (string) $role->name,
                    'level' => $role->level,
                    'status' => $role->status,
                    'department' => $role->department?->name,
                ],
                'compliance' => [
                    'digital_signature_present' => ! empty($role->digital_signature),
                    'signature_version' => $role->signature_version,
                    'signed_at' => $role->signed_at ? Carbon::parse($role->signed_at)->toIso8601String() : null,
                ],
            ],
            'proof' => [
                'type' => 'StratosDigitalSealProof',
                'created' => $issuedAt,
                'proofPurpose' => 'assertionMethod',
                'verificationMethod' => $issuerDid.'#'.$verificationMethodFragment,
                'jws' => $role->digital_signature,
            ],
        ];

        return $credential;
    }

    private function resolveIssuerDid(): string
    {
        $configured = config('stratos.compliance.issuer_did');
        if (is_string($configured) && trim($configured) !== '') {
            return trim($configured);
        }

        $appUrlHost = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'stratos.local';

        return 'did:web:'.$appUrlHost;
    }

    public function internalAuditWizard(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $validated = $request->validate([
            'signature_valid_days' => ['sometimes', 'integer', 'min:30', 'max:1095'],
        ]);

        $signatureValidDays = (int) ($validated['signature_valid_days'] ?? 365);
        $organizationId = (int) $user->organization_id;

        $criticalRoles = DB::table('roles as r')
            ->leftJoin('departments as d', 'd.id', '=', 'r.department_id')
            ->leftJoin('role_skills as rs', function ($join) {
                $join->on('rs.role_id', '=', 'r.id')
                    ->where('rs.is_critical', true);
            })
            ->where('r.organization_id', $organizationId)
            ->groupBy('r.id', 'r.name', 'r.status', 'r.signed_at', 'r.signature_version', 'r.digital_signature', 'd.name')
            ->havingRaw('COUNT(rs.id) > 0')
            ->orderBy('r.name')
            ->selectRaw('r.id, r.name, r.status, r.signed_at, r.signature_version, r.digital_signature, d.name as department_name, COUNT(rs.id) as critical_skills_count')
            ->get();

        $roleChecks = $criticalRoles->map(function ($role) use ($signatureValidDays) {
            $hasSignature = ! empty($role->digital_signature) && ! empty($role->signed_at);
            $signatureAgeDays = null;

            if ($hasSignature) {
                $signatureAgeDays = Carbon::parse($role->signed_at)->diffInDays(now());
            }

            $signatureStatus = match (true) {
                ! $hasSignature => 'missing',
                $signatureAgeDays !== null && $signatureAgeDays > $signatureValidDays => 'expired',
                default => 'current',
            };

            return [
                'role_id' => (int) $role->id,
                'role_name' => (string) $role->name,
                'department_name' => $role->department_name,
                'status' => (string) $role->status,
                'critical_skills_count' => (int) $role->critical_skills_count,
                'signature_status' => $signatureStatus,
                'signature_age_days' => $signatureAgeDays,
                'signed_at' => $role->signed_at ? Carbon::parse($role->signed_at)->toIso8601String() : null,
                'signature_version' => $role->signature_version,
                'is_compliant' => $signatureStatus === 'current',
            ];
        });

        $totalCriticalRoles = $roleChecks->count();
        $compliantRoles = $roleChecks->where('is_compliant', true)->count();
        $nonCompliantRoles = $totalCriticalRoles - $compliantRoles;

        $report = [
            'signature_valid_days' => $signatureValidDays,
            'summary' => [
                'total_critical_roles' => $totalCriticalRoles,
                'compliant_roles' => $compliantRoles,
                'non_compliant_roles' => $nonCompliantRoles,
                'compliance_rate' => $totalCriticalRoles > 0
                    ? round(($compliantRoles / $totalCriticalRoles) * 100, 2)
                    : 100.0,
            ],
            'roles' => $roleChecks->values(),
            'recommendations' => [
                'missing_signature' => $roleChecks->where('signature_status', 'missing')->values(),
                'expired_signature' => $roleChecks->where('signature_status', 'expired')->values(),
            ],
        ];

        return $this->success($report, 'Internal audit wizard report generated successfully.');
    }
}
