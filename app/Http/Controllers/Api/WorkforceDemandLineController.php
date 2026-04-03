<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreWorkforceDemandLineRequest;
use App\Http\Requests\UpdateWorkforceDemandLineRequest;
use App\Models\Scenario;
use App\Models\WorkforceDemandLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class WorkforceDemandLineController extends Controller
{
    private const ORGANIZATION_RESOLUTION_ERROR = 'No se pudo resolver organization_id';

    private const SCENARIO_NOT_FOUND = 'Scenario not found';

    private const DEMAND_LINE_NOT_FOUND = 'Demand line not found';

    private const SCENARIO_STATUS_LOCKED_MESSAGE = 'El escenario no permite modificaciones en su estado actual';

    /** GET /api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines */
    public function index(int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::ORGANIZATION_RESOLUTION_ERROR, 422);
        }

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        $lines = WorkforceDemandLine::query()
            ->where('scenario_id', $scenario->id)
            ->where('organization_id', $organizationId)
            ->orderBy('period')
            ->orderBy('unit')
            ->get()
            ->map(fn ($line) => array_merge($line->toArray(), [
                'required_hh' => $line->required_hh,
                'effective_hh' => $line->effective_hh,
            ]));

        return $this->successResponse([
            'scenario_id' => $scenario->id,
            'lines' => $lines,
            'total' => $lines->count(),
        ]);
    }

    /** POST /api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines */
    public function store(StoreWorkforceDemandLineRequest $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::ORGANIZATION_RESOLUTION_ERROR, 422);
        }

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        if (! $scenario->canMutateWorkforceExecutionData()) {
            return $this->errorResponse(self::SCENARIO_STATUS_LOCKED_MESSAGE, 409, [
                'scenario_status' => $scenario->status,
            ]);
        }

        $created = DB::transaction(function () use ($request, $scenario, $organizationId) {
            $rows = [];
            foreach ($request->validated()['lines'] as $line) {
                $rows[] = WorkforceDemandLine::create([
                    'scenario_id' => $scenario->id,
                    'organization_id' => $organizationId,
                    'unit' => $line['unit'],
                    'role_name' => $line['role_name'],
                    'period' => $line['period'],
                    'volume_expected' => (int) $line['volume_expected'],
                    'time_standard_minutes' => (int) $line['time_standard_minutes'],
                    'productivity_factor' => (float) ($line['productivity_factor'] ?? 1.0),
                    'coverage_target_pct' => (float) ($line['coverage_target_pct'] ?? 95.0),
                    'attrition_pct' => (float) ($line['attrition_pct'] ?? 0.0),
                    'ramp_factor' => (float) ($line['ramp_factor'] ?? 1.0),
                    'notes' => $line['notes'] ?? null,
                ]);
            }

            return $rows;
        });

        $payload = array_map(fn ($line) => array_merge($line->toArray(), [
            'required_hh' => $line->required_hh,
            'effective_hh' => $line->effective_hh,
        ]), $created);

        return $this->successResponse([
            'scenario_id' => $scenario->id,
            'created' => count($payload),
            'lines' => $payload,
        ], 'Líneas de demanda creadas correctamente', 201);
    }

    /** PATCH /api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId} */
    public function update(UpdateWorkforceDemandLineRequest $request, int $id, int $lineId): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::ORGANIZATION_RESOLUTION_ERROR, 422);
        }

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        if (! $scenario->canMutateWorkforceExecutionData()) {
            return $this->errorResponse(self::SCENARIO_STATUS_LOCKED_MESSAGE, 409, [
                'scenario_status' => $scenario->status,
            ]);
        }

        $line = WorkforceDemandLine::query()
            ->where('organization_id', $organizationId)
            ->where('scenario_id', $scenario->id)
            ->find($lineId);

        if (! $line) {
            return $this->notFoundResponse(self::DEMAND_LINE_NOT_FOUND);
        }

        $line->fill($request->validated());
        $line->save();

        return $this->successResponse([
            'scenario_id' => $scenario->id,
            'line' => array_merge($line->toArray(), [
                'required_hh' => $line->required_hh,
                'effective_hh' => $line->effective_hh,
            ]),
        ], 'Línea de demanda actualizada correctamente');
    }

    /** DELETE /api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId} */
    public function destroy(int $id, int $lineId): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $organizationId = (int) (($user->current_organization_id ?? $user->organization_id) ?? 0);
        if ($organizationId <= 0) {
            return $this->errorResponse(self::ORGANIZATION_RESOLUTION_ERROR, 422);
        }

        $scenario = Scenario::query()
            ->where('organization_id', $organizationId)
            ->find($id);

        if (! $scenario) {
            return $this->notFoundResponse(self::SCENARIO_NOT_FOUND);
        }

        if (! $scenario->canMutateWorkforceExecutionData()) {
            return $this->errorResponse(self::SCENARIO_STATUS_LOCKED_MESSAGE, 409, [
                'scenario_status' => $scenario->status,
            ]);
        }

        $line = WorkforceDemandLine::query()
            ->where('organization_id', $organizationId)
            ->where('scenario_id', $scenario->id)
            ->find($lineId);

        if (! $line) {
            return $this->notFoundResponse(self::DEMAND_LINE_NOT_FOUND);
        }

        $line->delete();

        return $this->successResponse([
            'scenario_id' => $scenario->id,
            'deleted_line_id' => $lineId,
        ], 'Línea de demanda eliminada correctamente');
    }
}
