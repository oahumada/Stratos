<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departments;
use App\Models\People;
use App\Models\Roles;
use App\Models\ChangeSet;
use App\Models\OrganizationSnapshot;
use App\Models\PersonMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BulkPeopleImportController extends Controller
{
    /**
     * Analiza el archivo y devuelve una propuesta de mapeo y limpieza.
     * En esta fase inicial, simulamos el parsing de las filas enviadas por el front.
     */
    public function analyze(Request $request)
    {
        $rows = $request->input('rows', []);
        $orgId = auth()->user()->organization_id;

        $analysis = [
            'people_count' => count($rows),
            'detected_departments' => [],
            'detected_roles' => [],
            'movements' => [
                'hires' => [],
                'transfers' => [],
                'exits' => [],
            ],
        ];

        $existingDepts = Departments::where('organization_id', $orgId)->get();
        $existingRoles = Roles::where('organization_id', $orgId)->get();
        $existingPeople = People::with(['department', 'role'])->where('organization_id', $orgId)->get();

        // 1. Analyze Structural Nodes (Depts/Roles)
        $analysis['detected_departments'] = $this->detectDepartments($rows, $existingDepts);
        $analysis['detected_roles'] = $this->detectRoles($rows, $existingRoles);

        // 2. Analyze Individual Movements (Talent Flow)
        $analysis['movements'] = $this->detectMovements($rows, $existingPeople);

        return response()->json([
            'success' => true,
            'analysis' => $analysis
        ]);
    }

    private function detectDepartments(array $rows, $existingDepts): array
    {
        $deptNames = collect($rows)->pluck('department')->unique()->filter();
        $detected = [];

        foreach ($deptNames as $name) {
            $match = $existingDepts->first(fn($d) => 
                mb_strtolower($d->name) === mb_strtolower($name) || 
                (is_array($d->aliases) && in_array(mb_strtolower($name), array_map('mb_strtolower', $d->aliases)))
            );

            $detected[] = [
                'raw_name' => $name,
                'status' => $match ? 'existing' : 'new',
                'matched_id' => $match ? $match->id : null,
                'suggested_name' => $match ? $match->name : $name,
            ];
        }
        return $detected;
    }

    private function detectRoles(array $rows, $existingRoles): array
    {
        $roleNames = collect($rows)->pluck('role')->unique()->filter();
        $detected = [];

        foreach ($roleNames as $name) {
            $match = $existingRoles->first(fn($r) => mb_strtolower($r->name) === mb_strtolower($name));

            $detected[] = [
                'raw_name' => $name,
                'status' => $match ? 'existing' : 'new',
                'matched_id' => $match ? $match->id : null,
                'suggested_name' => $match ? $match->name : $name,
            ];
        }
        return $detected;
    }

    private function detectMovements(array $rows, $existingPeople): array
    {
        $movements = ['hires' => [], 'transfers' => [], 'exits' => []];
        $uploadedEmails = [];

        foreach ($rows as $row) {
            $email = mb_strtolower($row['email'] ?? ($row['id_nacional'] . '@stratos.ai'));
            $uploadedEmails[] = $email;
            $person = $existingPeople->first(fn($p) => mb_strtolower($p->email) === $email);

            if (!$person) {
                $movements['hires'][] = [
                    'name' => ($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''),
                    'email' => $email,
                    'department' => $row['department'] ?? '?',
                    'role' => $row['role'] ?? '?'
                ];
            } else {
                $this->checkIfTransfer($row, $person, $movements);
            }
        }

        // Detect Exits
        $existingEmails = $existingPeople->pluck('email')->map(fn($e) => mb_strtolower($e))->toArray();
        $diff = array_diff($existingEmails, $uploadedEmails);

        foreach ($diff as $email) {
            $person = $existingPeople->first(fn($p) => mb_strtolower($p->email) === $email);
            $movements['exits'][] = [
                'name' => $person->full_name,
                'email' => $email,
                'department' => $person->department->name ?? '?',
                'role' => $person->role->name ?? '?'
            ];
        }

        return $movements;
    }

    private function checkIfTransfer(array $row, People $person, array &$movements): void
    {
        $newDept = mb_strtolower($row['department'] ?? '');
        $newRole = mb_strtolower($row['role'] ?? '');
        $currDept = mb_strtolower($person->department->name ?? '');
        $currRole = mb_strtolower($person->role->name ?? '');

        if ($newDept !== $currDept || $newRole !== $currRole) {
            $movements['transfers'][] = [
                'name' => $person->full_name,
                'email' => $person->email,
                'from_dept' => $person->department->name ?? 'N/A',
                'to_dept' => $row['department'],
                'from_role' => $person->role->name ?? 'N/A',
                'to_role' => $row['role'],
            ];
        }
    }
    /**
     * Crea un ChangeSet con el estado de la importación para su revisión y firma.
     */
    public function stage(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();

        $changeSet = ChangeSet::create([
            'organization_id' => $user->organization_id,
            'title' => 'Importación Masiva de Talento - ' . now()->format('d/m/Y'),
            'description' => 'Carga de nómina para validación organizacional.',
            'status' => 'draft',
            'created_by' => $user->id,
            'diff' => [
                'type' => 'bulk_people_import',
                'rows' => $data['rows'],
                'mapping' => $data['mapping']
            ],
            'metadata' => [
                'source' => 'bulk_upload',
                'ip_address' => $request->ip()
            ]
        ]);

        return response()->json([
            'success' => true,
            'change_set_id' => $changeSet->id
        ]);
    }

    /**
     * Firma digitalmente y aplica los cambios, creando el baseline.
     */
   public function approveAndCommit(Request $request, $id)
    {
        $user = auth()->user();
        $orgId = $user->organization_id;
        $changeSet = ChangeSet::findOrFail($id);
        
        $diff = $changeSet->diff;
        $mapping = $request->input('mapping', $diff['mapping'] ?? []);
        $rows = $request->input('rows', $diff['rows'] ?? []);

        if ($changeSet->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Status invalid'], 400);
        }

        DB::beginTransaction();
        try {
            // 1. Sync Structural Nodes (Depts/Roles)
            $deptMap = $this->syncDepartments($orgId, $mapping['departments'] ?? []);
            $roleMap = $this->syncRoles($orgId, $mapping['roles'] ?? []);

            // 2. Sync People & Track Movements
            $this->syncPeopleBatch($orgId, $rows, $deptMap, $roleMap, $changeSet);

            // 3. Finalize ChangeSet
            $this->finalizeChangeSet($changeSet, $user, $request->input('signature'), $mapping);

            // 4. Handle Exits
            $this->processExits($orgId, $mapping['movements']['exits'] ?? [], $changeSet);

            // 5. Baseline Snapshot
            $this->createBaselineSnapshot($orgId, count($mapping['movements']['hires'] ?? []));

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk Import Commit Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function syncDepartments($orgId, array $depts): array
    {
        $deptMap = [];
        foreach ($depts as $dMap) {
            if ($dMap['status'] === 'new') {
                $dept = Departments::create([
                    'organization_id' => $orgId,
                    'name' => $dMap['suggested_name'],
                    'status' => 'active'
                ]);
                $deptMap[$dMap['raw_name']] = $dept->id;
            } else {
                $deptMap[$dMap['raw_name']] = $dMap['matched_id'];
                // Update aliases
                $dept = Departments::find($dMap['matched_id']);
                if ($dept) {
                    $aliases = $dept->aliases ?: [];
                    if (!in_array($dMap['raw_name'], $aliases)) {
                        $aliases[] = $dMap['raw_name'];
                        $dept->aliases = $aliases;
                        $dept->save();
                    }
                }
            }
        }
        return $deptMap;
    }

    private function syncRoles($orgId, array $roles): array
    {
        $roleMap = [];
        foreach ($roles as $rMap) {
            if ($rMap['status'] === 'new') {
                $role = Roles::create([
                    'organization_id' => $orgId,
                    'name' => $rMap['suggested_name'],
                    'status' => 'active'
                ]);
                $roleMap[$rMap['raw_name']] = $role->id;
            } else {
                $roleMap[$rMap['raw_name']] = $rMap['matched_id'];
            }
        }
        return $roleMap;
    }

    private function syncPeopleBatch($orgId, array $rows, array $deptMap, array $roleMap, ChangeSet $changeSet): void
    {
        foreach ($rows as $row) {
            $email = $row['email'] ?? ($row['id_nacional'] . '@stratos.ai');
            $person = People::where('organization_id', $orgId)->where('email', $email)->first();
            
            $oldDeptId = $person ? $person->department_id : null;
            $oldRoleId = $person ? $person->role_id : null;
            $newDeptId = $deptMap[$row['department']] ?? null;
            $newRoleId = $roleMap[$row['role']] ?? null;

            $person = People::updateOrCreate(
                ['organization_id' => $orgId, 'email' => $email],
                [
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'department_id' => $newDeptId,
                    'role_id' => $newRoleId,
                    'hire_date' => $row['hire_date'] ?? ($person->hire_date ?? now()),
                ]
            );

            $this->trackMovement($person, $oldDeptId, $newDeptId, $oldRoleId, $newRoleId, $changeSet->id);
        }
    }

    private function finalizeChangeSet(ChangeSet $cs, $user, $signature, array $mapping): void
    {
        $cs->status = 'applied';
        $cs->approved_by = $user->id;
        $cs->applied_at = now();
        $meta = $cs->metadata;
        $meta['signature'] = $signature;
        $meta['approver_name'] = $user->name;
        $meta['movements_summary'] = [
            'hires' => count($mapping['movements']['hires'] ?? []),
            'transfers' => count($mapping['movements']['transfers'] ?? []),
            'exits' => count($mapping['movements']['exits'] ?? []),
        ];
        $cs->metadata = $meta;
        $cs->save();
    }

    private function processExits($orgId, array $exits, ChangeSet $cs): void
    {
        foreach ($exits as $exit) {
            $personToExit = People::where('organization_id', $orgId)->where('email', $exit['email'])->first();
            if ($personToExit) {
                PersonMovement::create([
                    'person_id' => $personToExit->id,
                    'organization_id' => $orgId,
                    'type' => 'exit',
                    'from_department_id' => $personToExit->department_id,
                    'from_role_id' => $personToExit->role_id,
                    'movement_date' => now(),
                    'change_set_id' => $cs->id
                ]);
                $personToExit->delete();
            }
        }
    }

    private function trackMovement($person, $oldDeptId, $newDeptId, $oldRoleId, $newRoleId, $csId)
    {
        if (!$oldDeptId && !$oldRoleId) {
            $type = 'hire';
        } elseif ($oldRoleId != $newRoleId) {
            $type = 'promotion';
        } elseif ($oldDeptId != $newDeptId) {
            $type = 'transfer';
        } else {
            return; // No change
        }

        PersonMovement::create([
            'person_id' => $person->id,
            'organization_id' => $person->organization_id,
            'type' => $type,
            'from_department_id' => $oldDeptId,
            'to_department_id' => $newDeptId,
            'from_role_id' => $oldRoleId,
            'to_role_id' => $newRoleId,
            'movement_date' => now(),
            'change_set_id' => $csId
        ]);
    }

    private function createBaselineSnapshot($orgId, $newHiresCount = 0)
    {
        $totalPeople = People::where('organization_id', $orgId)->count();
        $totalRoles = Roles::where('organization_id', $orgId)->count();

        OrganizationSnapshot::create([
            'organizations_id' => $orgId,
            'snapshot_date' => now(),
            'total_people' => $totalPeople,
            'metadata' => [
                'event' => 'bulk_sync',
                'description' => 'Sincronización mensual de nómina.',
                'total_roles' => $totalRoles,
                'new_hires' => $newHiresCount,
                'captured_at' => now()->toDateTimeString()
            ]
        ]);
    }
}
