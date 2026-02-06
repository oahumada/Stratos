<?php

namespace App\Services;

use App\Models\ChangeSet;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

    public function apply(ChangeSet $changeSet, User $actor): ChangeSet
    {
        return DB::transaction(function () use ($changeSet, $actor) {
            if ($changeSet->status === 'applied') {
                return $changeSet;
            }

            $diff = $changeSet->diff ?? [];

            // Process operations in diff (simple, extensible handler)
            $ops = $diff['ops'] ?? [];
            foreach ($ops as $op) {
                $type = $op['type'] ?? null;
                switch ($type) {
                    case 'create_competency_version':
                        if (class_exists(\App\Models\CompetencyVersion::class)) {
                            \App\Models\CompetencyVersion::create([
                                'organization_id' => $changeSet->organization_id,
                                'competency_id' => $op['competency_id'] ?? null,
                                'version_group_id' => $op['version_group_id'] ?? null,
                                'name' => $op['name'] ?? null,
                                'description' => $op['description'] ?? null,
                                'effective_from' => $op['effective_from'] ?? null,
                                'evolution_state' => $op['evolution_state'] ?? null,
                                'metadata' => $op['metadata'] ?? null,
                                'created_by' => $actor->id ?? null,
                            ]);
                        }
                        break;

                    case 'create_role_version':
                        if (class_exists(\App\Models\RoleVersion::class)) {
                            \App\Models\RoleVersion::create([
                                'organization_id' => $changeSet->organization_id,
                                'role_id' => $op['role_id'] ?? null,
                                'version_group_id' => $op['version_group_id'] ?? null,
                                'name' => $op['name'] ?? null,
                                'description' => $op['description'] ?? null,
                                'effective_from' => $op['effective_from'] ?? null,
                                'evolution_state' => $op['evolution_state'] ?? null,
                                'metadata' => $op['metadata'] ?? null,
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
                        // convenience: update/insert scenario_role_skills
                        $payload = $op['payload'] ?? [];
                        if (!empty($payload) && \Illuminate\Support\Facades\Schema::hasTable('scenario_role_skills')) {
                            $where = [
                                'scenario_id' => $payload['scenario_id'] ?? $changeSet->scenario_id,
                                'role_id' => $payload['role_id'] ?? null,
                                'skill_id' => $payload['skill_id'] ?? null,
                            ];
                            if ($where['role_id'] && $where['skill_id']) {
                                $q = DB::table('scenario_role_skills')->where($where);
                                $values = array_diff_key($payload, array_flip(array_keys($where)));
                                if ($q->exists()) {
                                    $q->update($values + ['updated_at' => now()]);
                                } else {
                                    DB::table('scenario_role_skills')->insert(array_merge($where, $values, ['created_at' => now(), 'updated_at' => now()]));
                                }
                            }
                        }
                        break;

                    case 'create_role_sunset_mapping':
                        // create a role sunset mapping record
                        $payload = $op['payload'] ?? [];
                        if (!empty($payload) && \Illuminate\Support\Facades\Schema::hasTable('role_sunset_mappings')) {
                            if (class_exists(\App\Models\RoleSunsetMapping::class)) {
                                \App\Models\RoleSunsetMapping::create([
                                    'organization_id' => $changeSet->organization_id,
                                    'scenario_id' => $payload['scenario_id'] ?? $changeSet->scenario_id ?? null,
                                    'role_id' => $payload['role_id'] ?? null,
                                    'mapped_role_id' => $payload['mapped_role_id'] ?? null,
                                    'sunset_reason' => $payload['sunset_reason'] ?? null,
                                    'metadata' => $payload['metadata'] ?? null,
                                    'created_by' => $actor->id ?? null,
                                ]);
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
