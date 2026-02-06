<?php

namespace App\Services;

use App\Models\ChangeSet;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ChangeSetService
{
    public function build(array $payload): ChangeSet
    {
        // Minimal builder: persist payload as a ChangeSet record
        return ChangeSet::create($payload);
    }

    public function preview(ChangeSet $changeSet): array
    {
        // For now the preview is the stored diff structure
        return $changeSet->diff ?? [];
    }

    public function apply(ChangeSet $changeSet, User $actor, array $options = []): ChangeSet
    {
        return DB::transaction(function () use ($changeSet, $actor, $options) {
            if ($changeSet->status === 'applied') {
                return $changeSet;
            }

            $diff = $changeSet->diff ?? [];

            // Process operations in diff (simple, extensible handler)
            $ops = $diff['ops'] ?? [];
            $ignored = $options['ignored_indexes'] ?? [];
            foreach ($ops as $idx => $op) {
                if (is_array($ignored) && in_array($idx, $ignored, true)) {
                    // skip this op as requested by client
                    continue;
                }
                $type = $op['type'] ?? null;
                switch ($type) {
                    case 'create_competency_version':
                        if (class_exists(\App\Models\CompetencyVersion::class)) {
                            $compId = $op['competency_id'] ?? null;
                            // idempotency: skip if a version for this competency with same version_group exists
                            $vg = $op['version_group_id'] ?? null;
                            if ($compId) {
                                $existsQuery = \App\Models\CompetencyVersion::where('competency_id', $compId);
                                if ($vg) {
                                    $existsQuery->where('version_group_id', $vg);
                                }
                                if ($existsQuery->exists()) {
                                    break;
                                }
                            }

                            $vg = $vg ?: (string) Str::uuid();
                            $meta = $op['metadata'] ?? ['source' => 'changeset'];

                            $name = $op['name'] ?? null;
                            $description = $op['description'] ?? null;
                            if ($compId && empty($name)) {
                                if (class_exists(\App\Models\Competency::class)) {
                                    $c = \App\Models\Competency::find($compId);
                                    if ($c) {
                                        $name = $name ?: $c->name;
                                        $description = $description ?: $c->description ?? null;
                                    }
                                }
                            }

                            $evolution = $op['evolution_state'] ?? 'new_embryo';
                            \App\Models\CompetencyVersion::create([
                                'organization_id' => $changeSet->organization_id,
                                'competency_id' => $compId,
                                'version_group_id' => $vg,
                                'name' => $name,
                                'description' => $description,
                                'effective_from' => $op['effective_from'] ?? null,
                                'evolution_state' => $evolution,
                                'metadata' => $meta,
                                'created_by' => $actor->id ?? null,
                            ]);
                        }
                        break;

                    case 'create_role_version':
                        if (class_exists(\App\Models\RoleVersion::class)) {
                            $vg = $op['version_group_id'] ?? null;
                            if (empty($vg)) {
                                $vg = (string) Str::uuid();
                            }
                            $meta = $op['metadata'] ?? [];
                            if (empty($meta)) {
                                $meta = ['source' => 'changeset'];
                            }
                            \App\Models\RoleVersion::create([
                                'organization_id' => $changeSet->organization_id,
                                'role_id' => $op['role_id'] ?? null,
                                'version_group_id' => $vg,
                                'name' => $op['name'] ?? null,
                                'description' => $op['description'] ?? null,
                                'effective_from' => $op['effective_from'] ?? null,
                                'evolution_state' => $op['evolution_state'] ?? null,
                                'metadata' => $meta,
                                'created_by' => $actor->id ?? null,
                            ]);
                        }
                        break;

                    case 'update_pivot':
                        // generic pivot update: table + where + values
                        $table = $op['table'] ?? null;
                        $where = $op['where'] ?? [];
                        $values = $op['values'] ?? [];
                        if ($table && is_array($where) && is_array($values)) {
                            // Use schema checks to avoid sqlite absent columns failure
                            if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                                // attempt update, or insert if not exists
                                $q = DB::table($table)->where($where);
                                if ($q->exists()) {
                                    $q->update($values);
                                } else {
                                    $insert = array_merge($where, $values);
                                    DB::table($table)->insert($insert);
                                }
                            }
                        }
                        break;

                    case 'update_scenario_role_skill':
                        // convenience: update/insert scenario_role_skills with traceability
                        $payload = $op['payload'] ?? [];
                        if (!empty($payload) && \Illuminate\Support\Facades\Schema::hasTable('scenario_role_skills')) {
                            $where = [
                                'scenario_id' => $payload['scenario_id'] ?? $changeSet->scenario_id,
                                'role_id' => $payload['role_id'] ?? null,
                                'skill_id' => $payload['skill_id'] ?? null,
                            ];
                            if ($where['role_id'] && $where['skill_id']) {
                                $q = DB::table('scenario_role_skills')->where($where);
                                // Allow competency_version_id and metadata and created_by to flow
                                $values = array_diff_key($payload, array_flip(array_keys($where)));
                                // Normalize metadata if provided as array
                                if (isset($values['metadata']) && is_array($values['metadata'])) {
                                    $values['metadata'] = json_encode($values['metadata']);
                                }
                                if ($q->exists()) {
                                    $updateValues = $values + ['updated_at' => now(), 'updated_by' => $actor->id ?? null];
                                    $q->update($updateValues);
                                } else {
                                    $insert = array_merge($where, $values, ['created_at' => now(), 'updated_at' => now(), 'created_by' => $actor->id ?? null]);
                                    DB::table('scenario_role_skills')->insert($insert);
                                }
                            }
                        }
                        break;

                    case 'create_role_sunset_mapping':
                        // create a role sunset mapping record
                        $payload = $op['payload'] ?? [];
                        if (!empty($payload) && \Illuminate\Support\Facades\Schema::hasTable('role_sunset_mappings')) {
                            // Ensure a RoleVersion exists for the role (auto-backfill)
                            $roleId = $payload['role_id'] ?? null;
                            if ($roleId && class_exists(\App\Models\RoleVersion::class)) {
                                $exists = \App\Models\RoleVersion::where('role_id', $roleId)->exists();
                                if (!$exists && class_exists(\App\Models\Roles::class)) {
                                    $role = \App\Models\Roles::find($roleId);
                                    if ($role) {
                                        \App\Models\RoleVersion::create([
                                            'organization_id' => $changeSet->organization_id,
                                            'role_id' => $role->id,
                                            'version_group_id' => (string) Str::uuid(),
                                            'name' => $role->name,
                                            'description' => $role->description ?? null,
                                            'effective_from' => now()->toDateString(),
                                            'evolution_state' => 'new_embryo',
                                            'metadata' => ['source' => 'backfill', 'scenario_id' => $payload['scenario_id'] ?? $changeSet->scenario_id ?? null],
                                            'created_by' => $actor->id ?? null,
                                        ]);
                                    }
                                }
                            }

                            if (class_exists(\App\Models\RoleSunsetMapping::class)) {
                                $where = [
                                    'role_id' => $payload['role_id'] ?? null,
                                    'scenario_id' => $payload['scenario_id'] ?? $changeSet->scenario_id ?? null,
                                ];
                                $existing = \App\Models\RoleSunsetMapping::where($where);
                                if (!empty($payload['sunset_reason'])) {
                                    $existing->where('sunset_reason', $payload['sunset_reason']);
                                }
                                if (!$existing->exists()) {
                                    \App\Models\RoleSunsetMapping::create([
                                        'organization_id' => $changeSet->organization_id,
                                        'scenario_id' => $where['scenario_id'],
                                        'role_id' => $where['role_id'],
                                        'mapped_role_id' => $payload['mapped_role_id'] ?? null,
                                        'sunset_reason' => $payload['sunset_reason'] ?? null,
                                        'metadata' => $payload['metadata'] ?? null,
                                        'created_by' => $actor->id ?? null,
                                    ]);
                                }
                            }
                        }
                        break;

                    default:
                        // unknown op -> skip
                        break;
                }
            }

            $changeSet->status = 'applied';
            $changeSet->applied_at = Carbon::now();
            $changeSet->approved_by = $actor->id;
            $changeSet->save();

            return $changeSet;
        });
    }
}
