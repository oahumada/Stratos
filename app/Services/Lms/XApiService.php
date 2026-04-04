<?php

namespace App\Services\Lms;

use App\Models\LmsXapiStatement;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class XApiService
{
    public function storeStatement(array $statement, int $organizationId): LmsXapiStatement
    {
        return LmsXapiStatement::create([
            'organization_id' => $organizationId,
            'actor_id' => $statement['actor']['id'] ?? null,
            'actor_name' => $statement['actor']['name'] ?? 'Unknown',
            'actor_email' => $statement['actor']['mbox'] ?? null,
            'verb_id' => $statement['verb']['id'],
            'verb_display' => $statement['verb']['display'] ?? $statement['verb']['id'],
            'object_type' => $statement['object']['type'] ?? 'Activity',
            'object_id' => $statement['object']['id'],
            'object_name' => $statement['object']['name'] ?? null,
            'result_score_raw' => $statement['result']['score']['raw'] ?? null,
            'result_score_min' => $statement['result']['score']['min'] ?? null,
            'result_score_max' => $statement['result']['score']['max'] ?? null,
            'result_score_scaled' => $statement['result']['score']['scaled'] ?? null,
            'result_success' => $statement['result']['success'] ?? null,
            'result_completion' => $statement['result']['completion'] ?? null,
            'result_duration' => $statement['result']['duration'] ?? null,
            'context_data' => $statement['context'] ?? null,
            'statement_timestamp' => $statement['timestamp'] ?? now(),
            'stored_at' => now(),
        ]);
    }

    public function storeStatements(array $statements, int $organizationId): array
    {
        $stored = [];
        foreach ($statements as $statement) {
            $stored[] = $this->storeStatement($statement, $organizationId);
        }

        return $stored;
    }

    public function queryStatements(int $organizationId, array $filters = []): LengthAwarePaginator
    {
        $query = LmsXapiStatement::query()
            ->forOrganization($organizationId)
            ->with('actor');

        if (! empty($filters['actor'])) {
            $query->byActor((int) $filters['actor']);
        }

        if (! empty($filters['verb'])) {
            $query->byVerb($filters['verb']);
        }

        if (! empty($filters['activity'])) {
            $query->byObject($filters['activity']);
        }

        if (! empty($filters['since'])) {
            $query->where('statement_timestamp', '>=', Carbon::parse($filters['since']));
        }

        if (! empty($filters['until'])) {
            $query->where('statement_timestamp', '<=', Carbon::parse($filters['until']));
        }

        $perPage = (int) ($filters['per_page'] ?? 20);

        return $query->latest('statement_timestamp')->paginate($perPage);
    }

    public function emitLmsEvent(string $verb, int $userId, string $objectId, string $objectName, int $organizationId, array $result = []): LmsXapiStatement
    {
        $user = User::findOrFail($userId);

        $verbMap = [
            'attempted' => 'http://adlnet.gov/expapi/verbs/attempted',
            'completed' => 'http://adlnet.gov/expapi/verbs/completed',
            'passed' => 'http://adlnet.gov/expapi/verbs/passed',
            'failed' => 'http://adlnet.gov/expapi/verbs/failed',
            'experienced' => 'http://adlnet.gov/expapi/verbs/experienced',
        ];

        $verbIri = $verbMap[$verb] ?? $verb;

        $statement = [
            'actor' => [
                'id' => $userId,
                'name' => $user->name,
                'mbox' => $user->email,
            ],
            'verb' => [
                'id' => $verbIri,
                'display' => $verb,
            ],
            'object' => [
                'type' => 'Activity',
                'id' => $objectId,
                'name' => $objectName,
            ],
            'result' => $result,
            'timestamp' => now(),
        ];

        return $this->storeStatement($statement, $organizationId);
    }

    public function getActivityStats(string $objectId, int $organizationId): array
    {
        $query = LmsXapiStatement::query()
            ->forOrganization($organizationId)
            ->byObject($objectId);

        $totalStatements = $query->count();
        $uniqueActors = (clone $query)->distinct('actor_id')->count('actor_id');
        $completions = (clone $query)->where('result_completion', true)->count();
        $avgScore = (clone $query)->whereNotNull('result_score_raw')->avg('result_score_raw');

        $verbBreakdown = (clone $query)
            ->selectRaw('verb_display, count(*) as count')
            ->groupBy('verb_display')
            ->pluck('count', 'verb_display')
            ->toArray();

        return [
            'object_id' => $objectId,
            'total_statements' => $totalStatements,
            'unique_actors' => $uniqueActors,
            'completions' => $completions,
            'average_score' => $avgScore ? round((float) $avgScore, 2) : null,
            'verb_breakdown' => $verbBreakdown,
        ];
    }
}
