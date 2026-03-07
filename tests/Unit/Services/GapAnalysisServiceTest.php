<?php

namespace Tests\Unit\Services;

use App\Models\Organization;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Models\Skill;
use App\Services\GapAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GapAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;

    protected GapAnalysisService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GapAnalysisService;
        $this->org = Organization::create([
            'name' => 'Test Org',
            'subdomain' => 'test',
        ]);
    }

    public function test_calculate_returns_correct_match_percentage_and_gaps()
    {
        // 1. Crear el Rol y las Skills
        $role = Roles::create([
            'organization_id' => $this->org->id,
            'name' => 'Senior Developer',
            'level' => 'senior',
        ]);

        $skill1 = Skill::create(['name' => 'PHP', 'organization_id' => $this->org->id]);
        $skill2 = Skill::create(['name' => 'Laravel', 'organization_id' => $this->org->id]);

        // 2. Asociar skills al rol con niveles requeridos
        $role->skills()->attach($skill1->id, ['required_level' => 4, 'is_critical' => true]);
        $role->skills()->attach($skill2->id, ['required_level' => 3, 'is_critical' => false]);

        // 3. Crear Persona
        $person = People::create([
            'organization_id' => $this->org->id,
            'first_name' => 'Elena',
            'last_name' => 'Nito',
            'email' => 'elena@test.com',
        ]);

        // 4. Asignar niveles actuales a la persona
        // Skill 1: Nivel 4 (GAP 0)
        PeopleRoleSkills::create([
            'people_id' => $person->id,
            'skill_id' => $skill1->id,
            'current_level' => 4,
            'is_active' => true,
        ]);
        // Skill 2: Nivel 1 (GAP 2)
        PeopleRoleSkills::create([
            'people_id' => $person->id,
            'skill_id' => $skill2->id,
            'current_level' => 1,
            'is_active' => true,
        ]);

        // 5. Ejecutar análisis
        $result = $this->service->calculate($person, $role);

        // 6. Validaciones
        // match_percentage: (1 skill ok de 2) = 50%
        $this->assertEquals(50, $result['match_percentage']);
        $this->assertEquals('Gap significativo', $result['summary']['category']);
        $this->assertCount(2, $result['gaps']);

        // Validar gaps específicos
        $gap1 = collect($result['gaps'])->firstWhere('skill_id', $skill1->id);
        $this->assertEquals(0, $gap1['gap']);
        $this->assertEquals('ok', $gap1['status']);
        $this->assertTrue($gap1['is_critical']);

        $gap2 = collect($result['gaps'])->firstWhere('skill_id', $skill2->id);
        $this->assertEquals(2, $gap2['gap']);
        $this->assertEquals('critical', $gap2['status']); // Gap > 1 es critical según el código
        $this->assertFalse($gap2['is_critical']);
    }

    public function test_calculate_returns_ready_category_for_high_match()
    {
        $role = Roles::create(['organization_id' => $this->org->id, 'name' => 'Lead', 'level' => 'lead']);
        $skill = Skill::create(['name' => 'Leadership', 'organization_id' => $this->org->id]);
        $role->skills()->attach($skill->id, ['required_level' => 3]);

        $person = People::create(['organization_id' => $this->org->id, 'first_name' => 'Jim', 'last_name' => 'Mena', 'email' => 'jim@test.com']);
        PeopleRoleSkills::create(['people_id' => $person->id, 'skill_id' => $skill->id, 'current_level' => 3, 'is_active' => true]);

        $result = $this->service->calculate($person, $role);

        $this->assertEquals(100, $result['match_percentage']);
        $this->assertEquals('ready', $result['summary']['category']);
    }

    public function test_calculate_handles_missing_skills_as_zero_level()
    {
        $role = Roles::create(['organization_id' => $this->org->id, 'name' => 'Architect', 'level' => 'senior']);
        $skill = Skill::create(['name' => 'Kubernetes', 'organization_id' => $this->org->id]);
        $role->skills()->attach($skill->id, ['required_level' => 5]);

        $person = People::create(['organization_id' => $this->org->id, 'first_name' => 'No', 'last_name' => 'Skills', 'email' => 'noskills@test.com']);

        $result = $this->service->calculate($person, $role);

        $this->assertEquals(0, $result['match_percentage']);
        $gap = $result['gaps'][0];
        $this->assertEquals(5, $gap['gap']);
        $this->assertEquals(0, $gap['current_level']);
    }
}
