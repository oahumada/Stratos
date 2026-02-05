<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\Scenario\RoleMutationService;

class RoleMutationServiceTest extends TestCase
{
    public function test_calculate_role_mutation_enrichment()
    {
        $svc = new RoleMutationService();
        $current = ['a', 'b', 'c'];
        $target = ['d', 'e', 'a', 'f']; // 75% new (d,e,f vs target 4 -> 3 new => 0.75)
        $this->assertEquals('enrichment', $svc->calculateRoleMutation($current, $target));
    }

    public function test_calculate_role_mutation_specialization()
    {
        $svc = new RoleMutationService();
        $current = ['a', 'b', 'c', 'd', 'e'];
        $target = ['a', 'b']; // targetCount 2 <= 0.8 * 5 = 4 => specialization
        $this->assertEquals('specialization', $svc->calculateRoleMutation($current, $target));
    }

    public function test_calculate_role_mutation_stable()
    {
        $svc = new RoleMutationService();
        $current = ['a', 'b', 'c'];
        $target = ['a', 'b', 'c', 'd']; // only 25% new
        $this->assertEquals('stable', $svc->calculateRoleMutation($current, $target));
    }

    public function test_suggest_archetype_specialist()
    {
        $svc = new RoleMutationService();
        $this->assertEquals('specialist', $svc->suggestArchetype(['skills_count' => 5, 'specialized_skills' => 4]));
    }

    public function test_suggest_archetype_generalist()
    {
        $svc = new RoleMutationService();
        $this->assertEquals('generalist', $svc->suggestArchetype(['skills_count' => 12, 'specialized_skills' => 3]));
    }

    public function test_suggest_archetype_hybrid()
    {
        $svc = new RoleMutationService();
        $this->assertEquals('hybrid', $svc->suggestArchetype(['skills_count' => 6, 'specialized_skills' => 2]));
    }
}
