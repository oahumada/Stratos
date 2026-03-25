<?php

namespace Database\Seeders;

use App\Models\Departments;
use App\Models\EventStore;
use App\Models\Organization;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Models\Skill;
use App\Models\User;
use App\Models\VerifiableCredential;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * ComplianceDemoSeeder
 *
 * Populates realistic compliance audit data to demonstrate:
 * - Audit Trail: 200+ events with varied types over 24h
 * - ISO 30414: Talent risk metrics, replacement costs, skill gaps
 * - Internal Audit Wizard: Critical roles with different signature states
 * - Credential Verification: Exportable VC/JSON-LD for roles
 *
 * Run: php artisan db:seed --class=ComplianceDemoSeeder
 */
class ComplianceDemoSeeder extends Seeder
{
    private Organization $org;

    private User $adminUser;

    private array $departments = [];

    private array $roles = [];

    private array $skills = [];

    private array $people = [];

    public function run(): void
    {
        // 1. Create organization and admin user
        $this->setupOrganization();

        // 2. Create departments
        $this->createDepartments();

        // 3. Create skills
        $this->createSkills();

        // 4. Create critical roles with varied signature states
        $this->createCriticalRoles();

        // 5. Create people and assign roles + skills
        $this->createPeopleAndAssignments();

        // 6. Generate audit trail events (200+)
        $this->generateAuditTrailEvents();

        // 7. Create verifiable credentials
        $this->createCredentials();

        $this->command->info('✅ ComplianceDemoSeeder completed successfully!');
        $this->command->info("📊 Organization: {$this->org->name}");
        $this->command->info('👥 People: '.count($this->people));
        $this->command->info('💼 Roles: '.count($this->roles));
        $this->command->info('📋 Skills: '.count($this->skills));
        $this->command->info('📝 Events: '.EventStore::where('organization_id', $this->org->id)->count());
    }

    private function setupOrganization(): void
    {
        $this->org = Organization::factory()->create([
            'name' => 'Stratos Demo Corporation',
            'slug' => 'stratos-demo-corp',
        ]);

        $this->adminUser = User::factory()->create([
            'organization_id' => $this->org->id,
            'name' => 'Admin Omar',
            'email' => 'admin@stratos-demo.local',
        ]);
    }

    private function createDepartments(): void
    {
        $deptNames = ['Engineering', 'Operations', 'RRHH', 'Ventas', 'Finance', 'Innovation'];

        foreach ($deptNames as $name) {
            $this->departments[$name] = Departments::factory()->create([
                'organization_id' => $this->org->id,
                'name' => $name,
            ]);
        }
    }

    private function createSkills(): void
    {
        $skillNames = [
            // Technical
            'System Architecture' => 'Technical',
            'Cloud Infrastructure' => 'Technical',
            'Data Engineering' => 'Technical',
            'API Development' => 'Technical',

            // Transversal (these will show gaps)
            'Liderazgo Ágil' => 'Leadership',
            'Comunicación Intercultural' => 'Soft',
            'Data-Driven Decision' => 'Analytics',
            'Pensamiento Crítico' => 'Soft',
            'Gestión del Cambio' => 'Leadership',

            // Domain
            'Compliance & Auditoria' => 'Governance',
            'Gestión de Riesgo' => 'Governance',
            'Inteligencia Artificial' => 'Technical',
        ];

        foreach ($skillNames as $name => $domain) {
            $this->skills[$name] = Skill::factory()->create([
                'organization_id' => $this->org->id,
                'name' => $name,
                'description' => "Skill: $name ($domain)",
            ]);
        }
    }

    private function createCriticalRoles(): void
    {
        // 24 critical roles with varied signature states
        $roleConfigs = [
            // Signed (current) - 12 roles
            ['VP Talento', 'RRHH', 'current', 45],
            ['CTO', 'Engineering', 'current', 120],
            ['Head Operations', 'Operations', 'current', 15],
            ['Chief Data Officer', 'Engineering', 'current', 90],
            ['Director Finance', 'Finance', 'current', 180],
            ['VP Innovación', 'Innovation', 'current', 60],
            ['Chief Compliance Officer', 'RRHH', 'current', 30],
            ['Head Sales', 'Ventas', 'current', 75],
            ['Engineering Manager', 'Engineering', 'current', 50],
            ['Operations Manager', 'Operations', 'current', 100],
            ['Finance Manager', 'Finance', 'current', 110],
            ['People Manager', 'RRHH', 'current', 85],

            // Expired - 6 roles
            ['CRO (Comercial)', 'Ventas', 'expired', -30],
            ['VP Tecnología', 'Engineering', 'expired', -60],
            ['Head Strategy', 'RRHH', 'expired', -15],
            ['Director Marketing', 'Ventas', 'expired', -45],
            ['Chief Architect', 'Engineering', 'expired', -90],
            ['Head Innovation', 'Innovation', 'expired', -20],

            // Missing - 6 roles
            ['Data Science Lead', 'Engineering', 'missing', null],
            ['Product Manager', 'Innovation', 'missing', null],
            ['Quality Manager', 'Operations', 'missing', null],
            ['Policy Officer', 'RRHH', 'missing', null],
            ['Systems Manager', 'Engineering', 'missing', null],
            ['Budget Manager', 'Finance', 'missing', null],
        ];

        foreach ($roleConfigs as [$roleName, $deptName, $sigStatus, $daysOffset]) {
            $signedAt = null;
            if ($sigStatus === 'current' || $sigStatus === 'expired') {
                $signedAt = now()->addDays($daysOffset);
            }

            $this->roles[$roleName] = Roles::factory()->create([
                'organization_id' => $this->org->id,
                'name' => $roleName,
                'department_id' => $this->departments[$deptName]->id,
                'digital_signature' => $sigStatus !== 'missing' ? 'sig_'.Str::random(16) : null,
                'signed_at' => $signedAt,
                'signature_version' => $sigStatus !== 'missing' ? 'v1.0' : null,
                'status' => 'active',
            ]);
        }
    }

    private function createPeopleAndAssignments(): void
    {
        // Create 89 people (for ~$48.2M replacement cost example)
        $firstNames = ['Omar', 'Ana', 'Carlos', 'María', 'Juan', 'Laura', 'Pedro', 'Elena', 'Luis', 'Rosa'];
        $lastNames = ['García', 'López', 'Martín', 'Pérez', 'Chen', 'Kim', 'Silva', 'Santos', 'Müller', 'Cohen'];

        $peopleCount = 0;
        foreach ($this->roles as $role) {
            // Assign 3-5 people per role
            $assignmentCount = rand(3, 5);

            for ($i = 0; $i < $assignmentCount; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];

                $person = People::factory()->create([
                    'organization_id' => $this->org->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => strtolower($firstName.'.'.$lastName.'@demo.local'),
                ]);

                $this->people[$person->id] = $person;

                // Assign role + skills
                // Every person gets the critical skills for their role
                $skillsToAssign = array_slice($this->skills, 0, rand(4, 7), true);
                foreach ($skillsToAssign as $skill) {
                    $currentLevel = rand(1, 4);
                    $requiredLevel = rand(2, 5);

                    PeopleRoleSkills::factory()->create([
                        'people_id' => $person->id,
                        'roles_id' => $role->id,
                        'skill_id' => $skill->id,
                        'current_level' => $currentLevel,
                        'required_level' => $requiredLevel,
                        'organization_id' => $this->org->id,
                    ]);
                }

                $peopleCount++;
            }
        }

        $this->command->info("👥 Created $peopleCount people");
    }

    private function generateAuditTrailEvents(): void
    {
        $eventTypes = [
            'user.created' => 'Evento: Usuario creado',
            'user.updated' => 'Evento: Usuario actualizado',
            'role.created' => 'Evento: Rol creado',
            'role.updated' => 'Evento: Rol actualizado',
            'role.deleted' => 'Evento: Rol eliminado',
            'skill.created' => 'Evento: Skill creada',
            'skill.updated' => 'Evento: Skill actualizada',
            'skill.deleted' => 'Evento: Skill eliminada',
            'people_role_skills.created' => 'Evento: Asignación de rol-skill',
            'people_role_skills.updated' => 'Evento: Actualización de rol-skill',
            'governance.approved' => 'Evento: Gobernanza aprobada',
            'signature.created' => 'Evento: Firma digital creada',
        ];

        $aggregateTypes = ['User', 'Role', 'Skill', 'People', 'Governance'];

        // Generate 200+ events spread over last 24h + historical
        $now = now();

        // 70% of events in last 24h
        for ($i = 0; $i < 140; $i++) {
            $hoursAgo = rand(0, 24);
            $minutesAgo = rand(0, 60);
            $occurredAt = $now->copy()->subHours($hoursAgo)->subMinutes($minutesAgo);

            $eventName = array_key_rand($eventTypes);
            $aggregateType = $aggregateTypes[array_rand($aggregateTypes)];
            $aggregateId = rand(1, 100);

            EventStore::create([
                'id' => (string) Str::uuid(),
                'event_name' => $eventName,
                'aggregate_type' => $aggregateType,
                'aggregate_id' => $aggregateId,
                'organization_id' => $this->org->id,
                'actor_id' => $this->adminUser->id,
                'payload' => [
                    'action' => $eventName,
                    'entity_id' => $aggregateId,
                    'entity_type' => $aggregateType,
                    'timestamp' => $occurredAt->toIso8601String(),
                    'metadata' => [
                        'ip' => '192.168.'.rand(1, 255).'.'.rand(1, 255),
                        'user_agent' => 'Mozilla/5.0 (Demo)',
                    ],
                ],
                'occurred_at' => $occurredAt,
            ]);
        }

        // 30% of events older than 24h
        for ($i = 0; $i < 60; $i++) {
            $daysAgo = rand(2, 30);
            $hoursAgo = rand(0, 24);
            $occurredAt = $now->copy()->subDays($daysAgo)->subHours($hoursAgo);

            $eventName = array_key_rand($eventTypes);
            $aggregateType = $aggregateTypes[array_rand($aggregateTypes)];
            $aggregateId = rand(1, 100);

            EventStore::create([
                'id' => (string) Str::uuid(),
                'event_name' => $eventName,
                'aggregate_type' => $aggregateType,
                'aggregate_id' => $aggregateId,
                'organization_id' => $this->org->id,
                'actor_id' => $this->adminUser->id,
                'payload' => [
                    'action' => $eventName,
                    'entity_id' => $aggregateId,
                    'entity_type' => $aggregateType,
                    'timestamp' => $occurredAt->toIso8601String(),
                ],
                'occurred_at' => $occurredAt,
            ]);
        }

        $totalEvents = EventStore::where('organization_id', $this->org->id)->count();
        $this->command->info("📝 Created $totalEvents audit trail events");
    }

    private function createCredentials(): void
    {
        // Create verifiable credentials for signed roles
        $signedRoles = Roles::where('organization_id', $this->org->id)
            ->whereNotNull('digital_signature')
            ->limit(5)
            ->get();

        foreach ($signedRoles as $role) {
            if ($role->signed_at) {
                VerifiableCredential::factory()->create([
                    'people_id' => $this->people[array_key_first($this->people)]->id ?? null,
                    'type' => ['VerifiableCredential', 'RoleCredential'],
                    'issuer_name' => 'Stratos AI',
                    'issuer_did' => 'did:web:stratos.local',
                    'credential_data' => [
                        '@context' => ['https://www.w3.org/2018/credentials/v1'],
                        'type' => ['VerifiableCredential', 'RoleCredential'],
                        'issuer' => ['id' => 'did:web:stratos.local', 'name' => 'Stratos AI'],
                        'issuanceDate' => $role->signed_at->toIso8601String(),
                        'credentialSubject' => [
                            'id' => 'did:web:stratos.local#'.$role->id,
                            'role' => [
                                'id' => $role->id,
                                'name' => $role->name,
                                'department' => $role->department_id,
                            ],
                        ],
                    ],
                    'cryptographic_signature' => $role->digital_signature,
                    'issued_at' => $role->signed_at,
                    'expires_at' => $role->signed_at->addYear(),
                    'status' => 'active',
                ]);
            }
        }

        $credentialCount = VerifiableCredential::where('organization_id', $this->org->id)->count() ?? 0;
        $this->command->info("🔐 Created $credentialCount verifiable credentials");
    }
}

// Helper function
function array_key_rand(array $array)
{
    return $array[array_rand($array)];
}
