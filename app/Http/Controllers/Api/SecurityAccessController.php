<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SecurityAccessLog;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * API endpoints for security access audit logs.
 *
 * All endpoints are restricted to admin role (see routes/api.php).
 * Multi-tenant: results are always scoped to the authenticated user's organization.
 */
class SecurityAccessController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/security/access-logs
     *
     * Returns a paginated list of security access events for the organization.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $perPage = (int) $request->integer('per_page', 25);
        $perPage = max(1, min($perPage, 100));

        $query = SecurityAccessLog::query()
            ->where('organization_id', $user->organization_id)
            ->when($request->filled('event'), fn ($q) => $q->where('event', $request->string('event')->toString()))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('email'), fn ($q) => $q->where('email', 'like', '%'.$request->string('email')->toString().'%'))
            ->when($request->filled('from'), fn ($q) => $q->where('occurred_at', '>=', Carbon::parse($request->string('from')->toString())->startOfDay()))
            ->when($request->filled('to'), fn ($q) => $q->where('occurred_at', '<=', Carbon::parse($request->string('to')->toString())->endOfDay()))
            ->with('user:id,name,email')
            ->orderByDesc('occurred_at');

        return $this->success($query->paginate($perPage), 'Security access logs fetched.');
    }

    /**
     * GET /api/security/access-logs/summary
     *
     * Returns security event summary metrics for the organization.
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $orgId = $user->organization_id;

        $base = SecurityAccessLog::query()->where('organization_id', $orgId);

        $metrics = [
            'total_events' => (clone $base)->count(),
            'events_last_24h' => (clone $base)->where('occurred_at', '>=', now()->subDay())->count(),
            'successful_logins' => (clone $base)->where('event', 'login')->count(),
            'failed_logins' => (clone $base)->where('event', 'login_failed')->count(),
            'logouts' => (clone $base)->where('event', 'logout')->count(),
            'failed_logins_24h' => (clone $base)->where('event', 'login_failed')->where('occurred_at', '>=', now()->subDay())->count(),
            'mfa_used_percentage' => $this->mfaPercentage(clone $base),
            'top_ips' => (clone $base)
                ->whereNotNull('ip_address')
                ->selectRaw('ip_address, COUNT(*) as total')
                ->groupBy('ip_address')
                ->orderByDesc('total')
                ->limit(10)
                ->pluck('total', 'ip_address'),
            'events_by_type' => (clone $base)
                ->selectRaw('event, COUNT(*) as total')
                ->groupBy('event')
                ->orderByDesc('total')
                ->pluck('total', 'event'),
        ];

        return $this->success($metrics, 'Security summary fetched.');
    }

    /**
     * Calculate the percentage of logins that used MFA.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<SecurityAccessLog>  $query
     */
    private function mfaPercentage(\Illuminate\Database\Eloquent\Builder $query): float
    {
        $total = (clone $query)->where('event', 'login')->count();

        if ($total === 0) {
            return 0.0;
        }

        $withMfa = (clone $query)->where('event', 'login')->where('mfa_used', true)->count();

        return round(($withMfa / $total) * 100, 1);
    }
}
